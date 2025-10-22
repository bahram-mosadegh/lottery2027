<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-end me-3 rotate-caret noprint" id="sidenav-main">
  <div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute start-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
    <a class="align-items-center d-flex m-0 navbar-brand text-wrap" target="_blank" href="{{url('applicants')}}">
        <img src="../assets/img/logo-ct.png" class="navbar-brand-img h-100" alt="...">
        <span class="me-3 font-weight-bold"><h5>{{ __('message.site_name') }}</h5></span>
    </a>
  </div>
  <hr class="horizontal dark mt-0">
  <div class="collapse navbar-collapse px-0 w-auto" id="sidenav-collapse-main">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link {{ (Request::is('applicants') ? 'active' : '') }}" href="{{ url('applicants') }}">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center ms-2 d-flex align-items-center justify-content-center">
                <i style="font-size: 1rem;" class="fa fa-lg fa-clock ps-2 pe-2 text-center text-dark {{ (Request::is('applicants') ? 'text-white' : 'text-dark') }} " aria-hidden="true"></i>
            </div>
            <span class="nav-link-text me-1">{{ __('message.applicants') }}</span>
        </a>
      </li>
      @can('check_data')
      <li class="nav-item">
        <a class="nav-link {{ (Request::is('check_data') ? 'active' : '') }}" href="{{ url('check_data') }}">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center ms-2 d-flex align-items-center justify-content-center">
                <i style="font-size: 1rem;" class="fa fa-lg fa-check-square ps-2 pe-2 text-center text-dark {{ (Request::is('check_data') ? 'text-white' : 'text-dark') }} " aria-hidden="true"></i>
            </div>
            <span class="nav-link-text me-1">{{ __('message.check_data') }}</span>
        </a>
      </li>
      @endcan
      <li class="nav-item">
        <a class="nav-link {{ (Request::is('profile') ? 'active' : '') }}" href="{{ url('profile') }}">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center ms-2 d-flex align-items-center justify-content-center">
                <i style="font-size: 1rem;" class="fas fa-lg fa-user-circle ps-2 pe-2 text-center text-dark {{ (Request::is('profile') ? 'text-white' : 'text-dark') }} " aria-hidden="true"></i>
            </div>
            <span class="nav-link-text me-1">{{ __('message.profile') }}</span>
        </a>
      </li>
      @can('users')
      <li class="nav-item">
        <a class="nav-link {{ (Request::is('users') ? 'active' : '') }}" href="{{ url('users') }}">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center ms-2 d-flex align-items-center justify-content-center">
                <i style="font-size: 1rem;" class="fas fa-lg fa-user ps-2 pe-2 text-center text-dark {{ (Request::is('users') ? 'text-white' : 'text-dark') }} " aria-hidden="true"></i>
            </div>
            <span class="nav-link-text me-1">{{ __('message.users') }}</span>
        </a>
      </li>
      @endcan
    </ul>
  </div>
  
</aside>
