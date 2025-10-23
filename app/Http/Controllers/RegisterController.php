<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Spouse;
use App\Models\AdultChild;
use App\Models\Child;
use App\Models\Otp;
use App\Jobs\send_sms;
use App\DataTables\ApplicantDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Jobs\create_payment_in_crm;
use App\Jobs\translate;
use Illuminate\Support\Facades\Gate;

class RegisterController extends Controller
{
    public function index(ApplicantDataTable $dataTable)
    {
        return $dataTable->render('applicants');
    }

    public function otp_request(Request $req) {
        $data = $req->validate([
            'phone' => ['required', 'regex:/^09\d{9}$/']
        ]);

        $otp = Otp::firstOrCreate([
            'mobile' => request()->phone
        ]);

        if ($otp->expires_at && $otp->expires_at > now()) {
            return response()->json([
                'success' => true,
                'message' => 'به تازگی کد ارسال شده است. لطفاً کمی صبر کنید.'
            ]);
        }

        $code = (string) random_int(1000, 9999);

        $otp->update([
            'code' => $code,
            'expires_at' => now()->addMinutes(2)
        ]);

        send_sms::dispatch(
            null,
            __('message.otp_sms_text', [
                'code' => $code,
                'url' => parse_url(url('/'), PHP_URL_HOST)
            ]),
            'otp',
            request()->phone
        );

        return response()->json([
            'success' => true
        ]);
    }

    public function otp_verify(Request $req) {
        $data = $req->validate([
            'phone' => ['required', 'regex:/^09\d{9}$/'],
            'code'  => ['required', 'digits:4'],
        ]);
        
        $otp = Otp::where('mobile', request()->phone)
            ->where('code', request()->code)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otp) {
            return response()->json([
                'success' => false,
                'message' => 'کد معتبر نیست یا منقضی شده است.'
            ]);
        }

        Session::forget('applicant_id');
        Session::put('verified_mobile', request()->phone);

