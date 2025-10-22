@extends('layouts.app')

@section('auth')

    @include('layouts.navbars.auth.sidebar-rtl')
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg" style="overflow: unset !important;{{session('sidebar_show') ? '' : 'margin-right: 0px;'}}">
        @include('layouts.navbars.auth.nav-rtl')
        <div class="container-fluid py-4">
            @yield('content')
            @include('layouts.footers.auth.footer')
        </div>
    </main>

@endsection