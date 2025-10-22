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
            <h4 class="mb-0 text-center">{{ __('message.bill') }}</h4>
        </div>
        <div class="card-body pt-4 p-3">
            @if($prices['marital'])
            <div class="row justify-content-center">
                <div class="col-lg-3 col-6 text-center">
                    <h6>{{__('message.register_price')}} {{__('message.'.$applicant->marital)}}</h6>
                </div>
                <div class="col-lg-3 col-6 text-center">
                    <h6>{{number_format($prices['marital']/10)}} {{__('message.IRT')}}</h6>
                </div>
            </div>
            @endif
            @if($prices['children'])
            <div class="row justify-content-center">
                <div class="col-lg-3 col-6 text-center">
                    <h6>{{__('message.register_price')}} {{__('message.children')}}</h6>
                </div>
                <div class="col-lg-3 col-6 text-center">
                    <h6>{{number_format($prices['children']/10)}} {{__('message.IRT')}}</h6>
                </div>
            </div>
            @endif
            @if($prices['double_register'])
            <div class="row justify-content-center">
                <div class="col-lg-3 col-6 text-center">
                    <h6>{{__('message.register_price')}} {{__('message.spouse_double_register')}}</h6>
                </div>
                <div class="col-lg-3 col-6 text-center">
                    <h6>{{number_format($prices['double_register']/10)}} {{__('message.IRT')}}</h6>
                </div>
            </div>
            @endif
            @if($prices['independent_register'])
            <div class="row justify-content-center">
                <div class="col-lg-3 col-6 text-center">
                    <h6>{{__('message.register_price')}} {{__('message.children_independent_register')}}</h6>
                </div>
                <div class="col-lg-3 col-6 text-center">
                    <h6>{{number_format($prices['independent_register']/10)}} {{__('message.IRT')}}</h6>
                </div>
            </div>
            @endif
            <hr class="horizontal dark m-0 mt-1">
            <div class="row justify-content-center mt-4">
                <div class="col-lg-3 col-6 text-center">
                    <h5>{{__('message.total')}}</h5>
                </div>
                <div class="col-lg-3 col-6 text-center">
                    <h5 class="text-success">{{number_format($applicant->price/10)}} {{__('message.IRT')}}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center mt-4">
        <div class="col-12">
            <a href="{{url('payment/'.($applicant ? $applicant->id : ''))}}" type="button" class="btn bg-gradient-success btn-md mb-4 w-100">{{ __('message.payment') }}</a>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('.country').select2();
    });
</script>
@endsection