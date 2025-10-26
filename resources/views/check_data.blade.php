@extends('layouts.user_type.auth')

@section('content')

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-6 col-12">
            <div class="card">
                <div class="card-body pt-4 p-3">
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
                    @if($data['id'])
                    <div class="row justify-content-center">
                        <div class="col-12 text-center">
                            <a target="_blank" href="{{$data['link']}}">
                                <img width="100%" src="{{$data['image']}}" style="border-radius: 10px;">
                            </a>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-6 text-center">
                            <form action="{{url('check_data/'.$image)}}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{$data['id']}}">
                                <input type="hidden" name="type" value="{{$data['type']}}">
                                <input type="hidden" name="{{$image}}_status" value="accepted">
                                <button type="submit" class="w-100 btn bg-gradient-success btn-lg mt-4 mb-4">{{ __('message.accept') }}</button>
                            </form>
                        </div>
                        <div class="col-6 text-center">
                            <form action="{{url('check_data/'.$image)}}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{$data['id']}}">
                                <input type="hidden" name="type" value="{{$data['type']}}">
                                <input type="hidden" name="{{$image}}_status" value="rejected">
                                <button type="submit" class="w-100 btn bg-gradient-danger btn-lg mt-4 mb-4">{{ __('message.reject') }}</button>
                            </form>
                        </div>
                    </div>
                    @else
                    <div class="row justify-content-center">
                        <div class="col-12 text-center">
                            <p class="text-secondary">{{__('message.no_image_found')}}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
            
</div>

@endsection