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

    <form wire:submit.prevent="updatePassword">
        <div class="space-y-5">
            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Mật khẩu hiện tại</label>
                <input type="password"
                    class="w-full rounded-xl p-3 border border-gray-200 outline-none focus:border-[#bc9c75]"
                    wire:model.defer="current_password" placeholder="••••••••">
                @error('current_password')
                    <span class="mt-1 text-xs font-medium text-red-600">{{ $message }}</span>
                @enderror
            </div>
            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Mật khẩu mới</label>
                <input type="password"
                    class="w-full rounded-xl p-3 border border-gray-200 outline-none focus:border-[#bc9c75]"
                    wire:model.defer="new_password" placeholder="Mật khẩu mới">
                @error('new_password')
                    <span class="mt-1 text-xs font-medium text-red-600">{{ $message }}</span>
                @enderror
            </div>
            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Xác nhận lại mật khẩu</label>
                <input type="password"
                    class="w-full rounded-xl p-3 border border-gray-200 outline-none focus:border-[#bc9c75]"
                    wire:model.defer="new_password_confirmation" placeholder="Nhập lại mật khẩu mới">
                @error('new_password_confirmation')
                    <span class="mt-1 text-xs font-medium text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit"
                class="w-full bg-[#bc9c75] text-white py-3.5 rounded-xl font-bold shadow-lg shadow-[#bc9c75]/20 hover:brightness-95 active:scale-95 transition-all mt-4">
                Cập nhật mật khẩu
            </button>

            <a href="{{ route('admin.dashboard') }}"
                class="text-gray-400 hover:text-gray-600 font-semibold text-sm transition-all flex items-center gap-2">
                <i class="fa-solid fa-arrow-left-long"></i> Quay lại
            </a>
        </div>
    </form>
</div>
