@extends('layouts.user-static-layout')
@section('title', 'Trang cá nhân')

@section('main-content')
    @php
        $avatarPath = (string) ($user->avatar ?? '');
        $avatarUrl = '';
        $avatarVersion = optional($user->updated_at)->timestamp;

        if ($avatarPath !== '') {
            if (\Illuminate\Support\Str::startsWith($avatarPath, ['http://', 'https://', '/'])) {
                $avatarUrl = $avatarPath;
            } else {
                $avatarUrl = '/storage/' . ltrim($avatarPath, '/');
            }

            if ($avatarVersion) {
                $avatarUrl .= (str_contains($avatarUrl, '?') ? '&' : '?') . 'v=' . $avatarVersion;
            }
        }

        $displayName = trim((string) ($user->full_name ?: $user->username ?: $user->email));
        $avatarInitials = \Illuminate\Support\Str::of($displayName)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn($part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
            ->implode('');
    @endphp

    <div class="max-w-6xl mx-auto px-4 md:px-6 py-10">
        @if (session('success'))
            <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 lg:col-span-1">
                <p class="text-[11px] uppercase tracking-[0.2em] text-[#bc9c75] font-bold">Hồ sơ thành viên</p>

                <div class="mt-5 flex items-center gap-4">
                    @if ($avatarUrl !== '')
                        <img id="profile-avatar-main" src="{{ $avatarUrl }}" alt="Avatar"
                            class="w-20 h-20 rounded-full object-cover ring-4 ring-[#bc9c75]/15">
                    @else
                        <div id="profile-avatar-main-fallback"
                            class="w-20 h-20 rounded-full bg-[#bc9c75]/15 text-[#bc9c75] flex items-center justify-center text-2xl font-black ring-4 ring-[#bc9c75]/10">
                            {{ $avatarInitials !== '' ? $avatarInitials : 'U' }}
                        </div>
                    @endif

                    <div>
                        <h1 class="text-xl font-black text-gray-900 leading-tight">{{ $user->full_name ?: $user->username }}
                        </h1>
                        <p class="text-xs text-gray-500 mt-1 truncate">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="mt-6 space-y-2 text-sm">
                    <div class="flex items-center justify-between text-gray-500">
                        <span>Mã khách hàng</span>
                        <span class="font-bold text-gray-800">{{ $membership?->customer_code ?? 'Chưa cấp' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-500">
                        <span>Ngày tham gia</span>
                        <span class="font-bold text-gray-800">{{ optional($user->created_at)->format('d/m/Y') }}</span>
                    </div>
                </div>

                <a href="{{ route('user.profile.password') }}"
                    class="mt-6 inline-flex items-center justify-center gap-2 w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-700 text-xs uppercase tracking-wider font-bold hover:bg-gray-50 transition">
                    <i class="ri-lock-password-line"></i>
                    Đổi mật khẩu
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:col-span-2">
                <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                    <p class="text-xs uppercase tracking-widest text-gray-400 font-bold">Hạng thành viên</p>
                    <h3 class="text-2xl font-black text-[#bc9c75] mt-3">
                        {{ $membership?->membershipLevel?->name ?? 'Thành viên mới' }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-2">Giảm giá hiện tại: <span
                            class="font-bold text-gray-800">{{ $membership?->membershipLevel?->discount_rate ?? 0 }}%</span>
                    </p>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                    <p class="text-xs uppercase tracking-widest text-gray-400 font-bold">Điểm tích lũy</p>
                    <h3 class="text-2xl font-black text-gray-900 mt-3">
                        {{ number_format($membership?->points ?? 0, 0, ',', '.') }}</h3>
                    <p class="text-sm text-gray-500 mt-2">Tiếp tục mua sắm để nâng hạng nhanh hơn.</p>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm md:col-span-2">
                    <p class="text-xs uppercase tracking-widest text-gray-400 font-bold">Thông tin tài khoản</p>
                    <h3 class="text-lg font-black text-gray-900 mt-3">{{ $user->email }}</h3>
                    <p class="text-sm text-gray-500 mt-2">Bạn có thể cập nhật thông tin và ảnh đại diện ở form bên dưới.</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 md:p-8">
            <h2 class="text-xl font-black text-gray-900 mb-6">Cập nhật thông tin cá nhân</h2>

            <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Ảnh đại diện</label>
                    <div class="flex items-center gap-4">
                        @if ($avatarUrl !== '')
                            <img id="profile-avatar-preview" src="{{ $avatarUrl }}" alt="Avatar hiện tại"
                                class="w-16 h-16 rounded-full object-cover border border-gray-200">
                        @else
                            <div id="profile-avatar-preview-fallback"
                                class="w-16 h-16 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center font-bold border border-gray-200">
                                {{ $avatarInitials !== '' ? $avatarInitials : 'U' }}
                            </div>
                        @endif
                        <input id="avatar-input" type="file" name="avatar" accept="image/*"
                            class="block w-full text-sm text-gray-700 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:uppercase file:tracking-wider file:bg-[#bc9c75]/15 file:text-[#8d6e49] hover:file:bg-[#bc9c75]/25">
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Hỗ trợ JPG, PNG, WEBP. Tối đa 2MB.</p>
                    @error('avatar')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Họ và tên</label>
                        <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-[#bc9c75] focus:bg-white">
                        @error('full_name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Số điện
                            thoại</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-[#bc9c75] focus:bg-white">
                        @error('phone_number')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Giới tính</label>
                        <select name="gender"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-[#bc9c75] focus:bg-white">
                            <option value="">-- Chọn giới tính --</option>
                            <option value="male" @selected(old('gender', $user->gender) === 'male')>Nam</option>
                            <option value="female" @selected(old('gender', $user->gender) === 'female')>Nữ</option>
                            <option value="other" @selected(old('gender', $user->gender) === 'other')>Khác</option>
                        </select>
                        @error('gender')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Ngày sinh</label>
                        <input type="date" name="birthday"
                            value="{{ old('birthday', optional($user->birthday)->format('Y-m-d')) }}"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-[#bc9c75] focus:bg-white">
                        @error('birthday')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Địa chỉ</label>
                    <textarea name="address" rows="3"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-[#bc9c75] focus:bg-white">{{ old('address', $user->address) }}</textarea>
                    @error('address')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="submit"
                        class="px-8 py-3 bg-[#bc9c75] text-white rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-[#a0805a] transition">
                        Lưu thông tin
                    </button>
                </div>
            </form>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            const avatarInput = document.getElementById('avatar-input');
            if (!avatarInput) return;

            const mainAvatarImg = document.getElementById('profile-avatar-main');
            const mainAvatarFallback = document.getElementById('profile-avatar-main-fallback');
            const previewAvatarImg = document.getElementById('profile-avatar-preview');
            const previewAvatarFallback = document.getElementById('profile-avatar-preview-fallback');
            const headerAvatars = Array.from(document.querySelectorAll('.js-header-avatar'));

            const ensureImageElement = (targetImg, fallbackEl, className, altText) => {
                if (targetImg) return targetImg;
                if (!fallbackEl || !fallbackEl.parentElement) return null;

                const img = document.createElement('img');
                img.className = className;
                img.alt = altText;
                fallbackEl.parentElement.replaceChild(img, fallbackEl);
                return img;
            };

            avatarInput.addEventListener('change', function() {
                const file = avatarInput.files && avatarInput.files[0] ? avatarInput.files[0] : null;
                if (!file) return;

                const previewUrl = URL.createObjectURL(file);

                const mainImg = ensureImageElement(
                    mainAvatarImg,
                    mainAvatarFallback,
                    'w-20 h-20 rounded-full object-cover ring-4 ring-[#bc9c75]/15',
                    'Avatar'
                );

                const previewImg = ensureImageElement(
                    previewAvatarImg,
                    previewAvatarFallback,
                    'w-16 h-16 rounded-full object-cover border border-gray-200',
                    'Avatar hiện tại'
                );

                if (mainImg) {
                    mainImg.src = previewUrl;
                    mainImg.id = 'profile-avatar-main';
                }

                if (previewImg) {
                    previewImg.src = previewUrl;
                    previewImg.id = 'profile-avatar-preview';
                }

                headerAvatars.forEach((img) => {
                    img.src = previewUrl;
                });
            });
        })();
    </script>
@endpush
