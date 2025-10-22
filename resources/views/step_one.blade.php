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
    @if(date('Y-m-d') >= env('END_REG_DATE'))
    <div class="alert alert-warning text-white" role="alert">
        {{__('message.registration_ended')}}
    </div>
    @endif
    <div class="card">
        <div class="card-header pb-0 px-3">
            <h4 class="mb-0 text-center">{{ __('message.register_for_lottery') }}</h4>
        </div>
        <div class="card-body pt-4 p-3">
            <form method="POST" action="{{url('step_one')}}" id="step_one_form">
                @csrf
                <input type="hidden" name="applicant_id" value="{{$applicant ? $applicant->id : null}}">
                <div class="row justify-content-center mt-2 radio-to-button">
                    <div class="col-lg-4 col-md-6 col-6">
                        <div class="form-check">
                            <input required class="form-check-input" type="radio" name="marital" value="single" id="single" {{old('marital') == 'single' || ($applicant && $applicant->marital == 'single') ? 'checked' : ''}}>
                            <label class="custom-control-label" for="single">{{ __('message.single') }}</label>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-6">
                        <div class="form-check">
                            <input required class="form-check-input" type="radio" name="marital" value="married" id="married" {{old('marital') == 'married' || ($applicant && $applicant->marital == 'married') ? 'checked' : ''}}>
                            <label class="custom-control-label" for="married">{{ __('message.married') }}</label>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center mt-2 radio-to-button">
                    <div class="col-lg-4 col-md-6 col-6">
                        <div class="form-check">
                            <input required class="form-check-input" type="radio" name="have_child" value="have_not_child" id="have_not_child" {{old('have_child') == 'have_not_child' || ($applicant && !$applicant->adult_children_count && !$applicant->children_count) ? 'checked' : ''}}>
                            <label class="custom-control-label" for="have_not_child">{{ __('message.have_not_child') }}</label>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-6">
                        <div class="form-check">
                            <input required class="form-check-input" type="radio" name="have_child" value="have_child" id="have_child" {{old('have_child') == 'have_child' || ($applicant && ($applicant->adult_children_count || $applicant->children_count)) ? 'checked' : ''}}>
                            <label class="custom-control-label" for="have_child">{{ __('message.have_child') }}</label>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center hide-child" style="{{old('have_child') == 'have_child' || ($applicant && ($applicant->adult_children_count || $applicant->children_count)) ? '' : 'display: none;'}}">
                    <div class="col-lg-4 col-md-6 mt-4">
                        <div class="form-group">
                            <h6>{{ __('message.children_count') }} <span class="text-danger">*</span></h6>
                            <select required class="form-control" name="children_count" id="children_count">
                                <option value="">-- {{ __('message.select') }} --</option>
                                <option value="0" {{old('children_count') == 0 || ($applicant && $applicant->children_count == 0) ? 'selected' : ''}}>{{ __('message.no_children') }}</option>
                                @for($i=1; $i <= 6; $i++)
                                <option value="{{$i}}" {{old('children_count') == $i || ($applicant && $applicant->children_count == $i) ? 'selected' : ''}}>{{$i}}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mt-4">
                        <div class="form-group">
                            <h6>{{ __('message.adult_children_count') }} <span class="text-danger">*</span></h6>
                            <select required class="form-control" name="adult_children_count" id="adult_children_count">
                                <option value="">-- {{ __('message.select') }} --</option>
                                <option value="0" {{old('adult_children_count') == 0 || ($applicant && $applicant->adult_children_count == 0) ? 'selected' : ''}}>{{ __('message.no_adult_children') }}</option>
                                @for($i=1; $i <= 6; $i++)
                                <option value="{{$i}}" {{old('adult_children_count') == $i || ($applicant && $applicant->adult_children_count == $i) ? 'selected' : ''}}>{{$i}}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center mt-4 hide-checkbox" style="{{old('marital') == 'married' || ($applicant && $applicant->marital == 'married') ? '' : 'display: none;'}}">
                    <div class="col-lg-8 col-12">
                        <div class="form-check" style="padding-right: 1.73em;">
                            <input class="form-check-input" type="checkbox" name="double_register" value="1" id="double_register" {{old('double_register') == '1' || ($applicant && $applicant->double_register) ? 'checked' : ''}}>
                            <label type="button" class="custom-control-label" for="double_register"><h6>{{ __('message.double_register') }}</h6></label>
                        </div>
                        <p class="text-justify" style="line-height: 1.2;">{{__('message.double_register_desc')}}</p>
                    </div>
                </div>
                <div class="row justify-content-center mt-4">
                    <div class="col-lg-4 col-6 text-center">
                        <h5>{{__('message.register_price')}}:</h5>
                    </div>
                    <div class="col-lg-4 col-6 text-center">
                        <h5 class="text-success" id="register_price">0 {{__('message.IRT')}}</h5>
                    </div>
                </div>
                <div class="row justify-content-center mt-4">
                    <div class="col-lg-8 col-12">
                        <button type="submit" class="btn bg-gradient-dark btn-md mb-4 w-100">{{ __('message.send') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        register_price_calc();
    });

    $('#step_one_form').on('submit', function () {
        if ($('#have_child').is(':checked')) {
            if ($('#children_count').val() == 0 && $('#adult_children_count').val() == 0) {
                alert('{{__('message.children_count_have_to_be_1_at_least')}}');
                return false;
            }
        }

        return true;
    });

    $('#married').on('change', function () {
        if ($(this).is(':checked')) {
            $('.hide-checkbox').show();
        }
        register_price_calc();
    });

    $('#single').on('change', function () {
        if ($(this).is(':checked')) {
            $('.hide-checkbox').hide();
        }
        register_price_calc();
    });

    $('#have_child').on('change', function () {
        if ($(this).is(':checked')) {
            $('.hide-child').show();
            $('#children_count').val('');
            $('#adult_children_count').val('');
        }
        register_price_calc();
    });

    $('#have_not_child').on('change', function () {
        if ($(this).is(':checked')) {
            $('.hide-child').hide();
            $('#children_count').val(0);
            $('#adult_children_count').val(0);
        }
        register_price_calc();
    });

    $('#double_register').on('change', function () {
        register_price_calc();
    });

    $('#children_count').on('change', function () {
        register_price_calc();
    });

    $('#adult_children_count').on('change', function () {
        register_price_calc();
    });

    function register_price_calc() {
        @php $price = \Helper::price()[session('registration_type')?session('registration_type'):'online']; @endphp
        var total = 0;
        if ($('#single').is(':checked')) {
            total = (total*1) + {{$price['single']/10}};
        }
        if ($('#married').is(':checked')) {
            total = (total*1) + {{$price['married']/10}};
            if ($('#double_register').is(':checked')) {
                total = (total*1) + {{$price['double_register']/10}};
                total = (total*1) + $('#adult_children_count').val()*{{$price['adult_child']/10}};
                total = (total*1) + $('#children_count').val()*{{$price['child']/10}};
            }
        }
        total = (total*1) + $('#adult_children_count').val()*{{$price['adult_child']/10}};
        total = (total*1) + $('#children_count').val()*{{$price['child']/10}};
        animate_price('register_price', 0, total, 500)
        // $('#register_price').html(number_separator(total)+' {{__('message.IRT')}}');
    }

    function animate_price(id, start, end, duration) {
        if (start === end) return;
        var range = end - start;
        var current = start;
        var increment = end > start? 10000 : -10000;
        var stepTime = Math.abs(Math.floor(duration * increment / range));
        var timer = setInterval(function() {
            if (current + increment > end) {
                current = end;
            } else {
                current += (increment);
            }
            
            $('#'+id).html(number_separator(current)+' {{__('message.IRT')}}');
            if (current == end) {
                clearInterval(timer);
            }
        }, stepTime);
    }

</script>

<style type="text/css">
    .radio-to-button .form-check {
      float: left;
      margin: 0 5px 0 0;
      width: 100%;
      height: 60px;
      position: relative;
      text-align: center;
    }

    .radio-to-button label,
    .radio-to-button input {
      display: block;
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      border-radius: 0.5rem;
      font-size: 22px;
      font-weight: 100;
    }

    .radio-to-button input[type="radio"] {
      opacity: 0;
      z-index: 1;
      right:50px;
    }

    .radio-to-button input[type="radio"]:checked+label,
    .Checked+label {
      background: #0047ab;
      color: white;
    }

    .radio-to-button label {
      padding: 5px;
      border: 1px solid #CCC;
      cursor: pointer;
      z-index: 90;
    }

    .radio-to-button label:hover {
      background: #DDD;
    }

    .form-check .form-check-input {
        float: right;
        margin-right: -1.73em;
    }
</style>
@endsection