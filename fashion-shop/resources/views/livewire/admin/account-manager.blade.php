<div>
    <div class="account-body">
        <section class="account-section">
            <h3 class="section-title"><i class="fa-solid fa-key"></i> Đổi mật khẩu</h3>
            @if (session()->has('success'))
                <p style="color: #15803d; margin-bottom: 12px;">{{ session('success') }}</p>
            @endif

            <form class="password-form" wire:submit.prevent="updatePassword">
                <div class="form-group">
                    <label>Mật khẩu hiện tại</label>
                    <input type="password" wire:model.defer="current_password" placeholder="••••••••">
                    @error('current_password')
                        <p style="color: #dc2626; margin-top: 6px; font-size: 13px;">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Mật khẩu mới</label>
                        <input type="password" wire:model.defer="new_password" placeholder="Nhập mật khẩu mới">
                        @error('new_password')
                            <p style="color: #dc2626; margin-top: 6px; font-size: 13px;">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Xác nhận mật khẩu mới</label>
                        <input type="password" wire:model.defer="new_password_confirmation"
                            placeholder="Xác nhận lại mật khẩu">
                        @error('new_password_confirmation')
                            <p style="color: #dc2626; margin-top: 6px; font-size: 13px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="btn-primary">Cập nhật mật khẩu</button>
            </form>
        </section>
    </div>
</div>
