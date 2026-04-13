@extends('layouts.user-layout')

@section('content')
    @include('pages.user.components.header')

    <main id="content-area" class="pt-36 md:pt-40 px-0">
        @yield('main-content')
    </main>

    @include('pages.user.components.footer')
    @include('pages.user.components.support-widget')
@endsection
