@extends('layouts.user-layout')

@section('content')
    @include('pages.user.components.header')

    <main id="content-area" class="pt-32 md:pt-36">
        @yield('main-content')
    </main>

    @include('pages.user.components.footer')
    @include('pages.user.components.support-widget')
@endsection
