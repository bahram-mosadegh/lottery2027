@extends('layouts.user_type.auth')

@section('content')

<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header pb-0 px-3">
            <h6 class="mb-0">{{ __('message.edit_coupon') }}</h6>
        </div>
        <div class="card-body pt-4 p-3">
            <form action="{{url('edit_coupon', $coupon->id)}}" method="POST" role="form text-left">
                @csrf
                @if($errors->any())
                    <div class="mt-3 alert alert-primary alert-dismissible fade show" role="alert">
                        <span class="alert-text text-white">
                        {{$errors->first()}}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <i class="fa fa-close" aria-hidden="true"></i>
                        </button>
                    </div>
                @endif
                @if(session('success'))
                    <div class="mt-3 alert alert-success alert-dismissible fade show" id="alert-success" role="alert">
                        <span class="alert-text text-white">
                        {{ session('success') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <i class="fa fa-close" aria-hidden="true"></i>
                        </button>
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-control-label">{{ __('message.code') }}</label>
                            <input required class="form-control" value="{{$coupon->code}}" type="text" placeholder="{{ __('message.code') }}" name="code">
                            @error('code')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-control-label">{{ __('message.amount') }}</label>
                            <input required class="form-control" value="{{$coupon->ammount}}" type="number" placeholder="{{ __('message.amount') }}" name="ammount">
                            @error('ammount')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-control-label">{{ __('message.type') }}</label>
                            <select required class="form-control" name="type">
                                <option value="percent" {{$coupon->type == 'percent' ? 'selected' : ''}}>{{ __('message.percent') }}</option>
                                <option value="discount" {{$coupon->type == 'discount' ? 'selected' : ''}}>{{ __('message.discount') }}</option>
                            </select>
                            @error('phone_currency')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-control-label">{{ __('message.expire_date') }}</label>
                            <input dir="ltr" required class="form-control" value="{{$coupon->expire_date}}" type="text" placeholder="{{ __('message.expire_date') }}" name="expire_date">
                            @error('expire_date')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn bg-gradient-dark btn-md mt-4 mb-4">{{ __('message.edit_coupon') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection