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
        <div class="card">
            <div class="card-header pb-3">
                <div class="d-flex flex-row justify-content-between">
                    <h5 class="mb-0">{{ __('message.products') }}</h5>
                    <a href="{{ url('add_product') }}" class="btn bg-gradient-primary btn-sm mb-0" type="button">+&nbsp; {{ __('message.add') }}  {{ __('message.product') }}</a>
                </div>
            </div>
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