<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="/images/logo/logo.jpg" type="image/x-icon">
    <title>@yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="/extra-assets/css/style.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

</head>

<body class="bg-gray-50 overflow-x-hidden">

    <x-toast :message="session('toast')" :success="session('success')" :error="session('error')" />

    @yield('content')

    <script src="/extra-assets/js/main.js"></script>
    <script src="/extra-assets/js/banner-carousel.js"></script>
    @livewireScripts
    @stack('scripts')
</body>

</html>
