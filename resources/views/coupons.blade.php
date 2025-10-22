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
                    <h5 class="mb-0">{{ __('message.coupons') }}</h5>
                    <a href="{{ url('add_coupon') }}" class="btn bg-gradient-primary btn-sm mb-0" type="button">+&nbsp; {{ __('message.add_coupon') }}</a>
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

<!-- Delet User Modal -->
<div class="modal fade" id="delete-coupon" tabindex="-1" role="dialog" aria-labelledby="delete-coupon-label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title font-weight-normal" id="delete-coupon-label">{{__('message.remove')}} {{__('message.coupon')}} <strong id="delete-coupon-id"></strong></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        {{__('message.are_you_sure')}}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">{{__('message.close')}}</button>
        <div id="delete-coupon-button">
        </div>
      </div>
    </div>
  </div>
</div>

{!! $dataTable->scripts() !!}

<script type="text/javascript">
    function delete_coupon(id, code, confirm = 0){
        if(confirm == 0){
            $('#delete-coupon').modal('show');
            document.getElementById('delete-coupon-id').innerHTML = code;
            document.getElementById('delete-coupon-button').innerHTML = '<button onclick="delete_coupon('+id+', \''+code+'\', 1);" type="button" class="btn bg-gradient-primary">{{__('message.yes')}}</button>';
        }else{
            window.location.replace("/delete_coupon/"+id);
        }
    }
</script>
@endsection