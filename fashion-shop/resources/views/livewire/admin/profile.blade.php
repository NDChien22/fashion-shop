<div>
    <div class="profile-body p-3 p-lg-4">
        @if (session()->has('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit.prevent="updateProfile" class="profile-form ">
            <div class="card border-0 shadow-sm mb-4 avatar-edit-section">
                <div class="card-body d-flex flex-column flex-md-row align-items-md-center gap-3">
                    <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center overflow-hidden"
                        style="width: 96px; height: 96px;">
                        @if ($avatar)
                            <img src="{{ $avatar->temporaryUrl() }}" alt="Avatar" class="w-100 h-100"
                                style="object-fit: cover;">
                        @elseif ($avatar_url)
                            <img src="{{ $avatar_url }}" alt="Avatar" class="w-100 h-100"
                                style="object-fit: cover;">
                        @else
                            <span class="fw-semibold text-secondary">{{ $avatar_initials }}</span>
                        @endif
                    </div>

                    <div style="flex: 1;">
                        <label for="avatar" class="form-label fw-semibold">Ảnh đại diện</label>
                        <div>
                            <label for="avatar" class="btn-outline"
                                style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                                <i class="fa-solid fa-camera"></i>
                                <span>Thay đổi ảnh</span>
                            </label>
                            <input id="avatar" type="file" wire:model="avatar"
                                accept="image/png,image/jpeg,image/jpg" style="display: none;">
                            <p class="hint-text">JPG, PNG. Tối đa 2MB.</p>
                            @error('avatar')
                                <p style="color: #dc2626; margin-top: 6px; font-size: 13px;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="row ">
                <div class="col-md-6 form-group">
                    <label for="full_name" class="form-label">Họ và tên</label>
                    <input id="full_name" type="text" class="form-control @error('full_name') is-invalid @enderror"
                        wire:model.defer="full_name" placeholder="Nhập họ tên">
                    @error('full_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 form-group">
                    <label for="email" class="form-label">Địa chỉ email</label>
                    <input id="email" type="email" class="form-control bg-light text-muted" wire:model="email"
                        readonly>
                </div>

                <div class="col-md-6 form-group">
                    <label for="phone_number" class="form-label">Số điện thoại</label>
                    <input id="phone_number" type="text"
                        class="form-control @error('phone_number') is-invalid @enderror" wire:model.defer="phone_number"
                        placeholder="Nhập số điện thoại">
                    @error('phone_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 form-group">
                    <label for="gender" class="form-label">Giới tính</label>
                    <select id="gender" class="form-select @error('gender') is-invalid @enderror"
                        wire:model.defer="gender">
                        <option value="">-- Chọn giới tính --</option>
                        <option value="male">Nam</option>
                        <option value="female">Nữ</option>
                        <option value="other">Khác</option>
                    </select>
                    @error('gender')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 form-group">
                    <label for="birthday" class="form-label">Ngày sinh</label>
                    <input id="birthday" type="text"
                        class="form-control @error('birthday_display') is-invalid @enderror"
                        wire:model.defer="birthday_display" placeholder="dd/mm/yyyy">
                    @error('birthday_display')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 form-group">
                    <label for="role" class="form-label">Vai trò</label>
                    <input id="role" type="text" class="form-control bg-light text-muted" wire:model="role"
                        readonly>
                </div>

                <div class="col-12 form-group">
                    <label for="address" class="form-label">Địa chỉ liên hệ</label>
                    <input id="address" type="text" class="form-control @error('address') is-invalid @enderror"
                        wire:model.defer="address"
                        placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố">
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-chevron-left me-1"></i> Quay lại
                </a>
                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>
