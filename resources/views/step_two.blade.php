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
    <div class="card">
        <div class="card-header pb-0 px-3">
            <h4 class="mb-0 text-center">{{ __('message.applicant_information') }}</h4>
        </div>
        <div class="card-body pt-4 p-3">
            <form method="POST" action="{{url('step_two')}}">
                @csrf
                <input type="hidden" name="applicant_id" value="{{$applicant ? $applicant->id : null}}">
                <div class="row justify-content-center">
                    <div class="col-lg-3 col-md-4 mt-4">
                        <div class="form-group">
                            <h6>{{ __('message.email') }} <span class="text-danger">*</span></h6>
                            <input required dir="ltr" class="form-control" value="{{old('email') ? old('email') : $applicant->email}}" type="email" placeholder="example@email.com" name="email">
                            @error('email')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 mt-4">
                        <div class="form-group">
                            <h6>{{ __('message.education_degree') }} <span class="text-danger">*</span></h6>
                            @php $education_degree = old('education_degree') ? old('education_degree') : $applicant->education_degree; @endphp
                            <select required class="form-control" name="education_degree">
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
                    <div class="col-lg-3 col-md-4 mt-4">
                        <div class="form-group">
                            <h6>{{ __('message.marital_status') }} <span class="text-danger">*</span></h6>
                            @php $marital_status = old('marital_status') ? old('marital_status') : $applicant->marital_status; @endphp
                            <select required class="form-control" name="marital_status">
                                <option value="">-- {{ __('message.select') }} --</option>
                                @if($applicant->marital == 'single')
                                <option value="unmarried" {{$marital_status == 'unmarried' ? 'selected' : ''}}>{{ __('message.unmarried') }}</option>
                                <option value="divorced" {{$marital_status == 'divorced' ? 'selected' : ''}}>{{ __('message.divorced') }}</option>
                                <option value="widowed" {{$marital_status == 'widowed' ? 'selected' : ''}}>{{ __('message.widowed') }}</option>
                                @else
                                <option value="married_not_us_citizen" {{$marital_status == 'married_not_us_citizen' ? 'selected' : ''}}>{{ __('message.married_not_us_citizen') }}</option>
                                <option value="married_us_citizen" {{$marital_status == 'married_us_citizen' ? 'selected' : ''}}>{{ __('message.married_us_citizen') }}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h5 class="mb-0 text-center pt-4">{{ __('message.residence_information') }}</h5>
                    </div>
                </div>
                <hr class="horizontal dark m-0 mt-1">
                <div class="row justify-content-center">
                    <div class="col-lg-3 col-md-3 mt-4">
                        <div class="form-group">
                            <h6>{{ __('message.residence_country') }} <span class="text-danger">*</span></h6>
                            @php $residence_country = old('residence_country') ? old('residence_country') : $applicant->residence_country; @endphp
                            <select required class="form-control" name="residence_country" id="residence_country">
                                @foreach(\helper::get_countries() as $code => $country)
                                <option value="{{ $code }}" {{$residence_country == $code ? 'selected' : ''}}>{{ $country }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 mt-4">
                        <div class="form-group">
                            <h6>{{ __('message.residence_state') }} <span class="text-danger">*</span></h6>
                            <input required class="form-control" value="{{old('residence_state') ? old('residence_state') : $applicant->residence_state}}" type="text" placeholder="{{ __('message.residence_state') }}" name="residence_state">
                            @error('residence_state')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 mt-4">
                        <div class="form-group">
                            <h6>{{ __('message.residence_city') }} <span class="text-danger">*</span></h6>
                            <input required class="form-control" value="{{old('residence_city') ? old('residence_city') : $applicant->residence_city}}" type="text" placeholder="{{ __('message.residence_city') }}" name="residence_city">
                            @error('residence_city')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 mt-4">
                        <div class="form-group">
                            <h6>{{ __('message.residence_street') }} <span class="text-danger">*</span></h6>
                            <input required class="form-control" value="{{old('residence_street') ? old('residence_street') : $applicant->residence_street}}" type="text" placeholder="{{ __('message.residence_street') }}" name="residence_street">
                            @error('residence_street')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-3 col-md-3 mt-4">
                        <div class="form-group">
                            <h6>{{ __('message.residence_alley') }} <span class="text-danger">*</span></h6>
                            <input required class="form-control" value="{{old('residence_alley') ? old('residence_alley') : $applicant->residence_alley}}" type="text" placeholder="{{ __('message.residence_alley') }}" name="residence_alley">
                            @error('residence_alley')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 mt-4">
                        <div class="form-group">
                            <h6>{{ __('message.residence_no') }} <span class="text-danger">*</span></h6>
                            <input required class="form-control" value="{{old('residence_no') ? old('residence_no') : $applicant->residence_no}}" type="text" placeholder="{{ __('message.residence_no') }}" name="residence_no">
                            @error('residence_no')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 mt-4">
                        <div class="form-group">
                            <h6>{{ __('message.residence_unit') }} <span class="text-danger">*</span></h6>
                            <input required class="form-control" value="{{old('residence_unit') ? old('residence_unit') : $applicant->residence_unit}}" type="text" placeholder="{{ __('message.residence_unit') }}" name="residence_unit">
                            @error('residence_unit')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 mt-4">
                        <div class="form-group">
                            <h6>{{ __('message.residence_postal_code') }} <span class="text-danger">*</span></h6>
                            <input required class="form-control" value="{{old('residence_postal_code') ? old('residence_postal_code') : $applicant->residence_postal_code}}" placeholder="{{ __('message.residence_postal_code') }}" name="residence_postal_code" id="residence_postal_code">
                            @error('residence_postal_code')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center mt-4">
                    <div class="col-lg-9 col-12">
                        <button type="submit" class="btn bg-gradient-dark btn-md mb-4 w-100">{{ __('message.send') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#residence_country').select2();
    });
    $('#residence_postal_code').on('keypress', function () {
        if ($('#residence_country').val() == 'Iran') {
            $(this).prop('pattern', '\\d*');
            $(this).prop('maxLength', 10);
            $(this).prop('minLength', 10);
        } else {
            $(this).removeAttr('pattern');
            $(this).removeAttr('maxlength');
            $(this).removeAttr('minlength');
        }
    });
</script>
@endsection