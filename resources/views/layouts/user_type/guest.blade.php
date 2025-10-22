@extends('layouts.app')

@section('guest')
    @if(\Request::is('step*'))
        @include('layouts.navbars.guest.sidebar-rtl')
        <div class="container position-sticky z-index-sticky top-0" style="max-width: 100% !important;">
            <div class="row">
                <div class="col-12">
                    @include('layouts.navbars.guest.custom')
                </div>
            </div>
        </div>
        @yield('content')
        @include('layouts.footers.guest.custom-footer')
    @else
        <div class="container position-sticky z-index-sticky top-0">
            <div class="row">
                <div class="col-12">
                    @include('layouts.navbars.guest.nav')
                </div>
            </div>
        </div>
        @yield('content')        
        @include('layouts.footers.guest.footer')
    @endif
@endsection