        return response()->json([
            'success' => true,
            'redirect' => route('step_zero')
        ]);
    }

    public function step_zero()
    {
        if (auth()->user() && request()->mobile) {
            $validator = \Validator::make(request()->all(), [
                'mobile' => ['required', 'regex:/^09\d{9}$/']
            ]);

            if ($validator->fails()) {
                return $validator->errors()->first();
            }

            Session::put('verified_mobile', request()->mobile);

            $applicant = Applicant::where('mobile', request()->mobile)->first();

            Session::forget('applicant_id');
            if ($applicant) {
                Session::put('applicant_id', $applicant->id);
            }

            $steps = $this->steps($applicant);

            return redirect($steps['last_true_step']);
        } elseif (!auth()->user() && session('verified_mobile')) {
            $applicant = Applicant::where('mobile', session('verified_mobile'))->first();

            Session::forget('applicant_id');
            if ($applicant) {
                Session::put('applicant_id', $applicant->id);
            }

            $steps = $this->steps($applicant);

            return redirect($steps['last_true_step']);
        } else {
            return view('step_otp', [
                'applicant' => new Applicant,
                'steps' => $this->steps()
            ]);
        }
    }

    public function step_one($applicant_id = null)
    {
        if ($applicant_id) {
            Gate::authorize('view_steps', $applicant_id);

            $applicant = Applicant::find($applicant_id);
        } elseif (session('verified_mobile')) {
            $applicant = Applicant::where('mobile', session('verified_mobile'))->first();
        } else {
            return redirect(route('step_zero'));
        }

        $steps = $this->steps($applicant);
        if (!$steps['step_one']) {
            return redirect($steps['last_true_step']);
        }

        return view('step_one', [
            'applicant' => $applicant,
            'steps' => $steps
        ]);
    }

    public function step_one_post()
    {
        if (request()->applicant_id) {
            Gate::authorize('view_steps', request()->applicant_id);
        }

        request()->merge([
            'user_id' => auth()->user() ? auth()->user()->id : 0,
            'registration_type' => auth()->user() ? (auth()->user()->role == 'agent' ? 'agent' : 'onsite') : 'online'
        ]);

        if (session('verified_mobile')) {
            request()->merge([
                'mobile' => session('verified_mobile')
            ]);
        }

        $rules = [
            'applicant_id' => ['nullable', 'exists:applicants,id,payment_status,unpaid'],
            'mobile' => ['required', 'regex:/^09\d{9}$/'],
            'marital' => ['required', 'in:single,married'],
            'children_count' => ['required', 'numeric', 'max:6'],
            'adult_children_count' => ['required', 'numeric', 'max:6'],
            'double_register' => ['nullable'],
            'registration_type' => ['nullable', 'in:online,onsite,agent'],
        ];

        if (request()->registration_type == 'agent') {
            $rules['user_id'] = ['required', 'exists:users,id,role,agent'];
        }

        $attributes = request()->validate($rules);

        $attributes['double_register'] = $attributes['marital'] == 'married' && isset($attributes['double_register']) ? 1 : 0;
        $attributes['price'] = 0;

        $applicant = Applicant::firstOrCreate([
            'mobile' => $attributes['mobile']
        ]);

        Session::put('applicant_id', $applicant->id);

        if ($applicant->registration_type == 'agent' && $applicant->user_id != request()->user_id) {
            return Redirect::back()->with('error', __('message.not_have_access_to_edit'));
        }
        unset($attributes['applicant_id']);
        $applicant->update($attributes);

        if ($applicant) {
            $spouse_id = null;

            if (($attributes['marital'] == 'married' && !$applicant->spouse) || ($attributes['marital'] == 'single' && $applicant->spouse)) {
                Spouse::where('applicant_id', $applicant->id)->delete();
                if ($attributes['marital'] == 'married') {
                    $spouse_id = Spouse::create([
                        'applicant_id' => $applicant->id,
                        'double_register' => $attributes['double_register'],
                    ])->id;
                }
            } elseif ($applicant->spouse) {
                $spouse_id = $applicant->spouse->id;
                $applicant->spouse->double_register = $attributes['double_register'];
                $applicant->spouse->save();
            }

            $count_adult_children = count($applicant->adult_children);

            if ($count_adult_children != $attributes['adult_children_count']) {
                if ($count_adult_children) {
                    AdultChild::where('applicant_id', $applicant->id)->delete();
                }

                if ($attributes['adult_children_count']) {
                    $create_adult_children = [];
                    for ($i=0; $i < $attributes['adult_children_count']; $i++) { 
                        $create_adult_children[] = new AdultChild([
                            'applicant_id' => $applicant->id,
                            'spouse_id' => $spouse_id
                        ]);
                    }
                    $applicant->adult_children()->saveMany($create_adult_children);
                }
            }

            $count_children = count($applicant->children);

            if ($count_children != $attributes['children_count']) {
                if ($count_children) {
                    Child::where('applicant_id', $applicant->id)->delete();
                }

                if ($attributes['children_count']) {
                    $create_children = [];
                    for ($i=0; $i < $attributes['children_count']; $i++) { 
                        $create_children[] = new Child([
                            'applicant_id' => $applicant->id,
                            'spouse_id' => $spouse_id
                        ]);
                    }
                    $applicant->children()->saveMany($create_children);
                }
            }
        }

        return redirect('step_two/'.$applicant->id);
    }

    public function step_two($applicant_id = null)
    {
        Gate::authorize('view_steps', $applicant_id);

        if ($applicant_id) {
            $applicant = Applicant::find($applicant_id);
            $steps = $this->steps($applicant);
            if (!$steps['step_two']) {
                return redirect($steps['last_true_step']);
            }
            return view('step_two', [
                'applicant' => $applicant,
                'steps' => $steps
            ]);
        } else {
            return redirect('step_one');
        }
    }

    public function step_two_post()
    {
        if (request()->applicant_id) {
            Gate::authorize('view_steps', request()->applicant_id);

            $applicant = Applicant::find(request()->applicant_id);

            if ($applicant && $applicant->registration_type == 'agent' && $applicant->user_id != (auth()->user() ? auth()->user()->id : 0)) {
                return Redirect::back()->with('error', __('message.not_have_access_to_edit'));
            }

            $attributes = request()->validate([
                'acquisition_channel' => ['required', 'in:social_media,friend_family,advertisement,search_engine,event,other'],
                'email' => ['required', 'email:rfc,dns'],
                'education_degree' => ['required', 'in:primary_school_only,high_school_no_degree,high_school_degree,vocational_school,some_university_courses,university_degree,some_graduate_level_courses,masters_degree,doctorate_level_courses,doctorate_degree'],
                'marital_status' => ['required', $applicant && $applicant->marital == 'single' ? 'in:unmarried,divorced,widowed' : 'in:married_us_citizen,married_not_us_citizen'],
                'residence_country' => ['required'],
                'residence_state' => ['required'],
                'residence_city' => ['required'],
                'residence_street' => ['required'],
                'residence_alley' => ['required'],
                'residence_no' => ['required'],
                'residence_unit' => ['required'],
                'residence_postal_code' => ['required'],
            ]);

            if ($applicant->payment_status == 'unpaid') {
                $attributes['price'] = 0;
            }

            $applicant->update($attributes);

            return redirect('step_three/'.$applicant->id);
        } else {
            return redirect('step_one');
        }
    }

    public function step_three($applicant_id = null)
    {
        if ($applicant_id) {
            Gate::authorize('view_steps', $applicant_id);

            $applicant = Applicant::with([
                'adult_children' => function ($q) {
                    return $q->orderBy('id', 'asc');
                },
                'children' => function ($q) {
                    return $q->orderBy('id', 'asc');
                }
            ])->find($applicant_id);

            $steps = $this->steps($applicant);
            if (!$steps['step_three']) {
                return redirect($steps['last_true_step']);
            }
            
            return view('step_three', [
                'applicant' => $applicant,
                'steps' => $steps
            ]);
        } else {
            return redirect('step_one');
        }
    }

    public function step_four($applicant_id = null)
    {
        if ($applicant_id) {
            Gate::authorize('view_steps', $applicant_id);

            $applicant = Applicant::with([
                'adult_children' => function ($q) {
                    return $q->orderBy('id', 'asc');
                },
                'children' => function ($q) {
                    return $q->orderBy('id', 'asc');
                }
            ])->find($applicant_id);
            
            request()->merge([
                'applicant_passport' => $applicant->passport_image,
                'applicant_face' => $applicant->face_image
            ]);

            $rules = [
                'applicant_passport' => ['required'],
                'applicant_face' => ['required']
            ];

            if ($applicant->spouse) {
                request()->merge([
                    'spouse_passport' => $applicant->spouse->passport_image,
                    'spouse_face' => $applicant->spouse->face_image,
                ]);

                $rules['spouse_passport'] = ['required'];
                $rules['spouse_face'] = ['required'];
            }

            if ($applicant->adult_children_count) {
                $rules['adult_child_passport.*'] = ['required'];
                $rules['adult_child_face.*'] = ['required'];
                $adult_child_face = [];
                foreach($applicant->adult_children as $index => $adult_child) {
                    $adult_child_passport[] = $adult_child->passport_image;
                    $adult_child_face[] = $adult_child->face_image;
                }

                request()->merge([
                    'adult_child_passport' => $adult_child_passport,
                    'adult_child_face' => $adult_child_face,
                ]);
            }

            if ($applicant->children_count) {
                $rules['child_face.*'] = ['required'];
                $child_face = [];

                foreach($applicant->children as $index => $child) {
                    $child_face[] = $child->face_image;
                }

                request()->merge([
                    'child_face' => $child_face,
                ]);
            }

            $attributes = request()->validate($rules);

            $steps = $this->steps($applicant);
            if (!$steps['step_four']) {
                return redirect($steps['last_true_step']);
            }

            return view('step_four', [
                'applicant' => $applicant,
                'steps' => $steps
            ]);
        } else {
            return redirect('step_one');
        }
    }

    public function step_four_post()
    {
        if (request()->applicant_id) {
            Gate::authorize('view_steps', request()->applicant_id);

            $applicant = Applicant::find(request()->applicant_id);

            if ($applicant && $applicant->registration_type == 'agent' && $applicant->user_id != (auth()->user() ? auth()->user()->id : 0)) {
                return Redirect::back()->with('error', __('message.not_have_access_to_edit'));
            }

            request()->merge([
                'applicant_birth_date' => request()->applicant_birth_year.'-'.request()->applicant_birth_month.'-'.request()->applicant_birth_day
            ]);

            $rules = [
                'applicant_name' => ['required', 'regex:/^[ a-z]+$/i'],
                'applicant_last_name' => ['required', 'regex:/^[ a-z]+$/i'],
                'applicant_gender' => ['required', 'in:male,female'],
                'applicant_birth_country' => ['required'],
                'applicant_birth_city' => ['required'],
                'applicant_birth_date' => ['required', 'regex:/^1\d\d\d\-(0\d|1[0-2])\-([0-2]\d|3[0-1])$/'],
            ];

            if ($applicant->spouse) {
                request()->merge([
                    'spouse_birth_date' => request()->spouse_birth_year.'-'.request()->spouse_birth_month.'-'.request()->spouse_birth_day
                ]);

                $rules['spouse_name'] = ['required', 'regex:/^[ a-z]+$/i'];
                $rules['spouse_last_name'] = ['required', 'regex:/^[ a-z]+$/i'];
                $rules['spouse_gender'] = ['required', 'in:male,female'];
                $rules['spouse_birth_country'] = ['required'];
                $rules['spouse_birth_city'] = ['required'];
                $rules['spouse_birth_date'] = ['required', 'regex:/^1\d\d\d\-(0\d|1[0-2])\-([0-2]\d|3[0-1])$/'];
                if ($applicant->double_register) {
                    $rules['spouse_mobile'] = ['required', 'regex:/^09\d{9}$/'];
                    $rules['spouse_email'] = ['required', 'email:rfc,dns'];
                    $rules['spouse_education_degree'] = ['required', 'in:primary_school_only,high_school_no_degree,high_school_degree,vocational_school,some_university_courses,university_degree,some_graduate_level_courses,masters_degree,doctorate_level_courses,doctorate_degree'];
                }
            }

            if (count($applicant->adult_children)) {
                foreach ($applicant->adult_children as $adult_child) {
                    request()->merge([
                        'adult_child_birth_date_'.$adult_child->id => request()->{'adult_child_birth_year_'.$adult_child->id}.'-'.request()->{'adult_child_birth_month_'.$adult_child->id}.'-'.request()->{'adult_child_birth_day_'.$adult_child->id}
                    ]);

                    $rules['adult_child_name_'.$adult_child->id] = ['required', 'regex:/^[ a-z]+$/i'];
                    $rules['adult_child_last_name_'.$adult_child->id] = ['required', 'regex:/^[ a-z]+$/i'];
                    $rules['adult_child_gender_'.$adult_child->id] = ['required', 'in:male,female'];
                    $rules['adult_child_birth_country_'.$adult_child->id] = ['required'];
                    $rules['adult_child_birth_city_'.$adult_child->id] = ['required'];
                    $rules['adult_child_birth_date_'.$adult_child->id] = ['required', 'regex:/^1\d\d\d\-(0\d|1[0-2])\-([0-2]\d|3[0-1])$/'];
                    if (($applicant->payment_status == 'paid' && $adult_child->independent_register) || ($applicant->payment_status == 'unpaid' && request()->{'independent_register_'.$adult_child->id})) {
                        $rules['adult_child_mobile_'.$adult_child->id] = ['required', 'regex:/^09\d{9}$/'];
                        $rules['adult_child_email_'.$adult_child->id] = ['required', 'email:rfc,dns'];
                        $rules['adult_child_education_degree_'.$adult_child->id] = ['required', 'in:primary_school_only,high_school_no_degree,high_school_degree,vocational_school,some_university_courses,university_degree,some_graduate_level_courses,masters_degree,doctorate_level_courses,doctorate_degree'];
                    }
                }
            }

            if (count($applicant->children)) {
                foreach ($applicant->children as $child) {
                    request()->merge([
                        'child_birth_date_'.$child->id => request()->{'child_birth_year_'.$child->id}.'-'.request()->{'child_birth_month_'.$child->id}.'-'.request()->{'child_birth_day_'.$child->id}
                    ]);

                    $rules['child_name_'.$child->id] = ['required', 'regex:/^[ a-z]+$/i'];
                    $rules['child_last_name_'.$child->id] = ['required', 'regex:/^[ a-z]+$/i'];
                    $rules['child_gender_'.$child->id] = ['required', 'in:male,female'];
                    $rules['child_birth_country_'.$child->id] = ['required'];
                    $rules['child_birth_city_'.$child->id] = ['required'];
                    $rules['child_birth_date_'.$child->id] = ['required', 'regex:/^1\d\d\d\-(0\d|1[0-2])\-([0-2]\d|3[0-1])$/'];
                }
            }

            $attributes = request()->validate($rules);

            $applicant->name = request()->applicant_name;
            $applicant->last_name = request()->applicant_last_name;
            $applicant->gender = request()->applicant_gender;
            $applicant->birth_country = request()->applicant_birth_country;
            $applicant->birth_city = request()->applicant_birth_city;
            $applicant->birth_date_fa = request()->applicant_birth_date;
            $applicant->save();

            if ($applicant->spouse) {
                $applicant->spouse->name = request()->spouse_name;
                $applicant->spouse->last_name = request()->spouse_last_name;
                $applicant->spouse->gender = request()->spouse_gender;
                $applicant->spouse->birth_country = request()->spouse_birth_country;
                $applicant->spouse->birth_city = request()->spouse_birth_city;
                $applicant->spouse->birth_date_fa = request()->spouse_birth_date;
                if ($applicant->double_register) {
                    $applicant->spouse->double_register = 1;
                    $applicant->spouse->mobile = request()->spouse_mobile;
                    $applicant->spouse->email = request()->spouse_email;
                    $applicant->spouse->education_degree = request()->spouse_education_degree;
                }
                $applicant->spouse->save();
            }

            $helper_price = \Helper::price()[$applicant->registration_type];
            $independent_register_price = 0;

            if (count($applicant->adult_children)) {
                foreach ($applicant->adult_children as $adult_child) {
                    $adult_child->name = $attributes['adult_child_name_'.$adult_child->id];
                    $adult_child->last_name = $attributes['adult_child_last_name_'.$adult_child->id];
                    $adult_child->gender = $attributes['adult_child_gender_'.$adult_child->id];
                    $adult_child->birth_country = $attributes['adult_child_birth_country_'.$adult_child->id];
                    $adult_child->birth_city = $attributes['adult_child_birth_city_'.$adult_child->id];
                    $adult_child->birth_date_fa = $attributes['adult_child_birth_date_'.$adult_child->id];
                    if (($applicant->payment_status == 'paid' && $adult_child->independent_register) || ($applicant->payment_status == 'unpaid' && request()->{'independent_register_'.$adult_child->id})) {
                        $adult_child->independent_register = 1;
                        $adult_child->mobile = $attributes['adult_child_mobile_'.$adult_child->id];
                        $adult_child->email = $attributes['adult_child_email_'.$adult_child->id];
                        $adult_child->education_degree = $attributes['adult_child_education_degree_'.$adult_child->id];
                        $independent_register_price += $helper_price['independent_register'];
                    } else {
                        $adult_child->independent_register = 0;
                    }
                    $adult_child->save();
                }
            }

            if (count($applicant->children)) {
                foreach ($applicant->children as $child) {
                    $child->name = $attributes['child_name_'.$child->id];
                    $child->last_name = $attributes['child_last_name_'.$child->id];
                    $child->gender = $attributes['child_gender_'.$child->id];
                    $child->birth_country = $attributes['child_birth_country_'.$child->id];
                    $child->birth_city = $attributes['child_birth_city_'.$child->id];
                    $child->birth_date_fa = $attributes['child_birth_date_'.$child->id];
                    $child->save();
                }
            }

            $marital_price = 0;

            if ($applicant->marital == 'single') {
                $marital_price += $helper_price['single'];
            }

            $children_price = ($applicant->adult_children_count * $helper_price['adult_child']) + ($applicant->children_count * $helper_price['child']) ;

            $double_register_price = 0;

            if ($applicant->marital == 'married') {
                $marital_price += $helper_price['married'];
                if ($applicant->double_register) {
                    $double_register_price = $helper_price['double_register'] + $children_price;
                }
            }

            $applicant->price = $marital_price + $children_price + $double_register_price + $independent_register_price;
            $applicant->save();

            if ($applicant->payment_status == 'paid') {
                return redirect('step_six/'.$applicant->id);
            }

            $applicant->refresh();

            return view('step_five', [
                'applicant' => $applicant,
                'steps' => $this->steps($applicant),
                'prices' => [
                    'marital' => $marital_price,
                    'children' => $children_price,
                    'double_register' => $double_register_price,
                    'independent_register' => $independent_register_price,
                ]
            ]);
        } else {
            return redirect('step_one');
        }
    }

    public function step_six()
    {
        if (request()->applicant_id) {
            Gate::authorize('view_steps', request()->applicant_id);

            $applicant = Applicant::find(request()->applicant_id);

            $steps = $this->steps($applicant);
            if (!$steps['step_six']) {
                return redirect($steps['last_true_step']);
            }

            return view('step_six', [
                'applicant' => $applicant,
                'steps' => $steps
            ]);
        } else {
            return redirect('step_one');
        }
    }

    public function step_image($applicant_id = null)
    {
        if ($applicant_id) {
            Gate::authorize('view_steps', $applicant_id);

            $applicant = Applicant::find($applicant_id);

            $steps = $this->steps($applicant);
            if (!$steps['step_image']) {
                return redirect($steps['last_true_step']);
            }

            return view('step_image', [
                'applicant' => $applicant,
                'steps' => $steps
            ]);
        } else {
            return redirect('step_one');
        }
    }

    public function payment($applicant_id = null)
    {
        if ($applicant_id) {
            Gate::authorize('view_steps', $applicant_id);

            $applicant = Applicant::find($applicant_id);
            if ($applicant->payment_status == 'unpaid') {
                if ($applicant->price) {
                    if ($applicant->nilgam_pay_ref) {
                        $nilgam_pay_res = Http::withoutVerifying()->timeout(10)->post('https://payment.nilgam.ir/delete',[
                            'payment_id' => $applicant->nilgam_pay_ref,
                            'product_ref_num' => env('NILGAM_PAY_CONTRACT_PREFIX').$applicant->id
                        ]);

                        $respose = $nilgam_pay_res->object();

                        if (!$respose || !$respose->success) {
                            Session::flash('error', __('message.errors_occured'));
                            return redirect('step_one');
                        }

                        $applicant->nilgam_pay_ref = null;
                        $applicant->save();
                    }

                    $nilgam_pay_res = Http::withoutVerifying()->timeout(10)->post('https://payment.nilgam.ir/create',[
                        'contract_number' => env('NILGAM_PAY_CONTRACT_PREFIX').$applicant->id,
                        'full_name_1' => $applicant->name.' '.$applicant->last_name,
                        'full_name_2' => __('message.site_name'),
                        'mobile' => $applicant->mobile,
                        'price' => $applicant->price,
                        'currency' => 'IRR',
                        'product_ref_num' => env('NILGAM_PAY_CONTRACT_PREFIX').$applicant->id,
                        'service_name' => __('message.site_name'),
                        'gateway_type' => $applicant->registration_type == 'onsite' ? 'kiosk' : 'bank',
                        'product' => 'lottery'
                    ]);

                    $respose = $nilgam_pay_res->object();

                    if ($respose && $respose->success) {
                        $applicant->nilgam_pay_ref = $respose->payment_id;
                        $applicant->save();
                        return redirect(str_replace('online', 'go_to_shaparak', $respose->link));
                    } else {
                        Session::flash('error', __('message.errors_occured'));
                    }
                    
                } else {
                    Session::flash('error', __('message.errors_occured'));
                }
            } else {
                return redirect('step_four');
            }
        }

        return redirect('step_one');
    }

    public function upload_file(Request $request)
    {
        $data = array();
        if (request()->applicant_id) {
            Gate::authorize('view_steps', request()->applicant_id);

            $validator = \Validator::make($request->all(), [
                'file' => 'required|mimes:png,jpg,jpeg|max:5120',
                'type' => ['required', 'in:passport,face'],
                'person' => ['required', 'in:applicants,spouses,adult_children,children'],
                'id' => [$request->person == 'applicants' ? 'nullable' : 'required', $request->person == 'applicants' ? '' : 'exists:'.$request->person.',id,applicant_id,'.request()->applicant_id]
            ]);

            if ($validator->fails()) {
                $data['success'] = false;
                $data['error'] = $validator->errors()->first();
            }else{
                $applicant = Applicant::find(request()->applicant_id);

                if ($applicant && $applicant->registration_type == 'agent' && $applicant->user_id != (auth()->user() ? auth()->user()->id : 0)) {
                    $data['success'] = false;
                    $data['error'] = __('message.not_have_access_to_edit');
                } else {
                    if($request->file('file')) {
                        $file = $request->file('file');
                        $chunks = explode(' ', microtime());
                        $filename = sprintf('%d%d%d', $chunks[1], $chunks[0] * 10000, rand(10000,99999)).'.'.$file->extension();
                        $location = 'uploads';
                        $file->move($location,$filename);

                        $update_data = [
                            $request->type.'_image' => $filename,
                            $request->type.'_image_status' => 'not_selected'
                        ];

                        if ($request->type == 'passport') {
                            $gooole_vision = $this->gooole_vision($filename);

                            if ($gooole_vision['success']) {
                                $update_data = array_merge($update_data, $gooole_vision['data']);
                            }
                        }
                        
                        if ($request->person == 'applicants') {
                            $applicant = Applicant::where('id', request()->applicant_id)->first();
                            if ($request->type == 'face' && ($applicant->face_image_status == 'accepted' || $applicant->face_image_status == 'cropped')) {
                                return response()->json([
                                    'success' => false,
                                    'error' => __('message.image_accepted_cant_edit')
                                ]);
                            } else {
                                $applicant->update($update_data);
                                $applicant->save();
                            }
                        }

                        if ($request->person == 'spouses') {
                            $spouse = Spouse::where('applicant_id', request()->applicant_id)->where('id', $request->id)->first();
                            if ($request->type == 'face' && ($spouse->face_image_status == 'accepted' || $spouse->face_image_status == 'cropped')) {
                                return response()->json([
                                    'success' => false,
                                    'error' => __('message.image_accepted_cant_edit')
                                ]);
                            } else {
                                $spouse->update($update_data);
                                $spouse->save();
                            }
                        }

                        if ($request->person == 'adult_children') {
                            $adult_child = AdultChild::where('applicant_id', request()->applicant_id)->where('id', $request->id)->first();
                            if ($request->type == 'face' && ($adult_child->face_image_status == 'accepted' || $adult_child->face_image_status == 'cropped')) {
                                return response()->json([
                                    'success' => false,
                                    'error' => __('message.image_accepted_cant_edit')
                                ]);
                            } else {
                                $adult_child->update($update_data);
                                $adult_child->save();
                            }
                        }

                        if ($request->person == 'children') {
                            $child = Child::where('applicant_id', request()->applicant_id)->where('id', $request->id)->first();
                            if ($request->type == 'face' && ($child->face_image_status == 'accepted' || $child->face_image_status == 'cropped')) {
                                return response()->json([
                                    'success' => false,
                                    'error' => __('message.image_accepted_cant_edit')
                                ]);
                            } else {
                                $child->update($update_data);
                                $child->save();
                            }
                        }

                        $data['success'] = true;
                        $data['file_name'] = $filename;
                        $data['message'] = 'Uploaded Successfully!';
                    }else{
                        $data['success'] = false;
                        $data['error'] = __('message.errors_occured');
                    }
                }
            }
        } else {
            $data['success'] = false;
            $data['error'] = __('message.mobile_not_set');
        }

        return response()->json($data);
    }

    public function delete_file(Request $request)
    {
        $data = array();
        if (request()->applicant_id) {
            Gate::authorize('view_steps', request()->applicant_id);

            $validator = \Validator::make($request->all(), [
                'type' => ['required', 'in:passport,face'],
                'person' => ['required', 'in:applicants,spouses,adult_children,children'],
                'id' => [$request->person == 'applicants' ? 'nullable' : 'required', $request->person == 'applicants' ? '' : 'exists:'.$request->person.',id,applicant_id,'.request()->applicant_id]
            ]);

            if ($validator->fails()) {
                $data['success'] = false;
                $data['error'] = $validator->errors()->first();// Error response
            }else{
                $applicant = Applicant::find(request()->applicant_id);
                if ($applicant && $applicant->registration_type == 'agent' && $applicant->user_id != (auth()->user() ? auth()->user()->id : 0)) {
                    $data['success'] = false;
                    $data['error'] = __('message.not_have_access_to_edit');
                } else {
                    if($applicant->payment_status == 'unpaid') {
                        if ($request->person == 'applicants') {
                            Applicant::where('id', request()->applicant_id)->update([
                                $request->type.'_image' => null
                            ]);
                        }

                        if ($request->person == 'spouses') {
                            Spouse::where('applicant_id', request()->applicant_id)->where('id', $request->id)->update([
                                $request->type.'_image' => null
                            ]);
                        }

                        if ($request->person == 'adult_children') {
                            AdultChild::where('applicant_id', request()->applicant_id)->where('id', $request->id)->update([
                                $request->type.'_image' => null
                            ]);
                        }

                        if ($request->person == 'children') {
                            Child::where('applicant_id', request()->applicant_id)->where('id', $request->id)->update([
                                $request->type.'_image' => null
                            ]);
                        }

                        $data['success'] = true;
                        $data['message'] = 'Deleted Successfully!';
                    } else {
                        $data['success'] = false;
                        $data['error'] = __('message.errors_occured');
                    }
                }
            }
        } else {
            $data['success'] = false;
            $data['error'] = __('message.mobile_not_set');
        }

        return response()->json($data);
    }

    public function gooole_vision($filename)
    {
        try {
            $response = Http::withoutVerifying()->timeout(7)->post('https://vision.googleapis.com/v1/images:annotate?key=AIzaSyB8N9RRMbsYGk0cWr4ujQli28Nd3wECh5Y', [
                'parent' => '',
                'requests' => [
                    [
                        'image' => [
                            'source' => [
                                'imageUri' => url('uploads/'.$filename)
                            ]
                        ],
                        'features' => [
                            [
                                'type' => 'DOCUMENT_TEXT_DETECTION'
                            ]
                        ]
                    ]
                ]
            ]);

            $data_array = explode("\n", $response->object()->responses[0]->textAnnotations[0]->description);

            $passport_data = [
                'name' => null,
                'last_name' => null,
                'passport_number' => null,
                'gender' => 'not_selected',
                'birth_date_fa' => null,
                'birth_date_en' => null,
                'birth_city' => null,
            ];

            foreach ($data_array as $text) {
                if (preg_match('/^(?:surname)[\s:]+(\w+)/i', trim($text), $matches)) {
                    $passport_data['last_name'] = isset($matches[1]) ? $matches[1] : null;
                } elseif (preg_match('/^(?:name)[\s:]+(\w+)/i', trim($text), $matches)) {
                    $passport_data['name'] = isset($matches[1]) ? $matches[1] : null;
                } elseif (preg_match('/^(?:passport no)[\s:]+(\w+)/i', trim($text), $matches)) {
                    $passport_data['passport_number'] = isset($matches[1]) ? $matches[1] : null;
                } elseif (preg_match('/^(?:sex)[\s:]+(\w+)/i', trim($text), $matches)) {
                    $passport_data['gender'] = isset($matches[1]) ? (strtoupper($matches[1]) == 'M' ? 'male' : (strtoupper($matches[1]) == 'F' ? 'female' : null)) : null;
                } elseif (preg_match('/^Date & Place of Birth\s*:?\s*(\d{2}\/\d{2}\/\d{4})\s*[-\s]+\s*(.+)/i', trim($text), $matches)) {
                    if (isset($matches[1])) {
                        $birth_date = explode('/', $matches[1]);
                        $passport_data['birth_date_en'] = sprintf('%s-%s-%s', $birth_date[2], $birth_date[1], $birth_date[0]);
                        $passport_data['birth_date_fa'] = \Helper::gregorian_to_jalali($birth_date[2], $birth_date[1], $birth_date[0], '-');
                    }
                    
                    $passport_data['birth_city'] = isset($matches[2]) ? $matches[2] : null;
                } elseif (preg_match('/^Date of Expiry[\s:]+(\d{2}\/\d{2}\/\d{4})/i', trim($text), $matches)) {
                    if (isset($matches[1])) {
                        $expire_date = explode('/', $matches[1]);
                        $passport_data['expire_date'] = sprintf('%s-%s-%s', $expire_date[2], $expire_date[1], $expire_date[0]);
                    }
                }
            }

            return [
                'success' => true,
                'data' => $passport_data
            ];
        } catch(\Exception $e) {
            return [
                'success' => false,
                'error' => $e
            ];
        } catch(\Error $e) {
            return [
                'success' => false,
                'error' => $e
            ];
        }
    }

    public function nilgam_pay_callback()
    {
        request()->request->add(['product_ref_num' => str_replace(env('NILGAM_PAY_CONTRACT_PREFIX'), '', request()->product_ref_num)]);
        $validator = \Validator::make(request()->all(), [
            'payment_id' => ['required', 'exists:applicants,nilgam_pay_ref'],
            'product_ref_num' => ['required', 'exists:applicants,id']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('message.fail')
            ]);
        }

        $applicant = Applicant::where('id', request()->product_ref_num)->where('nilgam_pay_ref', request()->payment_id)->first();

        if ($applicant) {
            $applicant->payment_status = 'paid';
            $applicant->save();
            if ($applicant->registration_type != 'agent') {
                send_sms::dispatch($applicant->id, __('message.register_successful_sms'), 'register');
            }
            return response()->json([
                'success' => true,
                'message' => __('message.success')
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => __('message.fail')
            ]);
        }
    }

    public function steps(Applicant $applicant = null)
    {
        $end_reg = date('Y-m-d') >= env('END_REG_DATE');
        $steps = [
            'step_one' => true,
            'step_two' => false,
            'step_three' => false,
            'step_four' => false,
            'step_five' => false,
            'step_six' => false,
            'step_image' => false,
            'last_true_step' => 'step_one'
        ];

        if ($applicant) {
            $step_image = $applicant->face_image_status == 'rejected' ? true : false;
            
            if (!$step_image) {
                if ($applicant->spouse) {
                    $step_image = $applicant->spouse->face_image_status == 'rejected' ? true : false;
                }

                if (!$step_image) {
                    foreach ($applicant->adult_children as $adult_child) {
                        if ($adult_child->face_image_status == 'rejected') {
                            $step_image = true;
                            break;
                        }
                    }
                }

                if (!$step_image) {
                    foreach ($applicant->children as $child) {
                        if ($child->face_image_status == 'rejected') {
                            $step_image = true;
                            break;
                        }
                    }
                }
            }

            $step_three = false;
            if (($applicant->marital == 'single' && ($applicant->marital_status == 'unmarried' || $applicant->marital_status == 'divorced' || $applicant->marital_status == 'widowed')) || ($applicant->marital == 'married' && ($applicant->marital_status == 'married_us_citizen' || $applicant->marital_status == 'married_not_us_citizen'))) {
                $step_three = true;
            }

            $step_four = $applicant->face_image ? true : false;
            
            if ($step_four) {
                if ($applicant->spouse) {
                    $step_four = $applicant->spouse->face_image ? true : false;
                }

                if ($step_four) {
                    foreach ($applicant->adult_children as $adult_child) {
                        if (!$adult_child->face_image) {
                            $step_four = false;
                            break;
                        }
                    }
                }

                if ($step_four) {
                    foreach ($applicant->children as $child) {
                        if (!$child->face_image) {
                            $step_four = false;
                            break;
                        }
                    }
                }
            }

            $steps = [
                'step_one' => $applicant->payment_status == 'paid' || $applicant->registration_tracking_number || $step_image ? false : true,
                'step_two' => $applicant->marital == 'not_selected' || $applicant->registration_tracking_number || $step_image || ($end_reg && $applicant->payment_status != 'paid') ? false : true,
                'step_three' => $step_image || $applicant->registration_tracking_number || ($end_reg && $applicant->payment_status != 'paid') ? false : ($step_three && $applicant->residence_country ? true : false),
                'step_four' => !$applicant->registration_tracking_number && !$step_image && $step_four && !($end_reg && $applicant->payment_status != 'paid'),
                'step_five' => false,
                'step_six' => (!$step_image || auth()->user()) && $applicant->payment_status == 'paid' ? true : false,
                'step_image' => $step_image,
            ];
        }

        foreach ($steps as $key => $value) {
            if ($value && $key != 'last_true_step') {
                $steps['last_true_step'] = $key.($applicant ? '/'.$applicant->id : '');
            }
        }

        return $steps;
    }

    public function check_data()
    {
        $applicant = Applicant::where('face_image', '<>', null)->where('face_image_status', 'not_selected')->where('payment_status', 'paid')->inRandomOrder()->first();
        if ($applicant) {
            $data = [
                'id' => $applicant->id,
                'type' => 'applicants',
                'image' => url('uploads/'.$applicant->face_image),
                'person' => $applicant,
                'link' => url('step_zero?mobile='.substr($applicant->mobile,1).'&registration_type='.$applicant->registration_type)
            ];
        } else {
            $spouse = Spouse::whereHas('applicant', function ($q) {
                return $q->where('payment_status', 'paid');
            })->where('face_image', '<>', null)->where('face_image_status', 'not_selected')->inRandomOrder()->first();
            if ($spouse) {
                $data = [
                    'id' => $spouse->id,
                    'type' => 'spouses',
                    'image' => url('uploads/'.$spouse->face_image),
                    'person' => $spouse,
                    'link' => url('step_zero?mobile='.substr($spouse->applicant->mobile,1).'&registration_type='.$spouse->applicant->registration_type)
                ];
            } else {
                $adult_child = AdultChild::whereHas('applicant', function ($q) {
                    return $q->where('payment_status', 'paid');
                })->where('face_image', '<>', null)->where('face_image_status', 'not_selected')->inRandomOrder()->first();
                if ($adult_child) {
                    $data = [
                        'id' => $adult_child->id,
                        'type' => 'adult_children',
                        'image' => url('uploads/'.$adult_child->face_image),
                        'person' => $adult_child,
                        'link' => url('step_zero?mobile='.substr($adult_child->applicant->mobile,1).'&registration_type='.$adult_child->applicant->registration_type)
                    ];
                } else {
                    $child = Child::whereHas('applicant', function ($q) {
                        return $q->where('payment_status', 'paid');
                    })->where('face_image', '<>', null)->where('face_image_status', 'not_selected')->inRandomOrder()->first();
                    if ($child) {
                        $data = [
                            'id' => $child->id,
                            'type' => 'children',
                            'image' => url('uploads/'.$child->face_image),
                            'person' => $child,
                            'link' => url('step_zero?mobile='.substr($child->applicant->mobile,1).'&registration_type='.$child->applicant->registration_type)
                        ];
                    } else {
                        $data = [
                            'id' => null,
                            'type' => null,
                            'image' => null,
                            'person' => null,
                            'link' => null
                        ];
                    }
                }
            }
        }
        
        return view('check_data', ['data' => $data]);
    }

    public function check_data_post()
    {
        $attributes = request()->validate([
            'id' => ['required', 'exists:'.request()->type.',id,face_image_status,not_selected'],
            'type' => ['required', 'in:applicants,spouses,adult_children,children'],
            'face_image_status' => ['required', 'in:accepted,rejected']
        ]);

        if ($attributes['type'] == 'applicants') {
            if ($attributes['face_image_status'] == 'rejected') {
                $applicant = Applicant::where('id', $attributes['id'])->first();
                $applicant_id = $attributes['id'];
                $applicant->update([
                    'face_image_status' => $attributes['face_image_status']
                ]);
                $applicant->save();
                $name_label = $applicant->name.' '.$applicant->last_name;
            } else {
                Applicant::where('id', $attributes['id'])->update([
                    'face_image_status' => $attributes['face_image_status']
                ]);
            }
        }

        if ($attributes['type'] == 'spouses') {
            if ($attributes['face_image_status'] == 'rejected') {
                $spouse = Spouse::where('id', $attributes['id'])->first();
                $applicant_id = $spouse->applicant_id;
                $spouse->update([
                    'face_image_status' => $attributes['face_image_status']
                ]);
                $spouse->save();
                $name_label = $spouse->name.' '.$spouse->last_name;
            } else {
                Spouse::where('id', $attributes['id'])->update([
                    'face_image_status' => $attributes['face_image_status']
                ]);
            }
        }

        if ($attributes['type'] == 'adult_children') {
            if ($attributes['face_image_status'] == 'rejected') {
                $adult_child = AdultChild::where('id', $attributes['id'])->first();
                $applicant_id = $adult_child->applicant_id;
                $adult_child->update([
                    'face_image_status' => $attributes['face_image_status']
                ]);
                $adult_child->save();
                $name_label = $adult_child->name.' '.$adult_child->last_name;
            } else {
                AdultChild::where('id', $attributes['id'])->update([
                    'face_image_status' => $attributes['face_image_status']
                ]);
            }
        }

        if ($attributes['type'] == 'children') {
            if ($attributes['face_image_status'] == 'rejected') {
                $child = Child::where('id', $attributes['id'])->first();
                $applicant_id = $child->applicant_id;
                $child->update([
                    'face_image_status' => $attributes['face_image_status']
                ]);
                $child->save();
                $name_label = $child->name.' '.$child->last_name;
            } else {
                Child::where('id', $attributes['id'])->update([
                    'face_image_status' => $attributes['face_image_status']
                ]);
            }
        }

        if ($attributes['face_image_status'] == 'rejected') {
            send_sms::dispatch($applicant_id, sprintf(__('message.applicant_image_regected_sms'), ucwords(strtolower($name_label))), 'check_data');
        }

        return redirect('check_data')->with('success', __('message.face_image_status').' '.__('message.updated_successfully'));
    }

    public function create_in_crm_bulk($limit = 10)
    {
        $applicants = Applicant::where('crm_guid', null)->where('price', '<>', 0)->where('payment_status', 'paid')->take($limit)->get();

        foreach ($applicants as $applicant) {
            create_payment_in_crm::dispatch($applicant->id, 0);
        }

        return $applicants;
    }

    public function register_data()
    {
        $validator = \Validator::make(request()->all(), [
            'key' => ['required', 'in:01d64fa98dec1d898342cb2723d6e127']
        ]);

        if ($validator->fails()) {
            return [
                'success' => false
            ];
        }

        $where_cond = [
            ['face_image', '<>', null],
            ['face_image_status', 'cropped'],
            ['payment_status', 'paid'],
            ['birth_city_en', '<>', null],
            ['registration_tracking_number', null]
        ];

        $applicant = Applicant::whereDoesntHave('spouse', function ($q) {
            return $q->where([
                ['face_image_status', '<>', 'cropped'],
            ])->orWhere([
                ['birth_city_en', null]
            ]);
        })->whereDoesntHave('adult_children', function ($q) {
            return $q->where([
                ['face_image_status', '<>', 'cropped']
            ])->orWhere([
                ['birth_city_en', null]
            ]);
        })->whereDoesntHave('children', function ($q) {
            return $q->where([
                ['face_image_status', '<>', 'cropped']
            ])->orWhere([
                ['birth_city_en', null]
            ]);
        })->where($where_cond)->orderBy('id')->first();

        $applicant_type = 'applicants';

        if (!$applicant) {
            $applicant = Applicant::whereHas('spouse', function ($q) {
                return $q->where([
                    ['face_image', '<>', null],
                    ['face_image_status', 'cropped'],
                    ['registration_tracking_number', null]
                ]);
            })->where([
                ['face_image', '<>', null],
                ['face_image_status', 'cropped'],
                ['payment_status', 'paid'],
                ['birth_city_en', '<>', null],
                ['double_register', 1],
                ['registration_tracking_number', '<>', null]
            ])->first();
            $applicant_type = 'spouses';
        }

        if (!$applicant) {
            $applicant = Applicant::with(['adult_children' => function ($q) {
                return $q->where([
                    ['face_image', '<>', null],
                    ['face_image_status', 'cropped'],
                    ['independent_register', 1],
                    ['registration_tracking_number', null]
                ])->first();
            }])->whereHas('adult_children', function ($q) {
                return $q->where([
                    ['face_image', '<>', null],
                    ['face_image_status', 'cropped'],
                    ['independent_register', 1],
                    ['registration_tracking_number', null]
                ]);
            })->where([
                ['face_image', '<>', null],
                ['face_image_status', 'cropped'],
                ['payment_status', 'paid'],
                ['birth_city_en', '<>', null],
                ['registration_tracking_number', '<>', null]
            ])->first();
            $applicant_type = 'adult_children';
        }

        if ($applicant) {
            if ($applicant_type != 'adult_children') {
                $birth_date_fa = explode('-', $applicant->birth_date_fa);
                $birth_date_en = \Helper::jalali_to_gregorian($birth_date_fa[0], $birth_date_fa[1], $birth_date_fa[2]);
                $applicant->birth_day = sprintf("%02d", $birth_date_en[2]);
                $applicant->birth_month = sprintf("%02d", $birth_date_en[1]);
                $applicant->birth_year = $birth_date_en[0];
                $applicant->total_children_count = $applicant->adult_children_count + $applicant->children_count;
            }
            
            $applicant->type = $applicant_type;
            if (strlen($applicant->residence_address_en) > 30) {
                $address_array = explode(',', $applicant->residence_address_en);
                $residence_address_line_1 = [];
                $residence_address_line_2 = [];
                foreach ($address_array as $address) {
                    if (empty($residence_address_line_2)) {
                        if (strlen(implode(',', array_merge($residence_address_line_1, [$address]))) > 30) {
                            $residence_address_line_2[] = $address;
                        } else {
                            $residence_address_line_1[] = $address;
                        }
                    } else {
                        $residence_address_line_2[] = $address;
                    }
                }
                $applicant->residence_address_line_1 = trim(implode(',', $residence_address_line_1));
                $applicant->residence_address_line_2 = trim(implode(',', $residence_address_line_2));
            } else {
                $applicant->residence_address_line_1 = $applicant->residence_address_en;
                $applicant->residence_address_line_2 = '';
            }

            $total_children = [];

            foreach ($applicant->adult_children as $adult_child) {
                $birth_date_fa = explode('-', $adult_child->birth_date_fa);
                $birth_date_en = \Helper::jalali_to_gregorian($birth_date_fa[0], $birth_date_fa[1], $birth_date_fa[2]);
                $adult_child->birth_day = sprintf("%02d", $birth_date_en[2]);
                $adult_child->birth_month = sprintf("%02d", $birth_date_en[1]);
                $adult_child->birth_year = $birth_date_en[0];
                $total_children[] = $adult_child;
            }

            if ($applicant_type == 'adult_children') {
                $applicant->id = $adult_child->id;
                $applicant->name = $adult_child->name;
                $applicant->last_name = $adult_child->last_name;
                $applicant->gender = $adult_child->gender;
                $applicant->birth_day = $adult_child->birth_day;
                $applicant->birth_month = $adult_child->birth_month;
                $applicant->birth_year = $adult_child->birth_year;
                $applicant->education_degree = $adult_child->education_degree;
                $applicant->birth_country = $adult_child->birth_country;
                $applicant->birth_city_en = $adult_child->birth_city_en;
                $applicant->email = $adult_child->email;
                $applicant->face_image = $adult_child->face_image;
                $applicant->total_children = null;
                $applicant->children = null;
                $applicant->total_children_count = 0;
                $applicant->marital = 'single';
                $applicant->marital_status = 'unmarried';
            } else {
                foreach ($applicant->children as $child) {
                    $birth_date_fa = explode('-', $child->birth_date_fa);
                    $birth_date_en = \Helper::jalali_to_gregorian($birth_date_fa[0], $birth_date_fa[1], $birth_date_fa[2]);
                    $child->birth_day = sprintf("%02d", $birth_date_en[2]);
                    $child->birth_month = sprintf("%02d", $birth_date_en[1]);
                    $child->birth_year = $birth_date_en[0];
                    $total_children[] = $child;
                }

                $applicant->total_children = empty($total_children) ? null : $total_children;

                if ($applicant->spouse) {
                    $birth_date_fa = explode('-', $applicant->spouse->birth_date_fa);
                    $birth_date_en = \Helper::jalali_to_gregorian($birth_date_fa[0], $birth_date_fa[1], $birth_date_fa[2]);
                    $applicant->spouse->birth_day = sprintf("%02d", $birth_date_en[2]);
                    $applicant->spouse->birth_month = sprintf("%02d", $birth_date_en[1]);
                    $applicant->spouse->birth_year = $birth_date_en[0];
                } else {
                    $applicant->spouse = null;
                }

                if ($applicant_type == 'spouses') {
                    $id = $applicant->id;
                    $name = $applicant->name;
                    $last_name = $applicant->last_name;
                    $gender = $applicant->gender;
                    $birth_day = $applicant->birth_day;
                    $birth_month = $applicant->birth_month;
                    $birth_year = $applicant->birth_year;
                    $education_degree = $applicant->education_degree;
                    $birth_country = $applicant->birth_country;
                    $birth_city_en = $applicant->birth_city_en;
                    $email = $applicant->email;
                    $face_image = $applicant->face_image;

                    $applicant->id = $applicant->spouse->id;
                    $applicant->name = $applicant->spouse->name;
                    $applicant->last_name = $applicant->spouse->last_name;
                    $applicant->gender = $applicant->spouse->gender;
                    $applicant->birth_day = $applicant->spouse->birth_day;
                    $applicant->birth_month = $applicant->spouse->birth_month;
                    $applicant->birth_year = $applicant->spouse->birth_year;
                    $applicant->education_degree = $applicant->spouse->education_degree;
                    $applicant->birth_country = $applicant->spouse->birth_country;
                    $applicant->birth_city_en = $applicant->spouse->birth_city_en;
                    $applicant->email = $applicant->spouse->email;
                    $applicant->face_image = $applicant->spouse->face_image;

                    $applicant->spouse->id = $id;
                    $applicant->spouse->name = $name;
                    $applicant->spouse->last_name = $last_name;
                    $applicant->spouse->gender = $gender;
                    $applicant->spouse->birth_day = $birth_day;
                    $applicant->spouse->birth_month = $birth_month;
                    $applicant->spouse->birth_year = $birth_year;
                    $applicant->spouse->education_degree = $education_degree;
                    $applicant->spouse->birth_country = $birth_country;
                    $applicant->spouse->birth_city_en = $birth_city_en;
                    $applicant->spouse->email = $email;
                    $applicant->spouse->face_image = $face_image;
                }
            }
            
            return [
                'success' => true,
                'data' => $applicant
            ];
        }

        return [
            'success' => false
        ];
    }

    public function translate_data($limit = 10)
    {
        $validator = \Validator::make(request()->all(), [
            'key' => ['required', 'in:01d64fa98dec1d898342cb2723d6e127']
        ]);

        if ($validator->fails()) {
            return [
                'success' => false
            ];
        }

        $applicants = Applicant::where('face_image', '<>', null)->where('face_image_status', 'cropped')->where('payment_status', 'paid')->where('birth_city_en', null)->take($limit)->get();
        $spouses = Spouse::where('face_image', '<>', null)->where('face_image_status', 'cropped')->where('birth_city_en', null)->take($limit)->get();
        $adult_children = AdultChild::where('face_image', '<>', null)->where('face_image_status', 'cropped')->where('birth_city_en', null)->take($limit)->get();
        $children = Child::where('face_image', '<>', null)->where('face_image_status', 'cropped')->where('birth_city_en', null)->take($limit)->get();
        foreach ($applicants as $applicant) {
            translate::dispatch($applicant->id, 'applicant');
        }
        foreach ($spouses as $spouse) {
            translate::dispatch($spouse->id, 'spouse');
        }
        foreach ($adult_children as $adult_child) {
            translate::dispatch($adult_child->id, 'adult_child');
        }
        foreach ($children as $child) {
            translate::dispatch($child->id, 'child');
        }
        return [
            'applicants' => $applicants,
            'spouses' => $spouses,
            'adult_children' => $adult_children,
            'children' => $children,
        ];
    }

    public function registration_tracking_number()
    {
        $validator = \Validator::make(request()->all(), [
            'key' => ['required', 'in:01d64fa98dec1d898342cb2723d6e127'],
            'type' => ['required', 'in:applicants,spouses,adult_children'],
            'id' => ['required', 'exists:'.request()->type.',id'],
            'registration_tracking_number' => ['required']
        ]);

        if ($validator->fails()) {
            return [
                'success' => false
            ];
        }

        if (request()->type == 'applicants') {
            $applicant = Applicant::where('id', request()->id)->first();
            $applicant_id = request()->id;
            $applicant->update([
                'registration_tracking_number' => request()->registration_tracking_number
            ]);
            $applicant->save();
            $name_label = $applicant->name.' '.$applicant->last_name;
        }

        if (request()->type == 'spouses') {
            $spouse = Spouse::where('id', request()->id)->first();
            $applicant_id = $spouse->applicant_id;
            $spouse->update([
                'registration_tracking_number' => request()->registration_tracking_number
            ]);
            $spouse->save();
            $name_label = $spouse->name.' '.$spouse->last_name;
        }

        if (request()->type == 'adult_children') {
            $adult_child = AdultChild::where('id', request()->id)->first();
            $applicant_id = $adult_child->applicant_id;
            $adult_child->update([
                'registration_tracking_number' => request()->registration_tracking_number
            ]);
            $adult_child->save();
            $name_label = $adult_child->name.' '.$adult_child->last_name;
        }

        send_sms::dispatch($applicant_id, sprintf(__('message.registration_tracking_number_sms'), ucwords(strtolower($name_label)), request()->registration_tracking_number), 'tracking_number');
        return [
            'success' => true
        ];
    }

    public function check_lottery_status()
    {
        $validator = \Validator::make(request()->all(), [
            'key' => ['required', 'in:01d64fa98dec1d898342cb2723d6e127'],
            'sys_id' => ['required', 'in:NIL-C12,LOT-1,LOT-2,LOT-3,LOT-4,LOT-5,LOT-6,LOT-7,LOT-8,LOT-9,LOT-10'],
        ]);

        if ($validator->fails()) {
            return [
                'success' => false
            ];
        }

        $applicant_type = 'applicants';

        $applicant = Applicant::where([
            ['registration_tracking_number', '<>', null],
            ['lottery_status', 'not_checked'],
            ['lottery_status_sys', request()->sys_id]
        ])->first();

        if (!$applicant) {
            Applicant::where([
                ['registration_tracking_number', '<>', null],
                ['lottery_status', 'not_checked'],
                ['lottery_status_sys', null]
            ])->first()->update(['lottery_status_sys' => request()->sys_id]);
            $applicant = Applicant::where([
                ['registration_tracking_number', '<>', null],
                ['lottery_status', 'not_checked'],
                ['lottery_status_sys', request()->sys_id]
            ])->first();
        }

        if (!$applicant) {
            $applicant_type = 'spouses';
            $applicant = Spouse::where([
                ['registration_tracking_number', '<>', null],
                ['lottery_status', 'not_checked'],
                ['lottery_status_sys', request()->sys_id]
            ])->first();
            if (!$applicant) {
                Spouse::where([
                    ['registration_tracking_number', '<>', null],
                    ['lottery_status', 'not_checked'],
                    ['lottery_status_sys', null]
                ])->first()->update(['lottery_status_sys' => request()->sys_id]);
                $applicant = Spouse::where([
                    ['registration_tracking_number', '<>', null],
                    ['lottery_status', 'not_checked'],
                    ['lottery_status_sys', request()->sys_id]
                ])->first();
            }
        }

        if (!$applicant) {
            $applicant_type = 'adult_children';
            $applicant = AdultChild::where([
                ['registration_tracking_number', '<>', null],
                ['lottery_status', 'not_checked'],
                ['lottery_status_sys', request()->sys_id]
            ])->first();
            if (!$applicant) {
                AdultChild::where([
                    ['registration_tracking_number', '<>', null],
                    ['lottery_status', 'not_checked'],
                    ['lottery_status_sys', null]
                ])->first()->update(['lottery_status_sys' => request()->sys_id]);
                $applicant = AdultChild::where([
                    ['registration_tracking_number', '<>', null],
                    ['lottery_status', 'not_checked'],
                    ['lottery_status_sys', request()->sys_id]
                ])->first();
            }
        }

        if ($applicant) {
            $birth_date_fa = explode('-', $applicant->birth_date_fa);
            $birth_date_en = \Helper::jalali_to_gregorian($birth_date_fa[0], $birth_date_fa[1], $birth_date_fa[2]);

            return [
                'success' => true,
                'data' => [
                    'type' => $applicant_type,
                    'id' => $applicant->id,
                    'registration_tracking_number' => $applicant->registration_tracking_number,
                    'last_name' => $applicant->last_name,
                    'birth_year' => $birth_date_en[0],
                ]
            ];
        }

        return [
            'success' => false
        ];
    }

    public function update_lottery_status()
    {
        $validator = \Validator::make(request()->all(), [
            'key' => ['required', 'in:01d64fa98dec1d898342cb2723d6e127'],
            'type' => ['required', 'in:applicants,spouses,adult_children'],
            'id' => ['required', 'exists:'.request()->type.',id,lottery_status,not_checked'],
            'status' => ['required', 'in:selected,not_selected']
        ]);

        if ($validator->fails()) {
            return [
                'success' => false
            ];
        }

        if (request()->type == 'applicants') {
            $applicant = Applicant::where('id', request()->id)->first();
            $applicant_id = request()->id;
            $applicant->update([
                'lottery_status' => request()->status
            ]);
            $applicant->save();
            $name_label = $applicant->name.' '.$applicant->last_name;
        }

        if (request()->type == 'spouses') {
            $spouse = Spouse::where('id', request()->id)->first();
            $applicant_id = $spouse->applicant_id;
            $spouse->update([
                'lottery_status' => request()->status
            ]);
            $spouse->save();
            $name_label = $spouse->name.' '.$spouse->last_name;
        }

        if (request()->type == 'adult_children') {
            $adult_child = AdultChild::where('id', request()->id)->first();
            $applicant_id = $adult_child->applicant_id;
            $adult_child->update([
                'lottery_status' => request()->status
            ]);
            $adult_child->save();
            $name_label = $adult_child->name.' '.$adult_child->last_name;
        }

        if (request()->status == 'not_selected') {
            send_sms::dispatch($applicant_id, sprintf(__('message.lottery_not_selected_status_sms'), ucwords(strtolower($name_label))), 'lottery_status');
        }
        
        return [
            'success' => true
        ];
    }
}
