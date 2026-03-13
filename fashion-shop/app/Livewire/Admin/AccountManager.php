<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class AccountManager extends Component
{
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public function updatePassword(): void
    {
        $validatedData = $this->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', Password::min(6)],
            'new_password_confirmation' => ['required'],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'current_password.current_password' => 'Mật khẩu hiện tại không chính xác.',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'new_password_confirmation.required' => 'Vui lòng xác nhận mật khẩu mới.',
        ]);

        $user = Auth::user();

        if (! $user) {
            return;
        }

        $user->update([
            'password' => Hash::make($validatedData['new_password']),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('success', 'Cập nhật mật khẩu thành công.');
    }

    public function render()
    {
        return view('livewire.admin.account-manager.account-manager');
    }
}
