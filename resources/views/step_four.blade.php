@extends('layouts.user_type.guest')

@section('content')

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
    <form method="POST" action="{{url('step_four')}}">
    @csrf
        <input type="hidden" name="applicant_id" value="{{$applicant ? $applicant->id : null}}">
        <div class="card">
            <div class="card-body pt-4 p-3">
                <div class="row">
                    <div class="col-lg-3 col-12 text-center mb-4">
                        <h4 class="mb-4 text-center">{{ __('message.applicant_information') }}</h4>
                        <img width="150px" height="150px" src="{{url('uploads', $applicant->face_image)}}" style="border-radius: 50%;">
                    </div>
                    <div class="col-lg-9 col-12">
                        <div class="row justify-content-center">
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <h6>{{ __('message.name_en') }} <span class="text-danger">*</span></h6>
                                    <input required dir="ltr" onblur="if(this.value && !final_latin_name_check(this.value)){$(this).val('')}" onkeypress="return latin_chars_check(event);" class="form-control" value="{{old('applicant_name') ? old('applicant_name') : $applicant->name}}" type="text" placeholder="{{ __('message.name_en') }}" name="applicant_name">
                                    @error('applicant_name')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <h6>{{ __('message.last_name_en') }} <span class="text-danger">*</span></h6>
                                    <input required dir="ltr" onblur="if(this.value && !final_latin_name_check(this.value)){$(this).val('')}" onkeypress="return latin_chars_check(event);" class="form-control" value="{{old('applicant_last_name') ? old('applicant_last_name') : $applicant->last_name}}" type="text" placeholder="{{ __('message.last_name_en') }}" name="applicant_last_name">
                                    @error('applicant_last_name')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <h6>{{ __('message.gender') }} <span class="text-danger">*</span></h6>
                                    @php $gender = old('applicant_gender') ? old('applicant_gender') : $applicant->gender; @endphp
                                    <select required class="form-control" name="applicant_gender">
                                        <option value="">-- {{ __('message.select') }} --</option>
                                        <option value="male" {{$gender == 'male' ? 'selected' : ''}}>{{ __('message.male') }}</option>
                                        <option value="female" {{$gender == 'female' ? 'selected' : ''}}>{{ __('message.female') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <h6>{{ __('message.birth_country') }} <span class="text-danger">*</span></h6>
                                    @php $applicant_birth_country = old('applicant_birth_country') ? old('applicant_birth_country') : $applicant->birth_country; @endphp
                                    <select required class="form-control country" name="applicant_birth_country" id="applicant_birth_country">
                                        @foreach(\helper::get_countries() as $code => $country)
                                        <option value="{{ $code }}" {{$applicant_birth_country == $code ? 'selected' : ''}}>{{ $country }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <h6>{{ __('message.birth_city') }} <span class="text-danger">*</span></h6>
                                    <input required class="form-control" value="{{old('applicant_birth_city') ? old('applicant_birth_city') : $applicant->birth_city}}" type="text" placeholder="{{ __('message.birth_city') }}" name="applicant_birth_city">
                                    @error('applicant_birth_city')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <h6>{{ __('message.birth_date_shamsi') }} <span class="text-danger">*</span></h6>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            @php $applicant_birth_day = old('applicant_birth_day') ? old('applicant_birth_day') : ($applicant->birth_date_fa ? explode('-', $applicant->birth_date_fa)[2] : null); @endphp
                                            <select required class="form-control" name="applicant_birth_day" id="applicant_birth_day">
                                                <option value="">{{ __('message.day') }}</option>
                                                @for($i=1; $i<=31; $i++)
                                                <option value="{{ sprintf('%02d', $i) }}" {{$applicant_birth_day == $i ? 'selected' : ''}}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4 p-0">
                                        <div class="form-group">
                                            @php $applicant_birth_month = old('applicant_birth_month') ? old('applicant_birth_month') : ($applicant->birth_date_fa ? explode('-', $applicant->birth_date_fa)[1] : null); @endphp
                                            <select required class="form-control" name="applicant_birth_month" id="applicant_birth_month">
                                                <option value="">{{ __('message.month') }}</option>
                                                @foreach(\helper::get_monthes() as $code => $month)
                                                <option value="{{ $code }}" {{$applicant_birth_month == $code ? 'selected' : ''}}>{{ $month }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            @php
                                            $applicant_birth_year = old('applicant_birth_year') ? old('applicant_birth_year') : ($applicant->birth_date_fa ? explode('-', $applicant->birth_date_fa)[0] : null);
                                            $this_year = \Helper::gregorian_to_jalali(date('Y'),date('m'),date('d'))[0];
                                            @endphp
                                            <select required class="form-control" name="applicant_birth_year" id="applicant_birth_year">
                                                <option value="">{{ __('message.year') }}</option>
                                                @for($i=($this_year-12); $i>($this_year-100); $i--)
                                                <option value="{{ $i }}" {{$applicant_birth_year == $i ? 'selected' : ''}}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
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
                        <div class="row justify-content-center">
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <h6>{{ __('message.name_en') }} <span class="text-danger">*</span></h6>
                                    <input required dir="ltr" onblur="if(this.value && !final_latin_name_check(this.value)){$(this).val('')}" onkeypress="return latin_chars_check(event);" class="form-control" value="{{old('spouse_name') ? old('spouse_name') : $applicant->spouse->name}}" type="text" placeholder="{{ __('message.name_en') }}" name="spouse_name">
                                    @error('spouse_name')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <h6>{{ __('message.last_name_en') }} <span class="text-danger">*</span></h6>
                                    <input required dir="ltr" onblur="if(this.value && !final_latin_name_check(this.value)){$(this).val('')}" onkeypress="return latin_chars_check(event);" class="form-control" value="{{old('spouse_last_name') ? old('spouse_last_name') : $applicant->spouse->last_name}}" type="text" placeholder="{{ __('message.last_name_en') }}" name="spouse_last_name">
                                    @error('spouse_last_name')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <h6>{{ __('message.gender') }} <span class="text-danger">*</span></h6>
                                    @php $gender = old('spouse_gender') ? old('spouse_gender') : $applicant->spouse->gender; @endphp
                                    <select required class="form-control" name="spouse_gender">
                                        <option value="">-- {{ __('message.select') }} --</option>
                                        <option value="male" {{$gender == 'male' ? 'selected' : ''}}>{{ __('message.male') }}</option>
                                        <option value="female" {{$gender == 'female' ? 'selected' : ''}}>{{ __('message.female') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <h6>{{ __('message.birth_country') }} <span class="text-danger">*</span></h6>
                                    @php $spouse_birth_country = old('spouse_birth_country') ? old('spouse_birth_country') : $applicant->spouse->birth_country; @endphp
                                    <select required class="form-control country" name="spouse_birth_country" id="spouse_birth_country">
                                        @foreach(\helper::get_countries() as $code => $country)
                                        <option value="{{ $code }}" {{$spouse_birth_country == $code ? 'selected' : ''}}>{{ $country }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <h6>{{ __('message.birth_city') }} <span class="text-danger">*</span></h6>
                                    <input required class="form-control" value="{{old('spouse_birth_city') ? old('spouse_birth_city') : $applicant->spouse->birth_city}}" type="text" placeholder="{{ __('message.birth_city') }}" name="spouse_birth_city">
                                    @error('spouse_birth_city')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-12">
                                <h6>{{ __('message.birth_date_shamsi') }} <span class="text-danger">*</span></h6>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            @php $spouse_birth_day = old('spouse_birth_day') ? old('spouse_birth_day') : ($applicant->spouse->birth_date_fa ? explode('-', $applicant->spouse->birth_date_fa)[2] : null); @endphp
                                            <select required class="form-control" name="spouse_birth_day" id="spouse_birth_day">
                                                <option value="">{{ __('message.day') }}</option>
                                                @for($i=1; $i<=31; $i++)
                                                <option value="{{ sprintf('%02d', $i) }}" {{$spouse_birth_day == $i ? 'selected' : ''}}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4 p-0">
                                        <div class="form-group">
                                            @php $spouse_birth_month = old('spouse_birth_month') ? old('spouse_birth_month') : ($applicant->spouse->birth_date_fa ? explode('-', $applicant->spouse->birth_date_fa)[1] : null); @endphp
                                            <select required class="form-control" name="spouse_birth_month" id="spouse_birth_month">
                                                <option value="">{{ __('message.month') }}</option>
                                                @foreach(\helper::get_monthes() as $code => $month)
                                                <option value="{{ $code }}" {{$spouse_birth_month == $code ? 'selected' : ''}}>{{ $month }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            @php $spouse_birth_year = old('spouse_birth_year') ? old('spouse_birth_year') : ($applicant->spouse->birth_date_fa ? explode('-', $applicant->spouse->birth_date_fa)[0] : null); @endphp
                                            <select required class="form-control" name="spouse_birth_year" id="spouse_birth_year">
                                                <option value="">{{ __('message.year') }}</option>
                                                @for($i=($this_year-12); $i>($this_year-100); $i--)
                                                <option value="{{ $i }}" {{$spouse_birth_year == $i ? 'selected' : ''}}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($applicant->double_register)
                        <div class="row justify-content-center">
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <h6>{{ __('message.mobile') }} <span class="text-danger">*</span></h6>
                                    <input required onblur="if(this.value && !final_phone_check(this.value)){$(this).val('')}" onkeypress="return numeric_check(event);" dir="ltr" class="form-control" value="{{old('spouse_mobile') ? old('spouse_mobile') : $applicant->spouse->mobile}}" type="text" pattern="[0][9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]" placeholder="091..." name="spouse_mobile">
                                    @error('spouse_mobile')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <h6>{{ __('message.email') }} <span class="text-danger">*</span></h6>
                                    <input required dir="ltr" class="form-control" value="{{old('spouse_email') ? old('spouse_email') : $applicant->spouse->email}}" type="email" placeholder="example@email.com" name="spouse_email">
                                    @error('spouse_email')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <h6>{{ __('message.education_degree') }} <span class="text-danger">*</span></h6>
                                    @php $education_degree = old('spouse_education_degree') ? old('spouse_education_degree') : $applicant->spouse->education_degree; @endphp
                                    <select required class="form-control" name="spouse_education_degree">
                                        <option value="">-- {{ __('message.select') }} --</option>
                                        <option value="primary_school_only" {{$education_degree == 'primary_school_only' ? 'selected' : ''}}>{{ __('message.primary_school_only') }}</option>
                                        <option value="high_school_no_degree" {{$education_degree == 'high_school_no_degree' ? 'selected' : ''}}>{{ __('message.high_school_no_degree') }}</option>
                                        <option value="high_school_degree" {{$education_degree == 'high_school_degree' ? 'selected' : ''}}>{{ __('message.high_school_degree') }}</option>
                                        <option value="vocational_school" {{$education_degree == 'vocational_school' ? 'selected' : ''}}>{{ __('message.vocational_school') }}</option>
                                        <option value="some_university_courses" {{$education_degree == 'some_university_courses' ? 'selected' : ''}}>{{ __('message.some_university_courses') }}</option>
                                        <option value="university_degree" {{$education_degree == 'university_degree' ? 'selected' : ''}}>{{ __('message.university_degree') }}</option>
                                        <option value="some_graduate_level_courses" {{$education_degree == 'some_graduate_level_courses' ? 'selected' : ''}}>{{ __('message.some_graduate_level_courses') }}</option>
                                        <option value="masters_degree" {{$education_degree == 'masters_degree' ? 'selected' : ''}}>{{ __('message.masters_degree') }}</option>
                                        <option value="doctorate_level_courses" {{$education_degree == 'doctorate_level_courses' ? 'selected' : ''}}>{{ __('message.doctorate_level_courses') }}</option>
                                        <option value="doctorate_degree" {{$education_degree == 'doctorate_degree' ? 'selected' : ''}}>{{ __('message.doctorate_degree') }}</option>
                                    </select>
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
                                    <label class="m-1 mb-0 text-sm" for="independent_register_{{$adult_child->id}}">{{ sprintf(__('message.independent_register'), number_format(\Helper::price()[$applicant->registration_type]['independent_register']/10)) }}</label>
                                    <input {{$applicant->payment_status == 'paid' ? 'disabled' : ''}} onchange="if($(this).is(':checked')) {$('#extra_data_{{$adult_child->id}}').show(); $('#adult_child_mobile_{{$adult_child->id}}').prop('required', true); $('#adult_child_email_{{$adult_child->id}}').prop('required', true); $('#adult_child_education_degree_{{$adult_child->id}}').prop('required', true);} else {$('#extra_data_{{$adult_child->id}}').hide(); $('#adult_child_mobile_{{$adult_child->id}}').prop('required', false); $('#adult_child_email_{{$adult_child->id}}').prop('required', false); $('#adult_child_education_degree_{{$adult_child->id}}').prop('required', false);}" class="form-check-input" type="checkbox" name="independent_register_{{$adult_child->id}}" id="independent_register_{{$adult_child->id}}" {{$adult_child->independent_register ? 'checked' : ''}}>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center mt-4">
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <h6>{{ __('message.name_en') }} <span class="text-danger">*</span></h6>
                                    <input required dir="ltr" onblur="if(this.value && !final_latin_name_check(this.value)){$(this).val('')}" onkeypress="return latin_chars_check(event);" class="form-control" value="{{old('adult_child_name_'.$adult_child->id) ? old('adult_child_name_'.$adult_child->id) : $adult_child->name}}" type="text" placeholder="{{ __('message.name_en') }}" name="adult_child_name_{{$adult_child->id}}">
                                    @error('adult_child_name_'.$adult_child->id)
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <h6>{{ __('message.last_name_en') }} <span class="text-danger">*</span></h6>
                                    <input required dir="ltr" onblur="if(this.value && !final_latin_name_check(this.value)){$(this).val('')}" onkeypress="return latin_chars_check(event);" class="form-control" value="{{old('adult_child_last_name_'.$adult_child->id) ? old('adult_child_last_name_'.$adult_child->id) : $adult_child->last_name}}" type="text" placeholder="{{ __('message.last_name_en') }}" name="adult_child_last_name_{{$adult_child->id}}">
                                    @error('adult_child_last_name_'.$adult_child->id)
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <h6>{{ __('message.gender') }} <span class="text-danger">*</span></h6>
                                    @php $gender = old('adult_child_gender_'.$adult_child->id) ? old('adult_child_gender_'.$adult_child->id) : $adult_child->gender; @endphp
                                    <select required class="form-control" name="adult_child_gender_{{$adult_child->id}}">
                                        <option value="">-- {{ __('message.select') }} --</option>
                                        <option value="male" {{$gender == 'male' ? 'selected' : ''}}>{{ __('message.male') }}</option>
                                        <option value="female" {{$gender == 'female' ? 'selected' : ''}}>{{ __('message.female') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <h6>{{ __('message.birth_country') }} <span class="text-danger">*</span></h6>
                                    @php $birth_country = old('adult_child_birth_country_'.$adult_child->id) ? old('adult_child_birth_country_'.$adult_child->id) : $adult_child->birth_country; @endphp
                                    <select required class="form-control country" name="adult_child_birth_country_{{$adult_child->id}}">
                                        @foreach(\helper::get_countries() as $code => $country)
                                        <option value="{{ $code }}" {{$birth_country == $code ? 'selected' : ''}}>{{ $country }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <h6>{{ __('message.birth_city') }} <span class="text-danger">*</span></h6>
                                    <input required class="form-control" value="{{old('adult_child_birth_city_'.$adult_child->id) ? old('adult_child_birth_city_'.$adult_child->id) : $adult_child->birth_city}}" type="text" placeholder="{{ __('message.birth_city') }}" name="adult_child_birth_city_{{$adult_child->id}}">
                                    @error('adult_child_birth_city_'.$adult_child->id)
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <h6>{{ __('message.birth_date_shamsi') }} <span class="text-danger">*</span></h6>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            @php $birth_day = old('adult_child_birth_day_'.$adult_child->id) ? old('adult_child_birth_day_'.$adult_child->id) : ($adult_child->birth_date_fa ? explode('-', $adult_child->birth_date_fa)[2] : null); @endphp
                                            <select required class="form-control" name="adult_child_birth_day_{{$adult_child->id}}">
                                                <option value="">{{ __('message.day') }}</option>
                                                @for($i=1; $i<=31; $i++)
                                                <option value="{{ sprintf('%02d', $i) }}" {{$birth_day == $i ? 'selected' : ''}}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4 p-0">
                                        <div class="form-group">
                                            @php $birth_month = old('adult_child_birth_month_'.$adult_child->id) ? old('adult_child_birth_month_'.$adult_child->id) : ($adult_child->birth_date_fa ? explode('-', $adult_child->birth_date_fa)[1] : null); @endphp
                                            <select required class="form-control" name="adult_child_birth_month_{{$adult_child->id}}">
                                                <option value="">{{ __('message.month') }}</option>
                                                @foreach(\helper::get_monthes() as $code => $month)
                                                <option value="{{ $code }}" {{$birth_month == $code ? 'selected' : ''}}>{{ $month }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            @php $birth_year = old('adult_child_birth_year_'.$adult_child->id) ? old('adult_child_birth_year_'.$adult_child->id) : ($adult_child->birth_date_fa ? explode('-', $adult_child->birth_date_fa)[0] : null); @endphp
                                            <select required class="form-control" name="adult_child_birth_year_{{$adult_child->id}}">
                                                <option value="">{{ __('message.year') }}</option>
                                                @for($i=($this_year-17); $i>($this_year-22); $i--)
                                                <option value="{{ $i }}" {{$birth_year == $i ? 'selected' : ''}}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center" style="{{$adult_child->independent_register ? '' : 'display: none;'}}" id="extra_data_{{$adult_child->id}}">
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <h6>{{ __('message.mobile') }} <span class="text-danger">*</span></h6>
                                    <input onblur="if(this.value && !final_phone_check(this.value)){$(this).val('')}" onkeypress="return numeric_check(event);" dir="ltr" class="form-control" value="{{old('adult_child_mobile_'.$adult_child->id) ? old('adult_child_mobile_'.$adult_child->id) : $adult_child->mobile}}" type="text" pattern="[0][9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]" placeholder="091..." name="adult_child_mobile_{{$adult_child->id}}" id="adult_child_mobile_{{$adult_child->id}}" {{$adult_child->independent_register ? 'required' : ''}}>
                                    @error('adult_child_mobile_'.$adult_child->id)
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <h6>{{ __('message.email') }} <span class="text-danger">*</span></h6>
                                    <input dir="ltr" class="form-control" value="{{old('adult_child_email_'.$adult_child->id) ? old('adult_child_email_'.$adult_child->id) : $adult_child->email}}" type="email" placeholder="example@email.com" name="adult_child_email_{{$adult_child->id}}" id="adult_child_email_{{$adult_child->id}}" {{$adult_child->independent_register ? 'required' : ''}}>
                                    @error('adult_child_email_'.$adult_child->id)
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <h6>{{ __('message.education_degree') }} <span class="text-danger">*</span></h6>
                                    @php $education_degree = old('adult_child_education_degree_'.$adult_child->id) ? old('adult_child_education_degree_'.$adult_child->id) : $adult_child->education_degree; @endphp
                                    <select class="form-control" name="adult_child_education_degree_{{$adult_child->id}}" id="adult_child_education_degree_{{$adult_child->id}}" {{$adult_child->independent_register ? 'required' : ''}}>
                                        <option value="">-- {{ __('message.select') }} --</option>
                                        <option value="primary_school_only" {{$education_degree == 'primary_school_only' ? 'selected' : ''}}>{{ __('message.primary_school_only') }}</option>
                                        <option value="high_school_no_degree" {{$education_degree == 'high_school_no_degree' ? 'selected' : ''}}>{{ __('message.high_school_no_degree') }}</option>
                                        <option value="high_school_degree" {{$education_degree == 'high_school_degree' ? 'selected' : ''}}>{{ __('message.high_school_degree') }}</option>
                                        <option value="vocational_school" {{$education_degree == 'vocational_school' ? 'selected' : ''}}>{{ __('message.vocational_school') }}</option>
                                        <option value="some_university_courses" {{$education_degree == 'some_university_courses' ? 'selected' : ''}}>{{ __('message.some_university_courses') }}</option>
                                        <option value="university_degree" {{$education_degree == 'university_degree' ? 'selected' : ''}}>{{ __('message.university_degree') }}</option>
                                        <option value="some_graduate_level_courses" {{$education_degree == 'some_graduate_level_courses' ? 'selected' : ''}}>{{ __('message.some_graduate_level_courses') }}</option>
                                        <option value="masters_degree" {{$education_degree == 'masters_degree' ? 'selected' : ''}}>{{ __('message.masters_degree') }}</option>
                                        <option value="doctorate_level_courses" {{$education_degree == 'doctorate_level_courses' ? 'selected' : ''}}>{{ __('message.doctorate_level_courses') }}</option>
                                        <option value="doctorate_degree" {{$education_degree == 'doctorate_degree' ? 'selected' : ''}}>{{ __('message.doctorate_degree') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
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
                                    <h6>{{ __('message.name_en') }} <span class="text-danger">*</span></h6>
                                    <input required dir="ltr" onblur="if(this.value && !final_latin_name_check(this.value)){$(this).val('')}" onkeypress="return latin_chars_check(event);" class="form-control" value="{{old('child_name_'.$child->id) ? old('child_name_'.$child->id) : $child->name}}" type="text" placeholder="{{ __('message.name_en') }}" name="child_name_{{$child->id}}">
                                    @error('child_name_'.$child->id)
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <h6>{{ __('message.last_name_en') }} <span class="text-danger">*</span></h6>
                                    <input required dir="ltr" onblur="if(this.value && !final_latin_name_check(this.value)){$(this).val('')}" onkeypress="return latin_chars_check(event);" class="form-control" value="{{old('child_last_name_'.$child->id) ? old('child_last_name_'.$child->id) : $child->last_name}}" type="text" placeholder="{{ __('message.last_name_en') }}" name="child_last_name_{{$child->id}}">
                                    @error('child_last_name_'.$child->id)
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <h6>{{ __('message.gender') }} <span class="text-danger">*</span></h6>
                                    @php $gender = old('child_gender_'.$child->id) ? old('child_gender_'.$child->id) : $child->gender; @endphp
                                    <select required class="form-control" name="child_gender_{{$child->id}}">
                                        <option value="">-- {{ __('message.select') }} --</option>
                                        <option value="male" {{$gender == 'male' ? 'selected' : ''}}>{{ __('message.male') }}</option>
                                        <option value="female" {{$gender == 'female' ? 'selected' : ''}}>{{ __('message.female') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <h6>{{ __('message.birth_country') }} <span class="text-danger">*</span></h6>
                                    @php $birth_country = old('child_birth_country_'.$child->id) ? old('child_birth_country_'.$child->id) : $child->birth_country; @endphp
                                    <select required class="form-control country" name="child_birth_country_{{$child->id}}">
                                        @foreach(\helper::get_countries() as $code => $country)
                                        <option value="{{ $code }}" {{$birth_country == $code ? 'selected' : ''}}>{{ $country }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <h6>{{ __('message.birth_city') }} <span class="text-danger">*</span></h6>
                                    <input required class="form-control" value="{{old('child_birth_city_'.$child->id) ? old('child_birth_city_'.$child->id) : $child->birth_city}}" type="text" placeholder="{{ __('message.birth_city') }}" name="child_birth_city_{{$child->id}}">
                                    @error('child_birth_city_'.$child->id)
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <h6>{{ __('message.birth_date_shamsi') }} <span class="text-danger">*</span></h6>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            @php $birth_day = old('child_birth_day_'.$child->id) ? old('child_birth_day_'.$child->id) : ($child->birth_date_fa ? explode('-', $child->birth_date_fa)[2] : null); @endphp
                                            <select required class="form-control" name="child_birth_day_{{$child->id}}">
                                                <option value="">{{ __('message.day') }}</option>
                                                @for($i=1; $i<=31; $i++)
                                                <option value="{{ sprintf('%02d', $i) }}" {{$birth_day == $i ? 'selected' : ''}}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4 p-0">
                                        <div class="form-group">
                                            @php $birth_month = old('child_birth_month_'.$child->id) ? old('child_birth_month_'.$child->id) : ($child->birth_date_fa ? explode('-', $child->birth_date_fa)[1] : null); @endphp
                                            <select required class="form-control" name="child_birth_month_{{$child->id}}">
                                                <option value="">{{ __('message.month') }}</option>
                                                @foreach(\helper::get_monthes() as $code => $month)
                                                <option value="{{ $code }}" {{$birth_month == $code ? 'selected' : ''}}>{{ $month }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            @php $birth_year = old('child_birth_year_'.$child->id) ? old('child_birth_year_'.$child->id) : ($child->birth_date_fa ? explode('-', $child->birth_date_fa)[0] : null); @endphp
                                            <select required class="form-control" name="child_birth_year_{{$child->id}}">
                                                <option value="">{{ __('message.year') }}</option>
                                                @for($i=($this_year); $i>($this_year-19); $i--)
                                                <option value="{{ $i }}" {{$birth_year == $i ? 'selected' : ''}}>{{ $i }}</option>
                                                @endfor
                                            </select>
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
        <div class="row justify-content-center mt-4">
            <div class="col-12">
                <button type="submit" class="btn bg-gradient-dark btn-md mb-4 w-100">{{ __('message.send') }}</button>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('.country').select2();
    });
</script>
@endsection