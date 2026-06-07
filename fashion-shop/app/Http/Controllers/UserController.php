<?php

namespace App\Http\Controllers;

use App\Models\CustomerMembershipLevel;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    public function profile(Request $request): View
    {
        $user = $request->user();
        $membership = CustomerMembershipLevel::query()
            ->with('membershipLevel')
            ->where('user_id', $user->id)
            ->first();

        return view('pages.user.profile.index', [
            'user' => $user,
            'membership' => $membership,
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female,other'],
            'birthday' => ['nullable', 'date'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'full_name.required' => 'Vui lòng nhập họ và tên.',
            'gender.in' => 'Giới tính không hợp lệ.',
            'birthday.date' => 'Ngày sinh không hợp lệ.',
            'avatar.image' => 'Ảnh đại diện phải là tệp hình ảnh.',
            'avatar.mimes' => 'Ảnh đại diện chỉ hỗ trợ jpg, jpeg, png, webp.',
            'avatar.max' => 'Ảnh đại diện không được vượt quá 2MB.',
        ]);

        /** @var User $user */
        $user = $request->user();

        if ($request->hasFile('avatar')) {
            $avatarFile = $request->file('avatar');
            $extension = $avatarFile->getClientOriginalExtension() ?: 'jpg';
            $fileName = sprintf('user-%d-%s.%s', $user->id, Str::uuid()->toString(), $extension);
            $newAvatarPath = $avatarFile->storeAs('avatars', $fileName, 'public');

            if (is_string($user->avatar) && $user->avatar !== '' && ! Str::startsWith($user->avatar, ['http://', 'https://', '/'])) {
                Storage::disk('public')->delete($user->avatar);
            }

            $validated['avatar'] = $newAvatarPath;
        }

        $user->update($validated);

        return back()->with('success', 'Cập nhật hồ sơ thành công.');
    }

    public function changePassword(): View
    {
        return view('pages.user.profile.change-password');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:5', 'confirmed'],
            'password_confirmation' => ['required'],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'current_password.current_password' => 'Mật khẩu hiện tại không chính xác.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 5 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'password_confirmation.required' => 'Vui lòng xác nhận mật khẩu mới.',
        ]);

        $user = $request->user();
        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('user.profile.password')->with('success', 'Đổi mật khẩu thành công.');
    }
}
