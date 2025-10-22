@extends('layouts.user_type.auth')

@section('content')
    @if(session('success'))
        <div class="m-3  alert alert-success alert-dismissible fade show" id="alert-success" role="alert">
            <span class="alert-text text-white">
            {{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                <i class="fa fa-close" aria-hidden="true"></i>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div class="mt-3  alert alert-primary alert-dismissible fade show" role="alert">
            <span class="alert-text text-white">
            {{ session('error') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                <i class="fa fa-close" aria-hidden="true"></i>
            </button>
        </div>
    @endif
  <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>{{ __('message.users_table') }}</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('message.user') }}</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('message.permission') }}</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('message.email') }}</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('message.mobile') }}</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('message.active') }}</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('message.action') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                  @foreach($users as $user)
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div>
                            <img src="../assets/img/avatar.png" class="avatar avatar-sm ms-3">
                          </div>
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">{{$user->name.' '.$user->last_name}}</h6>
                          </div>
                        </div>
                      </td>
                      <input type="hidden" id="user_{{$user->id}}" value="{{$user->name.' '.$user->last_name}}">
                      <td class="align-middle text-center">
                          <button type="button" onclick="change_user_permission_modal({{$user->id}}, '{{$user->role ? $user->role : ''}}')" class="btn btn-sm bg-gradient-primary m-0">{{ __('message.'.$user->role) }}</button>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="text-secondary text-xs font-weight-bold">{{$user->email}}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{$user->mobile}}</span>
                      </td>
                      <td class="align-middle text-center">
                        <div class="form-switch ps-0">
                          <input onclick="user_status({{$user->id}}, '{{$user->active ? 0 : 1}}');" class="form-check-input ms-auto" type="checkbox" {{ $user->active ? 'checked' : ''  }} {{ auth()->user()->id == $user->id ? 'disabled' : ''  }}>
                          </div>
                      </td>
                      <td class="align-middle text-center">
                          <a href="#" class="mx-3">
                              <i class="fas fa-user-edit text-secondary"></i>
                          </a>
                          <span onclick="delete_user({{$user->id}});">
                              <i class="cursor-pointer fas fa-trash text-secondary"></i>
                          </span>
                      </td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

<!-- Delet User Modal -->
<div class="modal fade" id="delete-user" tabindex="-1" role="dialog" aria-labelledby="delete-user-label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title font-weight-normal" id="delete-user-label">{{__('message.remove')}} {{__('message.user')}} <strong id="delete-user-id"></strong></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        {{__('message.are_you_sure')}}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">{{__('message.close')}}</button>
        <div id="delete-user-button">
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Change User Permission Modal -->
<div class="modal fade" id="change-user-permission-modal" tabindex="-1" role="dialog" aria-labelledby="change-user-permission-modal-label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title font-weight-normal" id="change-user-permission-modal-label">{{ __('message.change_user_permission') }} <strong id="user-name"></strong></h5>
      </div>
      <div class="modal-body">
        <form id="change-user-permission-form" action="" method="POST" role="form">
        @csrf
        <select required id="change-user-permission-select" name="role" class="form-control">
            <option value="not_selected">{{__('message.not_selected')}}</option>
            <option value="admin">{{__('message.admin')}}</option>
            <option value="user">{{__('message.user')}}</option>
            <option value="agent">{{__('message.agent')}}</option>
        </select>
        </form>
      </div>
      <div class="modal-footer">
        <button type="submit" form="change-user-permission-form" class="btn bg-gradient-primary">{{ __('message.update') }}</button>
        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">{{ __('message.close') }}</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
    function change_user_permission_modal(ui, pi) {
        $('#change-user-permission-modal').modal('show');
        $('#user-name').html($('#user_'+ui).val());
        $('#change-user-permission-select').val(pi);
        $('#change-user-permission-form').attr('action', '/change_user_permission/'+ui);
    }

    var destroy_select_2 = (function() {
        var destroy_select_2_executed = false;
        return function() {
            if (!destroy_select_2_executed) {
                destroy_select_2_executed = true;
                $('#change-user-permission-select').select2('destroy');
            }
        };
    })()
</script>
  
@endsection
