<header id="main-header" class="bg-white fixed top-0 left-0 z-50 w-full border-b border-gray-100">
    <div class="border-b border-gray-100 h-20 flex items-center">
        <nav class="max-w-[1800px] mx-auto flex items-center justify-between px-4 md:px-10 relative w-full">
            <div class="md:hidden">
                <div id="menu-btn" class="text-2xl cursor-pointer hover:text-[#bc9c75] transition">
                    <i class="ri-menu-4-line"></i>
                </div>
            </div>
            <x-logo></x-logo>
            <div class="grow max-w-md mx-2 lg:mx-8 hidden md:block">
                <div class="relative group">
                    <input type="text" placeholder="Tìm kiếm..."
                        class="w-full bg-gray-100 border border-transparent focus:border-[#bc9c75] focus:bg-white rounded-full py-1.5 md:py-2 pl-4 pr-10 outline-none transition-all text-xs lg:text-sm">
                    <button class="absolute right-0 top-0 h-full px-3 text-gray-500 hover:text-[#bc9c75]">
                        <i class="ri-search-line"></i>
                    </button>
                </div>
            </div>
            <div class="flex items-center justify-end gap-4 md:gap-6 text-xl flex-none">
                <a href="{{ route('user.wishlist') }}" class="relative p-2">
                    <i class="ri-heart-line cursor-pointer hidden md:block hover-gold transition"></i>
                    <span id="wishlist-count"
                        class="absolute top-0 right-0 bg-red-600 text-white text-[10px] w-4 h-4 hidden items-center justify-center rounded-full"></span>
                </a>
                <a href="{{ route('user.cart') }}" class="relative p-2">
                    <i class="ri-shopping-cart-line cursor-pointer hidden md:block hover-gold transition"></i>
                    <span id="cart-count"
                        class="absolute top-0 right-0 bg-red-600 text-white text-[10px] w-4 h-4 hidden items-center justify-center rounded-full"></span>
                </a>

                @auth
                    @php
                        $headerAvatarPath = (string) (auth()->user()->avatar ?? '');
                        $headerAvatarUrl = '';
                        $headerAvatarVersion = optional(auth()->user()->updated_at)->timestamp;

                        if ($headerAvatarPath !== '') {
                            if (\Illuminate\Support\Str::startsWith($headerAvatarPath, ['http://', 'https://', '/'])) {
                                $headerAvatarUrl = $headerAvatarPath;
                            } else {
                                $headerAvatarUrl = '/storage/' . ltrim($headerAvatarPath, '/');
                            }

                            if ($headerAvatarVersion) {
                                $headerAvatarUrl .=
                                    (str_contains($headerAvatarUrl, '?') ? '&' : '?') . 'v=' . $headerAvatarVersion;
                            }
                        }
                    @endphp

                    <details class="relative hidden md:block group">
                        <summary
                            class="list-none w-10 h-10 rounded-full border border-gray-200 bg-gray-50 hover:bg-white hover:border-[#bc9c75]/50 transition flex items-center justify-center cursor-pointer">
                            @if ($headerAvatarUrl !== '')
                                <img src="{{ $headerAvatarUrl }}" alt="Avatar"
                                    class="w-full h-full rounded-full object-cover js-header-avatar">
                            @else
                                <i class="ri-user-line text-gray-700"></i>
                            @endif
                        </summary>

                        <div
                            class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 p-2 z-[120]">
                            <div class="px-3 py-2 border-b border-gray-100 mb-1">
                                <p class="text-[11px] text-gray-400 uppercase tracking-wider font-bold">Tài khoản</p>
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->email }}</p>
                            </div>

                            <a href="{{ route('user.profile') }}"
                                class="flex items-center gap-2 px-3 py-2.5 text-sm rounded-xl hover:bg-gray-50 text-gray-700 transition">
                                <i class="ri-user-settings-line"></i>
                                Trang cá nhân
                            </a>

                            <a href="{{ route('user.profile.password') }}"
                                class="flex items-center gap-2 px-3 py-2.5 text-sm rounded-xl hover:bg-gray-50 text-gray-700 transition">
                                <i class="ri-lock-password-line"></i>
                                Đổi mật khẩu
                            </a>

                            <form action="{{ route('logout') }}" method="POST" class="mt-1">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-2 px-3 py-2.5 text-sm rounded-xl hover:bg-red-50 text-red-600 transition"
                                    onclick="return confirm('Bạn có chắc muốn đăng xuất?')">
                                    <i class="ri-logout-box-r-line"></i>
                                    Đăng xuất
                                </button>
                            </form>
                        </div>
                    </details>

                    <details class="relative md:hidden group">
                        <summary
                            class="list-none w-10 h-10 rounded-full border border-gray-200 bg-gray-50 hover:bg-white hover:border-[#bc9c75]/50 transition flex items-center justify-center cursor-pointer">
                            @if ($headerAvatarUrl !== '')
                                <img src="{{ $headerAvatarUrl }}" alt="Avatar"
                                    class="w-full h-full rounded-full object-cover js-header-avatar">
                            @else
                                <i class="ri-user-line text-gray-700"></i>
                            @endif
                        </summary>

                        <div
                            class="absolute right-0 mt-3 w-52 bg-white rounded-2xl shadow-xl border border-gray-100 p-2 z-[120]">
                            <a href="{{ route('user.profile') }}"
                                class="flex items-center gap-2 px-3 py-2.5 text-sm rounded-xl hover:bg-gray-50 text-gray-700 transition">
                                <i class="ri-user-settings-line"></i>
                                Trang cá nhân
                            </a>

                            <a href="{{ route('user.profile.password') }}"
                                class="flex items-center gap-2 px-3 py-2.5 text-sm rounded-xl hover:bg-gray-50 text-gray-700 transition">
                                <i class="ri-lock-password-line"></i>
                                Đổi mật khẩu
                            </a>

                            <form action="{{ route('logout') }}" method="POST" class="mt-1">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-2 px-3 py-2.5 text-sm rounded-xl hover:bg-red-50 text-red-600 transition"
                                    onclick="return confirm('Bạn có chắc muốn đăng xuất?')">
                                    <i class="ri-logout-box-r-line"></i>
                                    Đăng xuất
                                </button>
                            </form>
                        </div>
                    </details>
                @else
                    <a href="{{ route('login') }}"
                        class="text-sm font-bold uppercase tracking-widest px-4 py-2 rounded-full border border-gray-200 text-gray-700 hover:border-[#bc9c75] hover:text-[#bc9c75] transition">
                        Đăng nhập
                    </a>
                @endauth
            </div>
        </nav>
    </div>

    <div class="md:hidden bg-white px-4 py-2 border-b border-gray-100">
        <div class="relative">
            <input type="text" placeholder="Bạn tìm gì hôm nay?..."
                class="w-full bg-gray-50 border-none rounded-lg py-2 pl-4 pr-10 text-sm outline-none focus:ring-1 focus:ring-[#bc9c75]">
            <i class="ri-search-line absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </div>
    </div>

    <div class="bg-white border-b border-gray-100 hidden md:block">
        <div class="max-w-[1400px] mx-auto flex items-center justify-center h-16">
            <nav
                class="flex items-center justify-center gap-10 text-[clamp(11px,1.1vw,14px)] font-bold uppercase tracking-widest text-gray-800 w-full relative">
                <a href="{{ route('dashboard') }}" class="menu-link hover:text-[#bc9c75] transition-all">Trang chủ</a>
                <a href="{{ route('user.introduce') }}" class="menu-link hover:text-[#bc9c75] transition-all">Giới
                    thiệu</a>
                <a href="{{ route('user.product') }}" class="menu-link hover:text-[#bc9c75] transition-all">Sản
                    phẩm</a>
                <a href="{{ route('user.collection') }}" class="menu-link hover:text-[#bc9c75] transition-all">Bộ sưu
                    tập</a>
                <a href="{{ route('user.orders') }}" class="menu-link hover:text-[#bc9c75] transition-all">Đơn hàng</a>
                <a href="{{ route('user.support') }}" class="menu-link hover:text-[#bc9c75] transition-all">Hỗ trợ</a>
                <a href="{{ route('user.contact') }}" class="menu-link hover:text-[#bc9c75] transition-all">Liên hệ</a>
            </nav>
        </div>
    </div>
</header>

<div id="breadcrumb-area" class="bg-gray-50 py-4 hidden mt-36 md:mt-36">
    <div class="max-w-7xl mx-auto px-6 text-sm">
        <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-black">Trang chủ</a>
        <span id="breadcrumb-current"></span>
    </div>
</div>
