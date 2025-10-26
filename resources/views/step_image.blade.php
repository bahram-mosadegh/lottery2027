@extends('layouts.user_type.guest')

@section('content')
<style type="text/css">
    .cross {
        position: relative;
        width: 200px;
        height: 200px;
    }.cross::after {
        pointer-events: none;
        content: "";
        position: absolute;
        top: 0; bottom: 0; left: 0; right: 0;
    }
    .cross2::after {
      background:
        linear-gradient(to top left, transparent 49%, rgb(255,0,0) 49%, rgb(255,0,0) 51%, transparent 51%),
        linear-gradient(to top right, transparent 49%, rgb(255,0,0) 49%, rgb(255,0,0) 51%, transparent 51%);
      opacity: 0.7;
      border-radius: 10px;
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
    <div id="face_image_alert" class="d-none">
        <div class="alert alert-danger text-white" role="alert">
            {{__('message.upload_new_face_image')}}
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-12">
                        <img width="100%" src="{{asset('assets/img/lottery_image.png')}}" style="border-radius: 10px;">
                    </div>
                    <div class="col-md-8 col-12 mt-4">
                        <h5 class="mb-3">{{__('message.face_image_guide_title')}}</h5>
                        <ul>
                            <li>از فرستادن عکس ۴*۳ جدا خودداری کنید</li>
                            <li>عکس باید تمام رخ و مستقیم رو به دوربین باشد</li>
                            <li>پس زمینه عکس باید سفید باشد</li>
                            <li>عکس باید واضح، بدون سایه، تاری و کدری و ویرایش فتوشاپی باشد</li>
                            <li>عکس باید در حالت طبیعی و خنثی گرفته شود و نباید لبخند، خنده و نمایی از دندان‌ها در آن مشخص شود</li>
                            <li>در هنگام عکاسی از نوزاد، کودک باید بیدار و دارای چشمانی باز باشد و بدون همراهی والد گرفته شود</li>
                            <li>فرد نباید در هنگام عکاسی عینک داشته باشد</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
        @php $face_image_alert = false; @endphp
    @if($applicant->face_image_status == 'rejected')
    <div class="card mt-4">
        <div class="card-body pt-4 p-3">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-2 col-12">
                    <div class="row text-center">
                        <h4>{{ucwords(strtolower($applicant->name))}}</h4>
                        <h4>{{ucwords(strtolower($applicant->last_name))}}</h4>
                    </div>
                </div>
                <div class="col-lg-4 col-12 text-center mb-4 d-flex justify-content-center">
                    <div class="cross cross2">
                        <img width="200px" height="200px" src="{{url('uploads', $applicant->face_image)}}" style="border-radius: 10px;">
                    </div>
                </div>
                <div class="col-lg-3 col-12">
                    <div class="text-center">
                        <h5>{{ __('message.face_image') }} {{ __('message.new') }}<span class="text-danger">*</span></h5>
                        <form action="{{url('upload_file')}}" class="form-control dropzone face_image" id="applicant_face">
                        @csrf
                        <input type="hidden" name="type" value="face">
                        <input type="hidden" name="person" value="applicants">
                        <input type="hidden" name="applicant_id" value="{{$applicant ? $applicant->id : null}}">
                        <div class="fallback">
                            <input name="file" type="file" />
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
        @php $face_image_alert = true; @endphp
    @endif
    @if($applicant->spouse && $applicant->spouse->face_image_status == 'rejected')
    <div class="card mt-4">
        <div class="card-body pt-4 p-3">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-2 col-12">
                    <div class="row text-center">
                        <h4>{{ucwords(strtolower($applicant->spouse->name))}}</h4>
                        <h4>{{ucwords(strtolower($applicant->spouse->last_name))}}</h4>
                    </div>
                </div>
                <div class="col-lg-4 col-12 text-center mb-4 d-flex justify-content-center">
                    <div class="cross cross2">
                        <img width="200px" height="200px" src="{{url('uploads', $applicant->spouse->face_image)}}" style="border-radius: 10px;">
                    </div>
                </div>
                <div class="col-lg-3 col-12">
                    <div class="text-center">
                        <h5>{{ __('message.face_image') }} {{ __('message.new') }}<span class="text-danger">*</span></h5>
                        <form action="{{url('upload_file')}}" class="form-control dropzone face_image" id="spouse_face">
                        @csrf
                        <input type="hidden" name="type" value="face">
                        <input type="hidden" name="person" value="spouses">
                        <input type="hidden" name="id" value="{{$applicant->spouse->id}}">
                        <input type="hidden" name="applicant_id" value="{{$applicant ? $applicant->id : null}}">
                        <div class="fallback">
                            <input name="file" type="file" />
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
        @php $face_image_alert = true; @endphp
    @endif
    @foreach($applicant->adult_children as $index => $adult_child)
        @if($adult_child->face_image_status == 'rejected')
        <div class="card mt-4">
            <div class="card-body pt-4 p-3">
                <div class="row justify-content-center align-items-center">
                    <div class="col-lg-2 col-12">
                        <div class="row text-center">
                            <h4>{{ucwords(strtolower($adult_child->name))}}</h4>
                            <h4>{{ucwords(strtolower($adult_child->last_name))}}</h4>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12 text-center mb-4 d-flex justify-content-center">
                        <div class="cross cross2">
                            <img width="200px" height="200px" src="{{url('uploads', $adult_child->face_image)}}" style="border-radius: 10px;">
                        </div>
                    </div>
                    <div class="col-lg-3 col-12">
                        <div class="text-center">
                            <h5>{{ __('message.face_image') }} {{ __('message.new') }}<span class="text-danger">*</span></h5>
                            <form action="{{url('upload_file')}}" class="form-control dropzone face_image" id="adult_child_face_{{$index}}">
                            @csrf
                            <input type="hidden" name="type" value="face">
                            <input type="hidden" name="person" value="adult_children">
                            <input type="hidden" name="id" value="{{$adult_child->id}}">
                            <input type="hidden" name="applicant_id" value="{{$applicant ? $applicant->id : null}}">
                            <div class="fallback">
                                <input name="file" type="file" />
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            @php $face_image_alert = true; @endphp
        @endif
    @endforeach
    @foreach($applicant->children as $index => $child)
        @if($child->face_image_status == 'rejected')
        <div class="card mt-4">
            <div class="card-body pt-4 p-3">
                <div class="row justify-content-center align-items-center">
                    <div class="col-lg-2 col-12">
                        <div class="row text-center">
                            <h4>{{ucwords(strtolower($child->name))}}</h4>
                            <h4>{{ucwords(strtolower($child->last_name))}}</h4>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12 text-center mb-4 d-flex justify-content-center">
                        <div class="cross cross2">
                            <img width="200px" height="200px" src="{{url('uploads', $child->face_image)}}" style="border-radius: 10px;">
                        </div>
                    </div>
                    <div class="col-lg-3 col-12">
                        <div class="text-center">
                            <h5>{{ __('message.face_image') }} {{ __('message.new') }}<span class="text-danger">*</span></h5>
                            <form action="{{url('upload_file')}}" class="form-control dropzone face_image" id="child_face_{{$index}}">
                            @csrf
                            <input type="hidden" name="type" value="face">
                            <input type="hidden" name="person" value="children">
                            <input type="hidden" name="id" value="{{$child->id}}">
                            <input type="hidden" name="applicant_id" value="{{$applicant ? $applicant->id : null}}">
                            <div class="fallback">
                                <input name="file" type="file" />
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            @php $face_image_alert = true; @endphp
        @endif
    @endforeach
    <div id="passport_image_alert" class="d-none">
        <div class="alert alert-danger text-white mt-4" role="alert">
            {{__('message.upload_new_passport_image')}}
        </div>
    </div>
        @php $passport_image_alert = false; @endphp
    @if($applicant->passport_image_status == 'rejected')
    <div class="card mt-4">
        <div class="card-body pt-4 p-3">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-2 col-12">
                    <div class="row text-center">
                        <h4>{{ucwords(strtolower($applicant->name))}}</h4>
                        <h4>{{ucwords(strtolower($applicant->last_name))}}</h4>
                    </div>
                </div>
                <div class="col-lg-4 col-12 text-center mb-4 d-flex justify-content-center">
                    <div class="cross cross2">
                        <img width="200px" height="200px" src="{{url('uploads', $applicant->passport_image)}}" style="border-radius: 10px;">
                    </div>
                </div>
                <div class="col-lg-3 col-12">
                    <div class="text-center">
                        <h5>{{ __('message.passport_image') }} {{ __('message.new') }}<span class="text-danger">*</span></h5>
                        <form action="{{url('upload_file')}}" class="form-control dropzone passport_image" id="applicant_passport">
                        @csrf
                        <input type="hidden" name="type" value="passport">
                        <input type="hidden" name="person" value="applicants">
                        <input type="hidden" name="applicant_id" value="{{$applicant ? $applicant->id : null}}">
                        <div class="fallback">
                            <input name="file" type="file" />
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
        @php $passport_image_alert = true; @endphp
    @endif
    @if($applicant->spouse && $applicant->spouse->passport_image_status == 'rejected')
    <div class="card mt-4">
        <div class="card-body pt-4 p-3">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-2 col-12">
                    <div class="row text-center">
                        <h4>{{ucwords(strtolower($applicant->spouse->name))}}</h4>
                        <h4>{{ucwords(strtolower($applicant->spouse->last_name))}}</h4>
                    </div>
                </div>
                <div class="col-lg-4 col-12 text-center mb-4 d-flex justify-content-center">
                    <div class="cross cross2">
                        <img width="200px" height="200px" src="{{url('uploads', $applicant->spouse->passport_image)}}" style="border-radius: 10px;">
                    </div>
                </div>
                <div class="col-lg-3 col-12">
                    <div class="text-center">
                        <h5>{{ __('message.passport_image') }} {{ __('message.new') }}<span class="text-danger">*</span></h5>
                        <form action="{{url('upload_file')}}" class="form-control dropzone passport_image" id="spouse_passport">
                        @csrf
                        <input type="hidden" name="type" value="passport">
                        <input type="hidden" name="person" value="spouses">
                        <input type="hidden" name="id" value="{{$applicant->spouse->id}}">
                        <input type="hidden" name="applicant_id" value="{{$applicant ? $applicant->id : null}}">
                        <div class="fallback">
                            <input name="file" type="file" />
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
        @php $passport_image_alert = true; @endphp
    @endif
    @foreach($applicant->adult_children as $index => $adult_child)
        @if($adult_child->passport_image_status == 'rejected')
        <div class="card mt-4">
            <div class="card-body pt-4 p-3">
                <div class="row justify-content-center align-items-center">
                    <div class="col-lg-2 col-12">
                        <div class="row text-center">
                            <h4>{{ucwords(strtolower($adult_child->name))}}</h4>
                            <h4>{{ucwords(strtolower($adult_child->last_name))}}</h4>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12 text-center mb-4 d-flex justify-content-center">
                        <div class="cross cross2">
                            <img width="200px" height="200px" src="{{url('uploads', $adult_child->passport_image)}}" style="border-radius: 10px;">
                        </div>
                    </div>
                    <div class="col-lg-3 col-12">
                        <div class="text-center">
                            <h5>{{ __('message.passport_image') }} {{ __('message.new') }}<span class="text-danger">*</span></h5>
                            <form action="{{url('upload_file')}}" class="form-control dropzone passport_image" id="adult_child_passport_{{$index}}">
                            @csrf
                            <input type="hidden" name="type" value="passport">
                            <input type="hidden" name="person" value="adult_children">
                            <input type="hidden" name="id" value="{{$adult_child->id}}">
                            <input type="hidden" name="applicant_id" value="{{$applicant ? $applicant->id : null}}">
                            <div class="fallback">
                                <input name="file" type="file" />
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            @php $passport_image_alert = true; @endphp
        @endif
    @endforeach
    @foreach($applicant->children as $index => $child)
        @if($child->passport_image_status == 'rejected')
        <div class="card mt-4">
            <div class="card-body pt-4 p-3">
                <div class="row justify-content-center align-items-center">
                    <div class="col-lg-2 col-12">
                        <div class="row text-center">
                            <h4>{{ucwords(strtolower($child->name))}}</h4>
                            <h4>{{ucwords(strtolower($child->last_name))}}</h4>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12 text-center mb-4 d-flex justify-content-center">
                        <div class="cross cross2">
                            <img width="200px" height="200px" src="{{url('uploads', $child->passport_image)}}" style="border-radius: 10px;">
                        </div>
                    </div>
                    <div class="col-lg-3 col-12">
                        <div class="text-center">
                            <h5>{{ __('message.passport_image') }} {{ __('message.new') }}<span class="text-danger">*</span></h5>
                            <form action="{{url('upload_file')}}" class="form-control dropzone passport_image" id="child_passport_{{$index}}">
                            @csrf
                            <input type="hidden" name="type" value="passport">
                            <input type="hidden" name="person" value="children">
                            <input type="hidden" name="id" value="{{$child->id}}">
                            <input type="hidden" name="applicant_id" value="{{$applicant ? $applicant->id : null}}">
                            <div class="fallback">
                                <input name="file" type="file" />
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            @php $passport_image_alert = true; @endphp
        @endif
    @endforeach
    <div class="row justify-content-center mt-4">
        <div class="col-lg-12 col-12">
            <a href="{{url('step_six')}}" type="button" class="btn bg-gradient-dark btn-md mb-4 w-100">{{ __('message.send') }}</a>
        </div>
    </div>
</div>
@push('script')
<script type="text/javascript">
    $(document).ready(function () {
        @if($face_image_alert)
        $('#face_image_alert').removeClass('d-none');
        @endif
        @if($passport_image_alert)
        $('#passport_image_alert').removeClass('d-none');
        @endif
    });

    Dropzone.autoDiscover = false;

    $('.face_image').on('click', function () {
        alert('از فرستادن عکس ۴*۳ جدا خودداری کنید\nعکس باید تمام رخ و مستقیم رو به دوربین باشد\nپس زمینه عکس باید سفید باشد\nعکس باید واضح، بدون سایه، تاری و کدری و ویرایش فتوشاپی باشد\nعکس باید در حالت طبیعی و خنثی گرفته شود و نباید لبخند، خنده و نمایی از دندان‌ها در آن مشخص شود\nدر هنگام عکاسی از نوزاد، کودک باید بیدار و دارای چشمانی باز باشد و بدون همراهی والد گرفته شود\nفرد نباید در هنگام عکاسی عینک داشته باشد');
    });

    var drpzone_settings = {
        maxFilesize: 5,
        acceptedFiles: '.jpeg,.jpg,.png',
        addRemoveLinks: true,
        maxFiles: 1,
        dictRemoveFile: '{{ __('message.remove') }}',
        dictCancelUpload: '{{ __('message.cancel_upload') }}',
        dictCancelUploadConfirmation: '{{ __('message.cancel_upload_confirmation') }}',
        dictFileTooBig: '{{ __('message.file_too_big') }}',
        success: function(file, response) {
            if (!response.success) {
                alert(response.error);
            }
        }
    };

    function delete_file(id) {
        $.ajax({
            url: '{{url('delete_file')}}',
            type: 'POST',
            data: $('#'+id).serialize(),
            success: function(response) {
                if (!response.success) {
                    // alert(response.error);
                }
            },
            error: function(xhr, status, error) {
                alert('{{__('message.remove').' '.__('message.failed')}}');
            }
        });
    }

    @if($applicant->face_image_status == 'rejected')
    var applicant_face_file = null;
    var applicant_face_dz = new Dropzone('#applicant_face',  {
        ...drpzone_settings,
        dictDefaultMessage: '{{ __('message.drop_face_image_here_to_upload') }}',
        init: function() {
            this.on('addedfile', function(file) {
                if (applicant_face_file) {
                    this.removeFile(applicant_face_file);
                }
                applicant_face_file = file;
            });
            this.on('removedfile', function(file) {
                delete_file('applicant_face');
            });
        },
    });
    @endif

    @if($applicant->spouse && $applicant->spouse->face_image_status == 'rejected')
    var spouse_face_file = null;
    var spouse_face_dz = new Dropzone('#spouse_face',  {
        ...drpzone_settings,
        dictDefaultMessage: '{{ __('message.drop_face_image_here_to_upload') }}',
        init: function() {
            this.on('addedfile', function(file) {
                if (spouse_face_file) {
                    this.removeFile(spouse_face_file);
                }
                spouse_face_file = file;
            });
            this.on('removedfile', function(file) {
                delete_file('spouse_face');
            });
        },
    });
    @endif
    @foreach($applicant->adult_children as $index => $adult_child)
    @if($adult_child->face_image_status == 'rejected')
    var adult_child_face_file_{{$index}} = null;
    var adult_child_face_dz_{{$index}} = new Dropzone('#adult_child_face_{{$index}}',  {
        ...drpzone_settings,
        dictDefaultMessage: '{{ __('message.drop_face_image_here_to_upload') }}',
        init: function() {
            this.on('addedfile', function(file) {
                if (adult_child_face_file_{{$index}}) {
                    this.removeFile(adult_child_face_file_{{$index}});
                }
                adult_child_face_file_{{$index}} = file;
            });
            this.on('removedfile', function(file) {
                delete_file('adult_child_face_{{$index}}');
            });
        },
    });
    @endif
    @endforeach
    @foreach($applicant->children as $index => $child)
    @if($child->face_image_status == 'rejected')
    var child_face_file_{{$index}} = null;
    var child_face_dz_{{$index}} = new Dropzone('#child_face_{{$index}}',  {
        ...drpzone_settings,
        dictDefaultMessage: '{{ __('message.drop_face_image_here_to_upload') }}',
        init: function() {
            this.on('addedfile', function(file) {
                if (child_face_file_{{$index}}) {
                    this.removeFile(child_face_file_{{$index}});
                }
                child_face_file_{{$index}} = file;
            });
            this.on('removedfile', function(file) {
                delete_file('child_face_{{$index}}');
            });
        },
    });
    @endif
    @endforeach

    @if($applicant->passport_image_status == 'rejected')
    var applicant_passport_file = null;
    var applicant_passport_dz = new Dropzone('#applicant_passport',  {
        ...drpzone_settings,
        dictDefaultMessage: '{{ __('message.drop_passport_image_here_to_upload') }}',
        init: function() {
            this.on('addedfile', function(file) {
                if (applicant_passport_file) {
                    this.removeFile(applicant_passport_file);
                }
                applicant_passport_file = file;
            });
            this.on('removedfile', function(file) {
                delete_file('applicant_passport');
            });
        },
    });
    @endif

    @if($applicant->spouse && $applicant->spouse->passport_image_status == 'rejected')
    var spouse_passport_file = null;
    var spouse_passport_dz = new Dropzone('#spouse_passport',  {
        ...drpzone_settings,
        dictDefaultMessage: '{{ __('message.drop_passport_image_here_to_upload') }}',
        init: function() {
            this.on('addedfile', function(file) {
                if (spouse_passport_file) {
                    this.removeFile(spouse_passport_file);
                }
                spouse_passport_file = file;
            });
            this.on('removedfile', function(file) {
                delete_file('spouse_passport');
            });
        },
    });
    @endif
    @foreach($applicant->adult_children as $index => $adult_child)
    @if($adult_child->passport_image_status == 'rejected')
    var adult_child_passport_file_{{$index}} = null;
    var adult_child_passport_dz_{{$index}} = new Dropzone('#adult_child_passport_{{$index}}',  {
        ...drpzone_settings,
        dictDefaultMessage: '{{ __('message.drop_passport_image_here_to_upload') }}',
        init: function() {
            this.on('addedfile', function(file) {
                if (adult_child_passport_file_{{$index}}) {
                    this.removeFile(adult_child_passport_file_{{$index}});
                }
                adult_child_passport_file_{{$index}} = file;
            });
            this.on('removedfile', function(file) {
                delete_file('adult_child_passport_{{$index}}');
            });
        },
    });
    @endif
    @endforeach
    @foreach($applicant->children as $index => $child)
    @if($child->passport_image_status == 'rejected')
    var child_passport_file_{{$index}} = null;
    var child_passport_dz_{{$index}} = new Dropzone('#child_passport_{{$index}}',  {
        ...drpzone_settings,
        dictDefaultMessage: '{{ __('message.drop_passport_image_here_to_upload') }}',
        init: function() {
            this.on('addedfile', function(file) {
                if (child_passport_file_{{$index}}) {
                    this.removeFile(child_passport_file_{{$index}});
                }
                child_passport_file_{{$index}} = file;
            });
            this.on('removedfile', function(file) {
                delete_file('child_passport_{{$index}}');
            });
        },
    });
    @endif
    @endforeach
</script>
@endpush
@endsection