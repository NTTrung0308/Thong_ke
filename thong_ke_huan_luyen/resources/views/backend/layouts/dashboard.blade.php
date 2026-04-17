@extends('backend.layouts.master')

@section('content')
    <div class="main-panel">
        @include('backend.include.navbar')

        <div class="container">
            <div class="page-inner">
                @yield('dashboard_content')
            </div>
        </div>

        @include('backend.include.footer')
    </div>

    @include('backend.include.custom_template')
@endsection
