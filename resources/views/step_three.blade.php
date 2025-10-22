@extends('layouts.user_type.guest')

@section('content')
<style type="text/css">
    .dz-details {
        display: none;
    }
    a.dz-remove {
        color: red;
        padding-top: 10px;
    }
    .dz-image:hover {
        transform: scale(2);
    }
    .dropzone .dz-preview:hover .dz-image img {
        transform: unset;
        filter: unset;
    }
    .dropzone .dz-message {
        margin: 2em 0;
    }
    .dropzone {
        min-height: unset;
    }
    .dropzone .dz-preview {
        margin: unset;
    }
    .dropzone .dz-preview .dz-error-message {
        top: 160px!important;
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
    @include('layouts.breadcrump')
    <div class="card">
        <div class="card-body">
            <h4 class="text-center">{{ __('message.files_upload') }}</h4>
        </div>
    </div>
    <div class="card mt-4">
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
    <div class="card mt-4">
        <div class="card-header pb-3 px-3" style="border-bottom: 1px dotted #d2d6da;">
            <h5 class="mb-0">{{ __('message.main_applicant') }}</h5>
        </div>
        <div class="card-body pt-2 p-3">
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-4 mt-4">
                    <h6>{{ __('message.passport_image') }} <span class="text-xs text-danger">({{__('message.optional')}})</span></h6>
                    <div class="text-center">
                        <form action="{{url('upload_file')}}" class="form-control dropzone" id="applicant_passport">
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
                <div class="col-lg-4 col-md-4 mt-4">
                    <h6>{{ __('message.face_image') }} <span class="text-danger">*</span></h6>
                    <div class="text-center">
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
                    @error('applicant_face')
                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>
    @if($applicant->spouse)
    <div class="card mt-4">
        <div class="card-header pb-3 px-3" style="border-bottom: 1px dotted #d2d6da;">
            <h5 class="mb-0">{{ __('message.spouse') }}</h5>
        </div>
        <div class="card-body pt-2 p-3">
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-4 mt-4">
                    <h6>{{ __('message.passport_image') }} <span class="text-xs text-danger">({{__('message.optional')}})</span></h6>
                    <div class="text-center">
                        <form action="{{url('upload_file')}}" class="form-control dropzone" id="spouse_passport">
                        @csrf
                        <input type="hidden" name="type" value="passport">
                        <input type="hidden" name="person" value="spouses">
                        <input type="hidden" name="applicant_id" value="{{$applicant ? $applicant->id : null}}">
                        <input type="hidden" name="id" value="{{$applicant->spouse->id}}">
                        <div class="fallback">
                            <input name="file" type="file" />
                        </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 mt-4">
                    <h6>{{ __('message.face_image') }} <span class="text-danger">*</span></h6>
                    <div class="text-center">
                        <form action="{{url('upload_file')}}" class="form-control dropzone face_image" id="spouse_face">
                        @csrf
                        <input type="hidden" name="type" value="face">
                        <input type="hidden" name="person" value="spouses">
                        <input type="hidden" name="applicant_id" value="{{$applicant ? $applicant->id : null}}">
                        <input type="hidden" name="id" value="{{$applicant->spouse->id}}">
                        <div class="fallback">
                            <input name="file" type="file" />
                        </div>
                        </form>
                    </div>
                    @error('spouse_face')
                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>
    @endif
    @foreach($applicant->adult_children as $index => $adult_child)
    <div class="card mt-4">
        <div class="card-header pb-3 px-3" style="border-bottom: 1px dotted #d2d6da;">
            <h5 class="mb-0">{{ __('message.adult_child') }} ({{ __('message.'.($index+1).'_st') }})</h5>
        </div>
        <div class="card-body pt-2 p-3">
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-4 mt-4">
                    <h6>{{ __('message.passport_image') }} <span class="text-xs text-danger">({{__('message.optional')}})</span></h6>
                    <div class="text-center">
                        <form action="{{url('upload_file')}}" class="form-control dropzone" id="adult_child_passport_{{$index}}">
                        @csrf
                        <input type="hidden" name="type" value="passport">
                        <input type="hidden" name="person" value="adult_children">
                        <input type="hidden" name="applicant_id" value="{{$applicant ? $applicant->id : null}}">
                        <input type="hidden" name="id" value="{{$adult_child->id}}">
                        <div class="fallback">
                            <input name="file" type="file" />
                        </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 mt-4">
                    <h6>{{ __('message.face_image') }} <span class="text-danger">*</span></h6>
                    <div class="text-center">
                        <form action="{{url('upload_file')}}" class="form-control dropzone face_image" id="adult_child_face_{{$index}}">
                        @csrf
                        <input type="hidden" name="type" value="face">
                        <input type="hidden" name="person" value="adult_children">
                        <input type="hidden" name="applicant_id" value="{{$applicant ? $applicant->id : null}}">
                        <input type="hidden" name="id" value="{{$adult_child->id}}">
                        <div class="fallback">
                            <input name="file" type="file" />
                        </div>
                        </form>
                    </div>
                    @error('adult_child_face.'.$index)
                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>
    @endforeach
    @foreach($applicant->children as $index => $child)
    <div class="card mt-4">
        <div class="card-header pb-3 px-3" style="border-bottom: 1px dotted #d2d6da;">
            <h5 class="mb-0">{{ __('message.child') }} ({{ __('message.'.($index+1).'_st') }})</h5>
        </div>
        <div class="card-body pt-2 p-3">
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-4 mt-4">
                    <h6>{{ __('message.passport_image') }} <span class="text-xs text-danger">({{__('message.optional')}})</span></h6>
                    <div class="text-center">
                        <form action="{{url('upload_file')}}" class="form-control dropzone" id="child_passport_{{$index}}">
                        @csrf
                        <input type="hidden" name="type" value="passport">
                        <input type="hidden" name="person" value="children">
                        <input type="hidden" name="applicant_id" value="{{$applicant ? $applicant->id : null}}">
                        <input type="hidden" name="id" value="{{$child->id}}">
                        <div class="fallback">
                            <input name="file" type="file" />
                        </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 mt-4">
                    <h6>{{ __('message.face_image') }} <span class="text-danger">*</span></h6>
                    <div class="text-center">
                        <form action="{{url('upload_file')}}" class="form-control dropzone face_image" id="child_face_{{$index}}">
                        @csrf
                        <input type="hidden" name="type" value="face">
                        <input type="hidden" name="person" value="children">
                        <input type="hidden" name="applicant_id" value="{{$applicant ? $applicant->id : null}}">
                        <input type="hidden" name="id" value="{{$child->id}}">
                        <div class="fallback">
                            <input name="file" type="file" />
                        </div>
                        </form>
                    </div>
                    @error('child_face.'.$index)
                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>
    @endforeach
    <div class="row justify-content-center mt-4">
        <div class="col-lg-12 col-12">
            <a href="{{url('step_four', ($applicant ? $applicant->id : null))}}" type="button" class="btn bg-gradient-dark btn-md mb-4 w-100">{{ __('message.send') }}</a>
        </div>
    </div>
</div>

@push('script')
<script type="text/javascript">
    $(document).ready(function () {
        $('#residence_country').select2();
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

    var applicant_passport_file = null;
    var applicant_passport_dz = new Dropzone('#applicant_passport', {
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
    @if($applicant->passport_image)
    applicant_passport_dz.displayExistingFile({}, "/uploads/{{$applicant->passport_image}}");
    @endif

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
    @if($applicant->face_image)
    applicant_face_dz.displayExistingFile({}, "/uploads/{{$applicant->face_image}}");
    @endif

    @if($applicant->spouse)
    var spouse_passport_file = null;
    var spouse_passport_dz = new Dropzone('#spouse_passport', {
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
    @if($applicant->spouse->passport_image)
    spouse_passport_dz.displayExistingFile({}, "/uploads/{{$applicant->spouse->passport_image}}");
    @endif
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
    @if($applicant->spouse->face_image)
    spouse_face_dz.displayExistingFile({}, "/uploads/{{$applicant->spouse->face_image}}");
    @endif
    @endif
    @foreach($applicant->adult_children as $index => $adult_child)
    var adult_child_passport_file_{{$index}} = null;
    var adult_child_passport_dz_{{$index}} = new Dropzone('#adult_child_passport_{{$index}}', {
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
    @if($adult_child->passport_image)
    adult_child_passport_dz_{{$index}}.displayExistingFile({}, "/uploads/{{$adult_child->passport_image}}");
    @endif
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
    @if($adult_child->face_image)
    adult_child_face_dz_{{$index}}.displayExistingFile({}, "/uploads/{{$adult_child->face_image}}");
    @endif
    @endforeach
    @foreach($applicant->children as $index => $child)
    var child_passport_file_{{$index}} = null;
    var child_passport_dz_{{$index}} = new Dropzone('#child_passport_{{$index}}', {
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
    @if($child->passport_image)
    child_passport_dz_{{$index}}.displayExistingFile({}, "/uploads/{{$child->passport_image}}");
    @endif
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
    @if($child->face_image)
    child_face_dz_{{$index}}.displayExistingFile({}, "/uploads/{{$child->face_image}}");
    @endif
    @endforeach
</script>
@endpush
@endsection