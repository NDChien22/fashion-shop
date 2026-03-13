<div>
    <div>

        @if (session()->has('success'))
            <div id="profile-success-toast" x-data="{ show: true }" x-init="setTimeout(() => show = false, 2500)" x-show="show"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
                class="fixed top-5 right-5 z-50 min-w-[280px] max-w-sm rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 shadow-lg transition-all duration-300"
                role="status" aria-live="polite">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-circle-check mt-0.5"></i>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <form wire:submit.prevent="updateProfile" class="space-y-6">
            <div class="bg-[#fffbf7] rounded-2xl p-6 flex items-center gap-6 mb-8 border border-orange-50">
                <div class="h-20 w-20 rounded-full overflow-hidden flex items-center justify-center">
                    @if ($avatar)
                        <img src="{{ $avatar->temporaryUrl() }}" alt="Avatar"
                            class="w-full h-full rounded-full object-cover">
                    @elseif ($avatar_url)
                        <img src="{{ $avatar_url }}" alt="Avatar" class="w-full h-full rounded-full object-cover">
                    @else
                        <div
                            class="h-20 w-20 bg-[#bc9c75] text-white rounded-full flex items-center justify-center text-2xl font-bold shadow-inner">
                            {{ $avatar_initials }}</div>
                    @endif
                </div>
                <div>
                    <label for="avatar" class="block text-sm font-bold text-gray-700 mb-2">Ảnh đại diện</label>
                    <div>
                        <label for="avatar"
                            class="bg-white border border-[#bc9c75] text-[#bc9c75] px-4 py-2 rounded-lg text-xs font-bold hover:bg-[#bc9c75] hover:text-white transition-all shadow-sm">
                            <i class="fa-solid fa-camera mr-2"></i>
                            <span>Thay đổi ảnh</span>
                        </label>
                        <input id="avatar" type="file" wire:model="avatar" accept="image/png,image/jpeg,image/jpg"
                            style="display: none;">
                        <p class="text-[10px] text-gray-400 mt-2 uppercase tracking-wider">JPG, PNG. Tối đa 2MB.</p>
                        @error('avatar')
                            <span class="mt-1 text-xs font-medium text-red-600">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700">Họ và tên</label>
                    <input type="text"
                        class="w-full rounded-xl p-3 bg-white border border-gray-200 outline-none focus:border-[#bc9c75] focus:ring-2 focus:ring-[#bc9c75]/10 transition-all"
                        @error('full_name') is-invalid @enderror" wire:model.defer="full_name"
                        placeholder="Nhập họ tên">

                    @error('full_name')
                        <span class="mt-1 text-xs font-medium text-red-600">{{ $message }}</span>
                    @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700">Email</label>
                    <input type="email"
                        class="w-full rounded-xl p-3 bg-white border border-gray-200 outline-none focus:border-[#bc9c75] focus:ring-2 focus:ring-[#bc9c75]/10 transition-all"
                        @error('email') is-invalid @enderror" wire:model.defer="email"
                        placeholder="Nhập email">
                    
                    @error('email')
                        <span class="mt-1 text-xs font-medium text-red-600">{{ $message }}</span>
                    @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700">Số điện thoại</label>
                    <input type="text"
                        class="w-full rounded-xl p-3 bg-white border border-gray-200 outline-none focus:border-[#bc9c75]"
                        @error('phone_number') is-invalid @enderror" wire:model.defer="phone_number"
                        placeholder="Nhập số điện thoại">

                    @error('phone_number')
                        <span class="mt-1 text-xs font-medium text-red-600">{{ $message }}</span>
                    @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700">Giới tính</label>
                    <select
                        class="w-full rounded-xl p-3 bg-white border border-gray-200 outline-none focus:border-[#bc9c75] appearance-none cursor-pointer @error('gender') is-invalid @enderror"
                        wire:model="gender">
                        <option value="">-- Chọn giới tính --</option>
                        <option value="male">Nam</option>
                        <option value="female">Nữ</option>
                        <option value="other">Khác</option>
                    </select>
                    @error('gender')
                        <span class="mt-1 text-xs font-medium text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700">Ngày sinh</label>
                    <input type="text"
                        class="w-full rounded-xl p-3 bg-white border border-gray-200 outline-none focus:border-[#bc9c75] focus:ring-2 focus:ring-[#bc9c75]/10 transition-all uppercase @error('birthday_display') is-invalid @enderror"
                        wire:model.defer="birthday_display" placeholder="dd/mm/yyyy">
                    @error('birthday_display')
                        <span class="mt-1 text-xs font-medium text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700">Chức vụ</label>
                    <input
                        type="text"class="w-full rounded-xl p-3 bg-gray-50 border border-gray-100 text-gray-400 cursor-not-allowed "
                        wire:model="role" readonly>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700">Địa chỉ </label>
                    <input type="text"
                        class="w-full rounded-xl p-3 bg-white border border-gray-200 outline-none focus:border-[#bc9c75] focus:ring-2 focus:ring-[#bc9c75]/10 transition-all"
                        wire:model.defer="address" placeholder="Nhập địa chỉ">
                    @error('address')
                        <span class="mt-1 text-xs font-medium text-red-600">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="mt-10 pt-8 border-t border-gray-100 flex justify-between items-center">
                <a href="{{ route('admin.dashboard') }}"
                    class="text-gray-400 hover:text-gray-600 font-semibold text-sm transition-all flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left-long"></i> Quay lại
                </a>
                <button type="submit"
                    class="bg-[#bc9c75] text-white px-10 py-3 rounded-xl font-bold shadow-lg shadow-[#bc9c75]/20 hover:scale-[1.02] active:scale-95 transition-all">
                    Lưu thay đổi
                </button>
            </div>
        </form>

    </div>
</div>
