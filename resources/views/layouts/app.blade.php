<!--
=========================================================
* Nilgam Admin Dashboard - v1.0.3
=========================================================

* Copyright 2023 Nilgam Group (https://www.nilgam.com)
* Coded by Baharam Mosadegh

=========================================================
-->
<!DOCTYPE html>
 
    <html dir="rtl" lang="fa">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  @if (env('IS_DEMO'))
      <x-demo-metas></x-demo-metas>
  @endif

  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>
    {{ __('message.site_name') }}
  </title>
  <!-- Nucleo Icons -->
  <link href="{{\Helper::asset_versioned('assets/css/nucleo-icons.css')}}" rel="stylesheet" />
  <link href="{{\Helper::asset_versioned('assets/css/nucleo-svg.css')}}" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="{{\Helper::asset_versioned('assets/js/core/font-awesome.js')}}" crossorigin="anonymous"></script>
  <script src="{{\Helper::asset_versioned('assets/js/core/jquery-3.6.3.min.js')}}"></script>
  <link href="{{\Helper::asset_versioned('assets/css/nucleo-svg.css')}}" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="{{\Helper::asset_versioned('assets/css/soft-ui-dashboard.css')}}" rel="stylesheet" />
  <link href="{{\Helper::asset_versioned('assets/data-tables/DataTables-1.13.1/css/jquery.dataTables.min.css')}}" rel="stylesheet">
  <link href="{{\Helper::asset_versioned('assets/data-tables/DataTables-1.13.1/css/dataTables.responsive.min.css')}}" rel="stylesheet">
  <link href="{{\Helper::asset_versioned('assets/data-tables/DataTables-1.13.1/css/buttons.dataTables.min.css')}}" rel="stylesheet" />
  <link href="{{\Helper::asset_versioned('assets/css/select2.min.css')}}" rel="stylesheet" />
  <link href="{{\Helper::asset_versioned('assets/css/owl_carousel.min.css')}}" rel="stylesheet" />
  <link href="{{\Helper::asset_versioned('assets/css/ticket.css')}}" rel="stylesheet" />
  <link href="{{\Helper::asset_versioned('assets/css/breadcrumb.css')}}" rel="stylesheet" />
</head>

<body class="g-sidenav-show @if(!\Request::is('step*')) bg-gray-100 @endif rtl ">
  @auth
    @if(\Request::is('step*'))
      @yield('guest')
    @else
      @yield('auth')
    @endif
  @endauth
  @guest
    @yield('guest')
  @endguest
    <!--   Core JS Files   -->
  <script src="{{\Helper::asset_versioned('assets/js/plugins/dropzone.min.js')}}"></script>
  <script src="{{\Helper::asset_versioned('assets/js/plugins/modernizr.js')}}"></script>
  <script src="{{\Helper::asset_versioned('assets/js/plugins/owl_carousel.min.js')}}"></script>
  <script src="{{\Helper::asset_versioned('assets/js/core/popper.min.js')}}"></script>
  <!-- <script src="../assets/js/core/bootstrap.min.js"></script> -->
  <script src="{{\Helper::asset_versioned('assets/js/plugins/perfect-scrollbar.min.js')}}"></script>
  <script src="{{\Helper::asset_versioned('assets/js/plugins/smooth-scrollbar.min.js')}}"></script>
  <script src="{{\Helper::asset_versioned('assets/js/plugins/fullcalendar.min.js')}}"></script>
  <script src="{{\Helper::asset_versioned('assets/js/plugins/chartjs.min.js')}}"></script>
  <script src="{{\Helper::asset_versioned('assets/js/plugins/select2.min.js')}}"></script>
  <script src="{{\Helper::asset_versioned('assets/data-tables/DataTables-1.13.1/js/jquery.dataTables.min.js')}}"></script>
  <script src="{{\Helper::asset_versioned('assets/data-tables/DataTables-1.13.1/js/dataTables.responsive.min.js')}}"></script>
  <script src="{{\Helper::asset_versioned('assets/data-tables/DataTables-1.13.1/js/dataTables.buttons.min.js')}}"></script>
  <script src="{{\Helper::asset_versioned('assets/js/core/bootstrap.bundle.min.js')}}" type="text/javascript"></script>
  <script src="{{\Helper::asset_versioned('assets/js/core/bootstrap-multiselect.js')}}" type="text/javascript"></script>
  <script src="{{\Helper::asset_versioned('vendor/datatables/buttons.server-side.js')}}"></script>
  @stack('rtl')
  @stack('dashboard')
  <script src="{{\Helper::asset_versioned('assets/js/soft-ui-dashboard.js')}}"></script>
  @stack('script')
  <script type="text/javascript">
    $('.display-sidebar').on('click', function () {
      $.get('{{url('show_sidebar')}}', function (data) {
        if (data.success) {
          if (data.show_sidebar) {
            setTimeout(function () {
              $('.sidenav').prop('style', '');
              $('.display-sidebar').prop('src', "{{asset('assets/img/monkey-show.gif')}}");
            }, 200);
            $('.main-content').prop('style', 'overflow: unset !important;');
          } else {
            $('.sidenav').prop('style', 'display: none;');
            $('.main-content').prop('style', 'overflow: unset !important; margin-right: 0px;');
            setTimeout(function () {
              $('.display-sidebar').prop('src', "{{asset('assets/img/monkey-hide.gif')}}");
            }, 300);
          }
        }
      });
    });
  </script>
</body>

</html>
