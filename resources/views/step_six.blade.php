@extends('layouts.user_type.guest')

@section('content')
<style type="text/css">
    .tracking_number {
        border: 1px solid #e1e1e1;
        border-radius: 10px;
        box-shadow: 0 20px 27px 0 rgba(0,0,0,.05);
    }
</style>

<div class="container-fluid p-4 pt-1 mt-7" style="max-width: 1320px;">
    <div class="row">
        @if(session('success'))
            <div class="mt-0 alert alert-success alert-dismissible fade show" id="alert-success" role="alert">
                <span class="alert-text text-white">
                {{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <i class="fa fa-close" aria-hidden="true"></i>
                </button>
            </div>
            @php Session::forget('success'); @endphp
        @endif
        @if(session('error'))
            <div class="mt-0 alert alert-primary alert-dismissible fade show" role="alert">
                <span class="alert-text text-white">
                {{ session('error') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <i class="fa fa-close" aria-hidden="true"></i>
                </button>
            </div>
            @php Session::forget('error'); @endphp
        @endif
        @if($errors->any())
            <div class="mt-0 alert alert-primary alert-dismissible fade show" role="alert">
                <span class="alert-text text-white">
                {{$errors->first()}}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <i class="fa fa-close" aria-hidden="true"></i>
                </button>
            </div>
        @endif
    </div>
    @include('layouts.breadcrump')
    <div class="alert alert-success text-white" role="alert">
        {{__('message.your_registeration_was_successful')}}
    </div>
    <div class="card">
        <div class="card-body pt-4 p-3">
            <div class="row">
                <div class="col-lg-3 col-12 text-center mb-4">
                    <h4 class="mb-4 text-center">{{ __('message.applicant_information') }}</h4>
                    <img width="150px" height="150px" src="{{url('uploads', $applicant->face_image)}}" style="border-radius: 50%;">
                </div>
                <div class="col-lg-9 col-12">
                    <div class="row justify-content-center align-items-center mb-4 pt-2 pb-1 tracking_number">
                        <div class="col-lg-3 col-md-3 col-6 text-center mb-1">
                            <h6>{{ __('message.lottery_registration_status') }}</h6>
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 mb-1">
                            @if($applicant->registration_tracking_number)
                                @if(File::exists(public_path("conf/$applicant->registration_tracking_number.jpg")))
                                <div class="row justify-content-center align-items-center">
                                    <div class="col-4 p-0">
                                        <h6 class="text-success m-0">{{ __('message.completed') }}</h6>
                                    </div>
                                    <div class="col-8 pe-0">
                                        <a target="_blank" href="{{url('conf', $applicant->registration_tracking_number.'.jpg')}}" class="btn bg-gradient-primary w-100 m-0"><i class="fa fa-eye" aria-hidden="true"></i> {{ __('message.screen_shot') }}</a>
                                    </div>
                                </div>
                                @else
                                <span class="btn bg-gradient-success w-100 m-0">{{ __('message.completed') }}</span>
                                @endif
                            @else
                            <span class="btn bg-gradient-warning w-100 m-0">{{ __('message.proccessing') }}</span>
                            @endif
                        </div>
                        @if($applicant->registration_tracking_number)
                        <div class="col-lg-3 col-md-3 col-4 text-center">
                            <h6>{{ __('message.registration_tracking_number') }}</h6>
                        </div>
                        <div class="col-lg-3 col-md-3 col-8">
                            <div class="form-group">
                                <input dir="ltr" class="form-control" value="{{$applicant->registration_tracking_number}}" type="text" readonly>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.name_en') }}</label>
                                <input dir="ltr" class="form-control" value="{{$applicant->name}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.last_name_en') }}</label>
                                <input dir="ltr" class="form-control" value="{{$applicant->last_name}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.gender') }}</label>
                                <input class="form-control" value="{{__('message.'.$applicant->gender)}}" type="text" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.birth_country') }}</label>
                                <input class="form-control" value="{{\helper::get_countries()[$applicant->birth_country]}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.birth_city') }}</label>
                                <input class="form-control" value="{{$applicant->birth_city}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <label>{{ __('message.birth_date_shamsi') }}</label>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <input class="form-control" value="{{explode('-', $applicant->birth_date_fa)[2]}}" type="text" readonly>
                                    </div>
                                </div>
                                <div class="col-4 p-0">
                                    <div class="form-group">
                                        <input class="form-control" value="{{\helper::get_monthes()[explode('-', $applicant->birth_date_fa)[1]]}}" type="text" readonly>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <input class="form-control" value="{{explode('-', $applicant->birth_date_fa)[0]}}" type="text" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.mobile') }}</label>
                                <input dir="ltr" class="form-control" value="{{$applicant->mobile}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.email') }}</label>
                                <input dir="ltr" class="form-control" value="{{$applicant->email}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.education_degree') }}</label>
                                <input class="form-control" value="{{__('message.'.$applicant->education_degree)}}" type="text" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-lg-3 col-md-3">
                            <div class="form-group">
                                <label>{{ __('message.residence_country') }} {{__('message.residence')}}</label>
                                <input class="form-control" value="{{\helper::get_countries()[$applicant->residence_country]}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <div class="form-group">
                                <label>{{ __('message.residence_state') }}</label>
                                <input class="form-control" value="{{$applicant->residence_state}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <div class="form-group">
                                <label>{{ __('message.residence_city') }}</label>
                                <input class="form-control" value="{{$applicant->residence_city}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <div class="form-group">
                                <label>{{ __('message.residence_street') }}</label>
                                <input class="form-control" value="{{$applicant->residence_street}}" type="text" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-lg-3 col-md-3">
                            <div class="form-group">
                                <label>{{ __('message.residence_alley') }}</label>
                                <input class="form-control" value="{{$applicant->residence_alley}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <div class="form-group">
                                <label>{{ __('message.residence_no') }}</label>
                                <input class="form-control" value="{{$applicant->residence_no}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <div class="form-group">
                                <label>{{ __('message.residence_unit') }}</label>
                                <input class="form-control" value="{{$applicant->residence_unit}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <div class="form-group">
                                <label>{{ __('message.residence_postal_code') }}</label>
                                <input class="form-control" value="{{$applicant->residence_postal_code}}" type="text" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($applicant->spouse)
    <div class="card mt-4">
        <div class="card-body pt-4 p-3">
            <div class="row">
                <div class="col-lg-3 col-12 text-center mb-4">
                    <h4 class="mb-4 text-center">{{ __('message.spouse_information') }}</h4>
                    <img width="150px" height="150px" src="{{url('uploads', $applicant->spouse->face_image)}}" style="border-radius: 50%;">
                </div>
                <div class="col-lg-9 col-12">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-check d-flex justify-content-center">
                                <label class="m-1 mb-0 text-sm">{{ __('message.double_register') }}</label>
                                <input disabled class="form-check-input" type="checkbox" {{$applicant->spouse->double_register ? 'checked' : ''}}>
                            </div>
                        </div>
                    </div>
                    @if($applicant->double_register)
                    <div class="row justify-content-center align-items-center mt-4 pt-2 pb-1 tracking_number">
                        <div class="col-lg-3 col-md-3 col-6 text-center mb-1">
                            <h6>{{ __('message.lottery_registration_status') }}</h6>
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 mb-1">
                            @if($applicant->spouse->registration_tracking_number)
                                @if(File::exists(public_path('conf/'.$applicant->spouse->registration_tracking_number.'.jpg')))
                                <div class="row justify-content-center align-items-center">
                                    <div class="col-4 p-0">
                                        <h6 class="text-success m-0">{{ __('message.completed') }}</h6>
                                    </div>
                                    <div class="col-8 pe-0">
                                        <a target="_blank" href="{{url('conf', $applicant->spouse->registration_tracking_number.'.jpg')}}" class="btn bg-gradient-primary w-100 m-0"><i class="fa fa-eye" aria-hidden="true"></i> {{ __('message.screen_shot') }}</a>
                                    </div>
                                </div>
                                @else
                                <span class="btn bg-gradient-success w-100 m-0">{{ __('message.completed') }}</span>
                                @endif
                            @else
                            <span class="btn bg-gradient-warning w-100 m-0">{{ __('message.proccessing') }}</span>
                            @endif
                        </div>
                        @if($applicant->spouse->registration_tracking_number)
                        <div class="col-lg-3 col-md-3 col-4 text-center">
                            <h6>{{ __('message.registration_tracking_number') }}</h6>
                        </div>
                        <div class="col-lg-3 col-md-3 col-8">
                            <div class="form-group">
                                <input dir="ltr" class="form-control" value="{{$applicant->spouse->registration_tracking_number}}" type="text" readonly>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                    <div class="row justify-content-center mt-4">
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.name_en') }}</label>
                                <input dir="ltr" class="form-control" value="{{$applicant->spouse->name}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.last_name_en') }}</label>
                                <input dir="ltr" class="form-control" value="{{$applicant->spouse->last_name}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.gender') }}</label>
                                <input class="form-control" value="{{__('message.'.$applicant->spouse->gender)}}" type="text" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.birth_country') }}</label>
                                <input class="form-control" value="{{\helper::get_countries()[$applicant->spouse->birth_country]}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.birth_city') }}</label>
                                <input class="form-control" value="{{$applicant->spouse->birth_city}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <label>{{ __('message.birth_date_shamsi') }}</label>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <input class="form-control" value="{{explode('-', $applicant->spouse->birth_date_fa)[2]}}" type="text" readonly>
                                    </div>
                                </div>
                                <div class="col-4 p-0">
                                    <div class="form-group">
                                        <input class="form-control" value="{{\helper::get_monthes()[explode('-', $applicant->spouse->birth_date_fa)[1]]}}" type="text" readonly>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <input class="form-control" value="{{explode('-', $applicant->spouse->birth_date_fa)[0]}}" type="text" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($applicant->double_register)
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.mobile') }}</label>
                                <input dir="ltr" class="form-control" value="{{$applicant->spouse->mobile}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.email') }}</label>
                                <input dir="ltr" class="form-control" value="{{$applicant->spouse->email}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.education_degree') }}</label>
                                <input class="form-control" value="{{__('message.'.$applicant->spouse->education_degree)}}" type="text" readonly>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
    @foreach($applicant->adult_children as $index => $adult_child)
    <div class="card mt-4">
        <div class="card-body pt-4 p-3">
            <div class="row">
                <div class="col-lg-3 col-12 mb-4 text-center mb-4">
                    <h5 class="mb-4 text-center">{{ __('message.information') }} {{ __('message.adult_child') }} ({{ __('message.'.($index+1).'_st') }})</h5>
                    <img width="150px" height="150px" src="{{url('uploads', $adult_child->face_image)}}" style="border-radius: 50%;">
                </div>
                <div class="col-lg-9 col-12">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-check d-flex justify-content-center">
                                <label class="m-1 mb-0 text-sm" for="independent_register_{{$adult_child->id}}">{{ __('message.double_register') }}</label>
                                <input disabled class="form-check-input" type="checkbox" {{$adult_child->independent_register ? 'checked' : ''}}>
                            </div>
                        </div>
                    </div>
                    @if($adult_child->independent_register)
                    <div class="row justify-content-center align-items-center mt-4 pt-2 pb-1 tracking_number">
                        <div class="col-lg-3 col-md-3 col-6 text-center mb-1">
                            <h6>{{ __('message.lottery_registration_status') }}</h6>
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 mb-1">
                            @if($adult_child->registration_tracking_number)
                                @if(File::exists(public_path("conf/$adult_child->registration_tracking_number.jpg")))
                                <div class="row justify-content-center align-items-center">
                                    <div class="col-4 p-0">
                                        <h6 class="text-success m-0">{{ __('message.completed') }}</h6>
                                    </div>
                                    <div class="col-8 pe-0">
                                        <a target="_blank" href="{{url('conf', $adult_child->registration_tracking_number.'.jpg')}}" class="btn bg-gradient-primary w-100 m-0"><i class="fa fa-eye" aria-hidden="true"></i> {{ __('message.screen_shot') }}</a>
                                    </div>
                                </div>
                                @else
                                <span class="btn bg-gradient-success w-100 m-0">{{ __('message.completed') }}</span>
                                @endif
                            @else
                            <span class="btn bg-gradient-warning w-100 m-0">{{ __('message.proccessing') }}</span>
                            @endif
                        </div>
                        @if($adult_child->registration_tracking_number)
                        <div class="col-lg-3 col-md-3 text-center col-4">
                            <h6>{{ __('message.registration_tracking_number') }}</h6>
                        </div>
                        <div class="col-lg-3 col-md-3 col-8">
                            <div class="form-group">
                                <input dir="ltr" class="form-control" value="{{$adult_child->registration_tracking_number}}" type="text" readonly>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                    <div class="row justify-content-center mt-4">
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.name_en') }}</label>
                                <input dir="ltr" class="form-control" value="{{$adult_child->name}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.last_name_en') }}</label>
                                <input dir="ltr" class="form-control" value="{{$adult_child->last_name}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.gender') }}</label>
                                <input class="form-control" value="{{__('message.'.$adult_child->gender)}}" type="text" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.birth_country') }}</label>
                                <input class="form-control" value="{{\helper::get_countries()[$adult_child->birth_country]}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.birth_city') }}</label>
                                <input class="form-control" value="{{$adult_child->birth_city}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <label>{{ __('message.birth_date_shamsi') }}</label>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <input class="form-control" value="{{explode('-', $adult_child->birth_date_fa)[2]}}" type="text" readonly>
                                    </div>
                                </div>
                                <div class="col-4 p-0">
                                    <div class="form-group">
                                        <input class="form-control" value="{{\helper::get_monthes()[explode('-', $adult_child->birth_date_fa)[1]]}}" type="text" readonly>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <input class="form-control" value="{{explode('-', $adult_child->birth_date_fa)[0]}}" type="text" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($adult_child->independent_register)
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.mobile') }}</label>
                                <input dir="ltr" class="form-control" value="{{$adult_child->mobile}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.email') }}</label>
                                <input dir="ltr" class="form-control" value="{{$adult_child->email}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.education_degree') }}</label>
                                <input class="form-control" value="{{__('message.'.$adult_child->education_degree)}}" type="text" readonly>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
    @foreach($applicant->children as $index => $child)
    <div class="card mt-4">
        <div class="card-body pt-4 p-3">
            <div class="row">
                <div class="col-lg-3 col-12 text-center mb-4">
                    <h5 class="mb-4 text-center">{{ __('message.information') }} {{ __('message.child') }} ({{ __('message.'.($index+1).'_st') }})</h5>
                    <img width="150px" height="150px" src="{{url('uploads', $child->face_image)}}" style="border-radius: 50%;">
                </div>
                <div class="col-lg-9 col-12">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.name_en') }}</label>
                                <input dir="ltr" class="form-control" value="{{$child->name}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.last_name_en') }}</label>
                                <input dir="ltr" class="form-control" value="{{$child->last_name}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.gender') }}</label>
                                <input class="form-control" value="{{__('message.'.$child->gender)}}" type="text" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.birth_country') }}</label>
                                <input class="form-control" value="{{\helper::get_countries()[$child->birth_country]}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label>{{ __('message.birth_city') }}</label>
                                <input class="form-control" value="{{$child->birth_city}}" type="text" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <label>{{ __('message.birth_date_shamsi') }}</label>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <input class="form-control" value="{{explode('-', $child->birth_date_fa)[2]}}" type="text" readonly>
                                    </div>
                                </div>
                                <div class="col-4 p-0">
                                    <div class="form-group">
                                        <input class="form-control" value="{{\helper::get_monthes()[explode('-', $child->birth_date_fa)[1]]}}" type="text" readonly>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <input class="form-control" value="{{explode('-', $child->birth_date_fa)[0]}}" type="text" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('.country').select2();
    });
</script>
@endsection