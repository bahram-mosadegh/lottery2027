<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Support\Facades\View;
use App\DataTables\CouponDataTable;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function check($coupon, $ammount = null)
    {
        request()->request->add(['coupon' => $coupon]);
        request()->request->add(['ammount' => $ammount]);

        $rules = [
            'coupon' =>  ['required', 'exists:coupons,code'],
            'ammount' =>  ['nullable', 'numeric'],
        ];

        $validator = \Validator::make(request()->all(), $rules);

        if ($validator->fails()) {
           return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        $coupon = Coupon::where('code', request()->coupon)->first();

        if ($coupon->expire_date >= date('Y-m-d')) {
            $data = [
                'code' => $coupon->code,
                'type' => $coupon->type,
                'coupon_ammount' => $coupon->ammount,
            ];

            $data['main_ammount'] = 0;
            $data['discounted_ammount'] = 0;

            if (request()->ammount) {
                $data['main_ammount'] = request()->ammount;
                if ($coupon->type == 'percent') {
                    $data['discounted_ammount'] = request()->ammount * (100 - $coupon->ammount) / 100;
                }

                if ($coupon->type == 'discount') {
                    $discounted_ammount = request()->ammount - $coupon->ammount;
                    $data['discounted_ammount'] = $discounted_ammount > 0 ? $discounted_ammount : 0;
                }
            }

            return [
                'success' => true,
                'data' => $data
            ];
        } else {
            return [
                'success' => false,
                'errors' => [
                    'coupon' => __('message.coupon_code_expired')
                ]
            ];
        }
    }

    public function index(CouponDataTable $dataTable)
    {
        return $dataTable->render('coupons');
    }

    public function add_get()
    {
        return view('add_coupon');
    }

    public function add_post()
    {
        $rules = [
            'code' => ['required', 'unique:coupons'],
            'ammount' => ['required', 'numeric', 'min:1'],
            'type' => ['required', 'in:percent,discount'],
            'expire_date' => ['required', 'date_format:Y-m-d']
        ];

        if (request()->type == 'percent') {
            $rules['ammount'] = ['required', 'numeric', 'max:100', 'min:1'];
        }

        $attributes = request()->validate($rules);

        Coupon::create($attributes);

        return redirect('/coupons')->with('success', __('message.coupon').' '.__('message.added_successfully'));
    }

    public function edit_get($id)
    {
        $coupon = Coupon::find($id);
        if ($coupon) {
            return view('edit_coupon', ['coupon' => $coupon]);
        } else {
            return redirect('/coupons')->with('error', __('message.coupon').' '.__('message.not_found'));
        }
    }

    public function edit_post($id)
    {
        request()->request->add(['id' => $id]);
        $rules = [
            'id' => ['required', 'exists:coupons,id'],
            'code' => ['required', Rule::unique('coupons')->ignore($id)],
            'ammount' => ['required', 'numeric', 'min:1'],
            'type' => ['required', 'in:percent,discount'],
            'expire_date' => ['required', 'date_format:Y-m-d']
        ];

        if (request()->type == 'percent') {
            $rules['ammount'] = ['required', 'numeric', 'max:100', 'min:1'];
        }

        $attributes = request()->validate($rules);

        Coupon::where('id', $id)->update($attributes);

        return redirect('/coupons')->with('success', __('message.coupon').' '.__('message.updated_successfully'));
    }

    public function delete($id)
    {
        if (Coupon::where('id',$id)->delete()) {
            return redirect('/coupons')->with('success', __('message.coupon').' '.__('message.deleted_successfully'));
        } else {
            return redirect('/coupons')->with('error', __('message.coupon').' '.__('message.not_found'));
        }
    }
}
