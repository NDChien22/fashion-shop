<header id="main-header" class="bg-white fixed top-0 left-0 z-50 w-full border-b border-gray-100">
    <div class="border-b border-gray-100 flex items-start lg:items-center py-2 lg:py-0">
        <nav
            class="max-w-screen-2xl mx-auto flex flex-wrap items-center justify-between gap-x-3 gap-y-2 px-4 md:px-6 lg:px-10 relative w-full">
            <div class="md:hidden order-1 lg:order-0">
                <div id="menu-btn" class="text-2xl cursor-pointer hover:text-[#bc9c75] transition">
                    <i class="ri-menu-4-line"></i>
                </div>
            </div>
            <div class="order-2 lg:order-0">
                <x-logo></x-logo>
            </div>
            @livewire('user.product-search')
            @php
                $headerVoucherCount = auth()->check()
                    ? \App\Models\UserVoucher::where('user_id', auth()->id())
                        ->where('status', 'unused')
                        ->count()
                    : 0;
                $headerCartCount = auth()->check()
                    ? \App\Models\Cart::where('user_id', auth()->id())->sum('quantity')
                    : \App\Models\Cart::where('session_id', request()->session()->getId())->sum('quantity');
            @endphp
            <div class="flex items-center justify-end gap-2 md:gap-6 text-lg md:text-xl flex-none order-3 lg:order-0">
                @auth
                    <a wire:navigate href="{{ route('user.vouchers') }}" class="relative p-2" title="Ví voucher">
                        <i class="ri-coupon-3-line cursor-pointer hover-gold transition"></i>
                        <span id="header-voucher-count-badge" data-voucher-count-badge
                            class="absolute top-0 right-0 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center leading-none {{ $headerVoucherCount > 0 ? '' : 'hidden' }}">{{ $headerVoucherCount > 99 ? '99+' : $headerVoucherCount }}</span>
                    </a>
                @endauth
                <a wire:navigate href="{{ route('user.whistlist') }}" class="relative p-2">
                    <i class="ri-heart-line cursor-pointer hover-gold transition"></i>
                    <span id="whistlist-count"
                        class="absolute top-0 right-0 bg-red-600 text-white text-[10px] w-4 h-4 items-center justify-center rounded-full {{ ($globalWhistlistCount ?? 0) > 0 ? 'flex' : 'hidden' }}">{{ $globalWhistlistCount ?? 0 }}</span>
                </a>
                <a wire:navigate href="{{ route('user.cart') }}" class="relative p-2">
                    <i class="ri-shopping-cart-line cursor-pointer hover-gold transition"></i>
                    <span id="cart-count" data-cart-count-badge
                        class="absolute top-0 right-0 bg-red-600 text-white text-[10px] w-4 h-4 items-center justify-center rounded-full {{ $headerCartCount > 0 ? 'flex' : 'hidden' }}">{{ $headerCartCount > 99 ? '99+' : $headerCartCount }}</span>
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
                            class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 p-2 z-50">
                            <div class="px-3 py-2 border-b border-gray-100 mb-1">
                                <p class="text-[11px] text-gray-400 uppercase tracking-wider font-bold">Tài khoản</p>
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->email }}</p>
                            </div>

                            <a wire:navigate href="{{ route('user.profile') }}"
                                class="flex items-center gap-2 px-3 py-2.5 text-sm rounded-xl hover:bg-gray-50 text-gray-700 transition">
                                <i class="ri-user-settings-line"></i>
                                Trang cá nhân
                            </a>

                            <a wire:navigate href="{{ route('user.profile.password') }}"
                                class="flex items-center gap-2 px-3 py-2.5 text-sm rounded-xl hover:bg-gray-50 text-gray-700 transition">
                                <i class="ri-lock-password-line"></i>
                                Đổi mật khẩu
                            </a>

                            <a wire:navigate href="{{ route('user.vouchers') }}"
                                class="flex items-center gap-2 px-3 py-2.5 text-sm rounded-xl hover:bg-gray-50 text-gray-700 transition relative">
                                <i class="ri-coupon-3-line"></i>
                                Ví voucher
                                <span data-voucher-count-badge
                                    class="ml-auto bg-red-500 text-white text-[10px] font-bold rounded-full px-2 py-0.5 inline-flex items-center justify-center {{ $headerVoucherCount > 0 ? '' : 'hidden' }}">{{ $headerVoucherCount > 99 ? '99+' : $headerVoucherCount }}</span>
                            </a>

                            <form action="{{ route('logout') }}" method="POST" class="mt-1">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-2 px-3 py-2.5 text-sm rounded-xl hover:bg-red-50 text-red-600 transition">
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
                            class="absolute right-0 mt-3 w-52 bg-white rounded-2xl shadow-xl border border-gray-100 p-2 z-50">
                            <a wire:navigate href="{{ route('user.profile') }}"
                                class="flex items-center gap-2 px-3 py-2.5 text-sm rounded-xl hover:bg-gray-50 text-gray-700 transition">
                                <i class="ri-user-settings-line"></i>
                                Trang cá nhân
                            </a>

                            <a wire:navigate href="{{ route('user.profile.password') }}"
                                class="flex items-center gap-2 px-3 py-2.5 text-sm rounded-xl hover:bg-gray-50 text-gray-700 transition">
                                <i class="ri-lock-password-line"></i>
                                Đổi mật khẩu
                            </a>

                            <a wire:navigate href="{{ route('user.vouchers') }}"
                                class="flex items-center gap-2 px-3 py-2.5 text-sm rounded-xl hover:bg-gray-50 text-gray-700 transition relative">
                                <i class="ri-coupon-3-line"></i>
                                Ví voucher
                                <span data-voucher-count-badge
                                    class="ml-auto bg-red-500 text-white text-[10px] font-bold rounded-full px-2 py-0.5 inline-flex items-center justify-center {{ $headerVoucherCount > 0 ? '' : 'hidden' }}">{{ $headerVoucherCount > 99 ? '99+' : $headerVoucherCount }}</span>
                            </a>

                            <form action="{{ route('logout') }}" method="POST" class="mt-1">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-2 px-3 py-2.5 text-sm rounded-xl hover:bg-red-50 text-red-600 transition">
                                    <i class="ri-logout-box-r-line"></i>
                                    Đăng xuất
                                </button>
                            </form>
                        </div>
                    </details>
                @else
                    <a wire:navigate href="{{ route('login') }}"
                        class="text-sm font-bold uppercase tracking-widest px-4 py-2 rounded-full border border-gray-200 text-gray-700 hover:border-[#bc9c75] hover:text-[#bc9c75] transition">
                        Đăng nhập
                    </a>
                @endauth
            </div>
        </nav>
    </div>

    <div class="bg-white border-b border-gray-100 hidden md:block">
        <div class="max-w-7xl mx-auto flex items-center justify-center h-16">
            <nav
                class="flex items-center justify-center gap-10 text-[clamp(11px,1.1vw,14px)] font-bold uppercase tracking-widest text-gray-800 w-full relative">
                <a wire:navigate href="{{ route('dashboard') }}"
                    class="menu-link hover:text-[#bc9c75] transition-all">Trang chủ</a>
                <a wire:navigate href="{{ route('user.introduce') }}"
                    class="menu-link hover:text-[#bc9c75] transition-all">Giới
                    thiệu</a>
                <a wire:navigate href="{{ route('user.product') }}"
                    class="menu-link hover:text-[#bc9c75] transition-all">Sản
                    phẩm</a>
                <a wire:navigate href="{{ route('user.collections') }}"
                    class="menu-link hover:text-[#bc9c75] transition-all">Bộ sưu
                    tập</a>
                <a wire:navigate href="{{ route('user.orders') }}"
                    class="menu-link hover:text-[#bc9c75] transition-all">Đơn hàng</a>
                <a wire:navigate href="{{ route('user.support') }}"
                    class="menu-link hover:text-[#bc9c75] transition-all">Hỗ trợ</a>
            </nav>
        </div>
    </div>

    <div id="header-nav-overlay"
        class="fixed inset-0 z-40 hidden bg-black/40 transition-opacity duration-300 md:hidden"></div>

    <div id="header-nav-drawer"
        class="fixed left-0 right-0 top-20 z-40 hidden border-b border-gray-100 bg-white shadow-2xl md:hidden">
        <div class="max-h-[calc(100vh-5rem)] overflow-y-auto px-4 py-4">
            <nav class="flex flex-col gap-2 text-sm font-bold uppercase tracking-widest text-gray-800">
                <a wire:navigate href="{{ route('dashboard') }}"
                    class="rounded-xl px-3 py-3 hover:bg-gray-50 hover:text-[#bc9c75] transition">Trang chủ</a>
                <a wire:navigate href="{{ route('user.introduce') }}"
                    class="rounded-xl px-3 py-3 hover:bg-gray-50 hover:text-[#bc9c75] transition">Giới thiệu</a>
                <a wire:navigate href="{{ route('user.product') }}"
                    class="rounded-xl px-3 py-3 hover:bg-gray-50 hover:text-[#bc9c75] transition">Sản phẩm</a>
                <a wire:navigate href="{{ route('user.product') }}"
                    class="rounded-xl px-3 py-3 hover:bg-gray-50 hover:text-[#bc9c75] transition">Bộ sưu tập</a>
                <a wire:navigate href="{{ route('user.orders') }}"
                    class="rounded-xl px-3 py-3 hover:bg-gray-50 hover:text-[#bc9c75] transition">Đơn hàng</a>
                <a wire:navigate href="{{ route('user.support') }}"
                    class="rounded-xl px-3 py-3 hover:bg-gray-50 hover:text-[#bc9c75] transition">Hỗ trợ</a>
                <a wire:navigate href="{{ route('user.contact') }}"
                    class="rounded-xl px-3 py-3 hover:bg-gray-50 hover:text-[#bc9c75] transition">Liên hệ</a>
            </nav>
        </div>
    </div>
