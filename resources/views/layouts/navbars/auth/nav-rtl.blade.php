    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl noprint" id="navbarBlur" navbar-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <h6 class="font-weight-bolder mb-0">{{ __('message.'.explode('/', Request::path())[0]) }}</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 px-0 justify-content-end" id="navbar">
          <div class="dropdown">
            <button class="btn bg-gradient-secondary dropdown-toggle m-0" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
              {{ __('message.hello') }} {{auth()->user()->name}}
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <li>
                <a class="dropdown-item" href="{{ url('/profile')}}">
                  <i class="fa fa-user ms-1" aria-hidden="true"></i>
                  <strong>{{ __('message.profile') }}</strong>
                </a>
              </li>
              <hr class="horizontal dark mt-0 mb-0">
              <li>
                <a class="dropdown-item" href="{{ url('/logout')}}">
                  <i class="fa fa-power-off ms-1" aria-hidden="true"></i>
                  <strong>{{ __('message.sign_out') }}</strong>
                </a>
              </li>
            </ul>
          </div>
          <ul class="navbar-nav pe-2">
            <li class="nav-item d-xl-none pe-3 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                <div class="sidenav-toggler-inner">
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                </div>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- End Navbar -->