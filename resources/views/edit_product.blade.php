@extends('layouts.user_type.auth')

@section('content')

<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header pb-0 px-3">
            <h6 class="mb-0">{{ __('message.edit_product') }}</h6>
        </div>
        <div class="card-body pt-4 p-3">
            <form action="{{url('edit_product', $product->id)}}" method="POST" role="form text-left">
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
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">{{ __('message.title') }}</label>
                            <input required class="form-control" value="{{$product->title}}" type="text" placeholder="{{ __('message.title') }}" name="title">
                            @error('title')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">{{ __('message.nilgam_safar_code') }}</label>
                            <input required class="form-control" value="{{$product->nilgam_safar_code}}" type="text" placeholder="{{ __('message.nilgam_safar_code') }}" name="nilgam_safar_code">
                            @error('nilgam_safar_code')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">{{ __('message.crm_code') }}</label>
                            <input required class="form-control" value="{{$product->crm_code}}" type="text" placeholder="{{ __('message.crm_code') }}" name="crm_code">
                            @error('crm_code')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">GUID {{ __('message.phone_appointment') }}</label>
                            <input required class="form-control" value="{{$product->phone_guid}}" type="text" placeholder="GUID {{ __('message.phone_appointment') }}" name="phone_guid">
                            @error('phone_guid')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Owner GUID {{ __('message.phone_appointment') }}</label>
                            <input required class="form-control" value="{{$product->phone_owner_guid}}" type="text" placeholder="Owner GUID {{ __('message.phone_appointment') }}" name="phone_owner_guid">
                            @error('phone_owner_guid')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label class="form-control-label">{{ __('message.capacity') }} {{ __('message.phone_appointment') }}</label>
                            <input required class="form-control" value="{{$product->phone_cap}}" type="number" placeholder="{{ __('message.capacity') }}" name="phone_cap">
                            @error('phone_cap')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-control-label">{{ __('message.price') }} {{ __('message.phone_appointment') }}</label>
                            <input required class="form-control" value="{{$product->phone_price}}" type="number" placeholder="{{ __('message.price') }}" name="phone_price">
                            @error('phone_price')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label class="form-control-label">{{ __('message.currency') }} {{ __('message.phone_appointment') }}</label>
                            <select required class="form-control" name="phone_currency">
                                <option value="IRR">{{ __('message.IRR').' (IRR)' }}</option>
                            </select>
                            @error('phone_currency')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">GUID {{ __('message.onsite') }}</label>
                            <input required class="form-control" value="{{$product->onsite_guid}}" type="text" placeholder="GUID {{ __('message.onsite') }}" name="onsite_guid">
                            @error('onsite_guid')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Owner GUID {{ __('message.onsite') }}</label>
                            <input required class="form-control" value="{{$product->onsite_owner_guid}}" type="text" placeholder="Owner GUID {{ __('message.onsite') }}" name="onsite_owner_guid">
                            @error('onsite_owner_guid')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label class="form-control-label">{{ __('message.capacity') }} {{ __('message.onsite') }}</label>
                            <input required class="form-control" value="{{$product->onsite_cap}}" type="number" placeholder="{{ __('message.capacity') }}" name="onsite_cap">
                            @error('onsite_cap')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-control-label">{{ __('message.price') }} {{ __('message.onsite') }}</label>
                            <input required class="form-control" value="{{$product->onsite_price}}" type="number" placeholder="{{ __('message.price') }}" name="onsite_price">
                            @error('onsite_price')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label class="form-control-label">{{ __('message.currency') }} {{ __('message.onsite') }}</label>
                            <select required class="form-control" name="onsite_currency">
                                <option value="IRR">{{ __('message.IRR').' (IRR)' }}</option>
                            </select>
                            @error('onsite_currency')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn bg-gradient-dark btn-md mt-4 mb-4">{{ __('message.edit_product') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection