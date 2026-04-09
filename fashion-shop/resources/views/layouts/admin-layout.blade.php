<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link rel="shortcut icon" href="/images/logo/logo.jpg" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/extra-assets/css/admin.css">
    @vite('resources/css/app.css')
    @livewireStyles


</head>

<body class="bg-[#f8f9fa] h-screen flex flex-col">
    <x-toast :message="session('toast')" :success="session('success')" :error="session('error')" />

    @php
        $authUser = Auth::user();
        $displayName = $authUser->full_name ?: $authUser->username;
        $avatarPath = (string) ($authUser->avatar ?? '');
        $userRole = $authUser->role;

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
                        <p class="text-gray-400">{{ $userRole }}</p>
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

                <a href="{{ route('admin.admin-profile') }}"
                    class="mt-5 w-full border border-gray-200 rounded-lg py-2 text-sm hover:bg-gray-50 transition block text-center">
                    Quản lý tài khoản của bạn
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

            <nav class="flex-1 px-4 space-y-1">
                <div class="py-2">
                    <p class="px-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Chính</p>
                    <a href="{{ route('admin.admin_dashboard') }}"
                        class="nav-item group {{ Request::routeIs('admin.admin_dashboard') ? 'active' : '' }}">
                        <div class="nav-icon-box">
                            <i class="fa-solid fa-house"></i>
                        </div>
                        <span class="font-medium">Tổng quan</span>
                    </a>
                </div>

                <div class="py-2">
                    <p class="px-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Sản phẩm</p>
                    <a href="{{ route('admin.product-manager') }}"
                        class="nav-item group {{ Request::routeIs('admin.product-manager') || Request::routeIs('admin.add-product') || Request::routeIs('admin.edit-product') ? 'active' : '' }}">
                        <div class="nav-icon-box">
                            <i class="fa-solid fa-box-archive text-[15px]"></i>
                        </div>
                        <span class="font-medium">Danh sách sản phẩm</span>
                    </a>
                    <a href="{{ route('admin.product-categories') }}"
                        class="nav-item group {{ Request::routeIs('admin.product-categories*') ? 'active' : '' }}">
                        <div class="nav-icon-box">
                            <i class="fa-solid fa-sitemap text-[15px]"></i>
                        </div>
                        <span class="font-medium">Danh mục sản phẩm</span>
                    </a>
                    <a href="{{ route('admin.product-collections') }}"
                        class="nav-item group {{ Request::routeIs('admin.product-collections') || Request::routeIs('admin.create-collection') || Request::routeIs('admin.edit-collection') ? 'active' : '' }}">
                        <div class="nav-icon-box">
                            <i class="fa-solid fa-images text-[15px]"></i>
                        </div>
                        <span class="font-medium">Bộ sưu tập</span>
                    </a>
                    <div data-page="vouchers" onclick="loadPage('vouchers')" class="nav-item group">
                        <div class="nav-icon-box">
                            <i class="fa-solid fa-ticket text-[15px]"></i>
                        </div>
                        <span class="font-medium">Voucher</span>
                    </div>
                    <div data-page="flash-sale" onclick="loadPage('flash-sale')" class="nav-item group">
                        <div class="nav-icon-box">
                            <i class="fa-solid fa-gift text-[15px]"></i>
                        </div>
                        <span class="font-medium">Chương trình khuyến mãi</span>
                    </div>
                </div>

                <div class="py-2">
                    <p class="px-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Kinh doanh</p>
                    <div data-page="orders" onclick="loadPage('orders')" class="nav-item group justify-between">
                        <div class="flex items-center gap-3">
                            <div class="nav-icon-box">
                                <i class="fa-solid fa-cart-shopping text-[15px]"></i>
                            </div>
                            <span class="font-medium">Đơn hàng</span>
                        </div>
                        <span
                            class="bg-red-50 text-red-500 text-[10px] font-bold px-2 py-0.5 rounded-lg border border-red-100">05</span>
                    </div>
                    <div data-page="customers" onclick="loadPage('customers')" class="nav-item group">
                        <div class="nav-icon-box">
                            <i class="fa-solid fa-user-group text-[15px]"></i>
                        </div>
                        <span class="font-medium">Khách hàng</span>
                    </div>
                    <div data-page="revenue" onclick="loadPage('revenue')" class="nav-item group">
                        <div class="nav-icon-box">
                            <i class="fa-solid fa-chart-pie text-[15px]"></i>
                        </div>
                        <span class="font-medium">Doanh thu</span>
                    </div>
                    <div data-page="support" onclick="loadPage('support')" class="nav-item group ">
                        <div class="nav-icon-box">
                            <i class="fa-solid fa-headset text-[15px]"></i>
                        </div>
                        <span class="font-medium">Trợ giúp & Hỗ trợ</span>
                    </div>
                    <a href="{{ route('admin.employee-manager') }}"
                        class="nav-item group {{ Request::routeIs('admin.employee-manager') || Request::routeIs('admin.add-employee') || Request::routeIs('admin.edit-employee') ? 'active' : '' }}">
                        <div class="nav-icon-box">
                            <i class="fa-solid fa-user-tie text-[15px]"></i>
                        </div>
                        <span class="font-medium">Quản lý nhân sự</span>
                    </a>
                </div>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col overflow-y-auto custom-scrollbar">
            <div class="ff-page-header px-8 pt-6 pb-2">
                @yield('page-header')
            </div>
            <main id="content-area" class="p-8 transition-opacity duration-200">

                @yield('content')

            </main>
        </div>

    </div>

    <script src="/extra-assets/js/admin.js"></script>
    @livewireScripts
    @stack('scripts')
</body>

</html>
