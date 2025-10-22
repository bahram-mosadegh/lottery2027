@extends('layouts.user_type.auth')

@section('content')

<div>
    <div id="header_alerts">
    @if(session('success'))
        <div class="mt-3 alert alert-success alert-dismissible fade show" id="alert-success" role="alert">
            <span class="alert-text text-white">
            {{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                <i class="fa fa-close" aria-hidden="true"></i>
            </button>
        </div>
        @php Session::forget('success'); @endphp
    @endif
    @if(session('error'))
        <div class="mt-3 alert alert-primary alert-dismissible fade show" role="alert">
            <span class="alert-text text-white">
            {{ session('error') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                <i class="fa fa-close" aria-hidden="true"></i>
            </button>
        </div>
        @php Session::forget('error'); @endphp
    @endif
    @if($errors->any())
        <div class="mt-3 alert alert-primary alert-dismissible fade show" role="alert">
            <span class="alert-text text-white">
            {{$errors->first()}}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                <i class="fa fa-close" aria-hidden="true"></i>
            </button>
        </div>
    @endif
    </div>
    <div class="container-fluid py-4">
        <div class="card mb-4">
            <div class="card-header pb-3">
                <h5 class="mb-0">{{ __('message.onsite_registeration') }}</h5>
            </div>
            <div class="card-body px-3 pt-0 pb-3">
                <form target="_blank" action="{{url('step_zero')}}" method="GET">
                    <input type="hidden" name="registration_type" value="{{auth()->user()->role == 'agent' ? 'agent' : 'onsite'}}">
                    <div class="row">
                        <label class="form-control-label pt-2">{{ __('message.mobile') }}</label>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input onblur="if(this.value && !final_phone_check(this.value)){$(this).val('')}" onkeypress="return numeric_check(event);" required style="direction: ltr;" class="form-control" value="" type="text" pattern="[0][9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]" placeholder="091..." name="mobile">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <button type="submit" class="btn bg-gradient-dark" style="width: 100%;">{{ __('message.submit') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body px-3 pt-4 pb-3">
              <div class="table-responsive p-0">
                {!! $dataTable->table(['class' => 'datatable-new table align-items-center mb-0 display responsive nowrap'], true) !!}
              </div>
            </div>
        </div>
    </div>
</div>

{!! $dataTable->scripts() !!}
@endsection