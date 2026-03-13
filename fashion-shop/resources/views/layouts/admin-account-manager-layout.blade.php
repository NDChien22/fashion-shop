<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý tài khoản - FAST FASHION</title>
    <link rel="shortcut icon" href="/images/logo/logo.jpg" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../extra-assets/css/admin.css">
    @vite('resources/css/app.css')
    @livewireStyles
    @stack('styles')
</head>

<body class="bg-gray-50 flex flex-col h-screen overflow-hidden">
    @php
        $authUser = Auth::user();
        $displayName = $authUser->full_name ?: $authUser->username;
        $avatarPath = (string) ($authUser->avatar ?? '');

        if ($avatarPath !== '' && !\Illuminate\Support\Str::startsWith($avatarPath, ['http://', 'https://', '/'])) {
            $avatarPath = '/storage/' . $avatarPath;
        }

        $avatarInitials = \Illuminate\Support\Str::of(trim($displayName ?: $authUser->email))
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn(string $part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
            ->implode('');
    @endphp

    <header id="header"
        class="h-16 flex-none bg-white border-b border-gray-100 flex items-center justify-between px-3 sm:px-6 md:px-8 gap-6 sticky top-0 z-40">
        <x-logo></x-logo>

        <div class="flex items-center gap-6">

            <i class="fa-regular fa-bell text-gray-400 text-lg cursor-pointer hover:text-gray-600"></i>

            <div class="relative">

                <div onclick="toggleUserMenu()" class="flex items-center gap-3 cursor-pointer">

                    <div class="w-9 h-9">
                        @if ($avatarPath)
                            <img src="{{ $avatarPath }}" alt="Avatar"
                                style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                        @else
                            <div
                                class="w-9 h-9 bg-[#bc9c75] text-white rounded-full flex items-center justify-center text-sm font-bold">
                                {{ $avatarInitials }}</div>
                        @endif
                    </div>

                    <div class="text-[12px] leading-tight">
                        <p class="font-semibold text-gray-800">{{ $displayName }}</p>
                        <p class="text-gray-400">Chủ cửa hàng</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="userMenu"
            class="hidden absolute top-10 right-0 mt-4 w-72 bg-white rounded-2xl shadow-xl border border-gray-100 p-6">

            <div class="flex flex-col items-center text-center">

                <div class="w-16 h-16 ">
                    @if ($avatarPath)
                        <img src="{{ $avatarPath }}" alt="Avatar"
                            style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    @else
                        <div
                            class="w-16 h-16 bg-[#bc9c75] text-white rounded-full flex items-center justify-center text-lg font-bold">
                            {{ $avatarInitials }}
                        </div>
                    @endif
                </div>

                <p class="mt-4 font-semibold text-gray-800 text-[15px]">{{ $displayName }}</p>

                <p class="text-sm text-gray-400">{{ $authUser->email }}</p>

                <a href=" {{ route('admin.admin-profile') }}"
                    class="btn-manage mt-5 w-full border border-gray-200 rounded-lg py-2 text-sm hover:bg-gray-50 transition block text-center">Quản
                    lý tài khoản của bạn
                </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="mt-5 text-red-500 flex items-center gap-2 text-sm hover:opacity-80">
                        <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
                    </button>
                </form>


            </div>
        </div>

    </header>

    <div class="bg-[#f8f9fa] px-4 py-2 lg:hidden">
        <button onclick="toggleSidebar()" class="text-2xl p-2 text-gray-600 hover:text-black">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>

    <div class="flex flex-1 overflow-hidden">
        <aside id="sidebar"
            class="fixed lg:static top-16 w-64 lg:w-72 bg-white border-r border-gray-100 h-[calc(100vh-64px)] lg:h-full flex flex-col transition-all duration-300 -translate-x-full lg:translate-x-0 z-40 overflow-y-auto custom-scrollbar">
            <nav class=" flex-1 px-4 space-y-1 mt-6">
                <div class="nav-item group {{ Request::routeIs('admin.admin-profile') ? 'active' : '' }}">
                    <div class="nav-icon-box">
                        <i class="fa-solid fa-box-archive text-[15px]"></i>
                    </div>
                    <a href="{{ route('admin.admin-profile') }}">
                        <span class="font-medium">Hồ sơ cá nhân</span>
                    </a>
                </div>
                <div class="nav-item group {{ Request::routeIs('admin.account-manager') ? 'active' : '' }}">
                    <div class="nav-icon-box">
                        <i class="fa-solid fa-circle-plus text-[15px]"></i>
                    </div>
                    <a href="{{ route('admin.account-manager') }}">
                        <span class="font-medium">Đổi mật khẩu</span>
                    </a>
                </div>
            </nav>
        </aside>

        <main class="flex-1 overflow-y-auto p-4 md:p-10">

            @yield('content')

        </main>
    </div>

    <script src="/extra-assets/js/admin.js"></script>
    @livewireScripts
</body>

</html>