</header>

<div id="breadcrumb-area" class="bg-gray-50 py-4 hidden mt-36 md:mt-36">
    <div class="max-w-7xl mx-auto px-6 text-sm">
        <a wire:navigate href="{{ route('dashboard') }}" class="text-gray-500 hover:text-black">Trang chủ</a>
        <span id="breadcrumb-current"></span>
    </div>
</div>

@auth
    @once
        <script>
            (() => {
                const renderCartCount = (rawCount) => {
                    const count = Number.isFinite(Number(rawCount)) ? Math.max(0, Number(rawCount)) : 0
                    const text = count > 99 ? '99+' : String(count)

                    document.querySelectorAll('[data-cart-count-badge]').forEach((badge) => {
                        badge.textContent = text

                        if (count > 0) {
                            badge.classList.remove('hidden')
                            badge.classList.add('flex')
                            return
                        }

                        badge.classList.add('hidden')
                        badge.classList.remove('flex')
                    })
                }

                window.addEventListener('cart-count-updated', (event) => {
                    const detail = event.detail || {}
                    renderCartCount(detail.count)
                })

                const renderVoucherCount = (rawCount) => {
                    const count = Number.isFinite(Number(rawCount)) ? Math.max(0, Number(rawCount)) : 0
                    const text = count > 99 ? '99+' : String(count)

                    document.querySelectorAll('[data-voucher-count-badge]').forEach((badge) => {
                        badge.textContent = text

                        if (count > 0) {
                            badge.classList.remove('hidden')
                            return
                        }

                        badge.classList.add('hidden')
                    })
                }

                window.addEventListener('voucher-count-updated', (event) => {
                    const detail = event.detail || {}
                    renderVoucherCount(detail.count)
                })
            })
            ()
        </script>
    @endonce
@endauth
