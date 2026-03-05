<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    #[Validate('required|string|max:255')]
    public string $full_name = '';

    public string $email = '';

    #[Validate('nullable|string|max:20')]
    public string $phone_number = '';

    #[Validate('nullable|in:male,female,other')]
    public string $gender = '';

    #[Validate('nullable|date_format:d/m/Y')]
    public string $birthday_display = '';

    #[Validate('nullable|string|max:255')]
    public string $address = '';

    public string $role = '';

    #[Validate('nullable|image|max:2048')]
    public $avatar;

    public string $avatar_url = '';

    public string $avatar_initials = 'US';

    public function mount(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $this->full_name = (string) ($user->full_name ?? '');
        $this->email = (string) ($user->email ?? '');
        $this->phone_number = (string) ($user->phone_number ?? '');
        $this->gender = (string) ($user->gender ?? '');
        $this->birthday_display = $user->birthday
            ? Carbon::parse($user->birthday)->format('d/m/Y')
            : '';
        $this->address = (string) ($user->address ?? '');
        $this->role = (string) ($user->role ?? 'customer');
        $this->avatar_url = $this->resolveAvatarUrl((string) ($user->avatar ?? ''));
        $this->avatar_initials = $this->buildInitials(
            (string) ($user->full_name ?: $user->username ?: $user->email)
        );
    }

    public function updateProfile(): void
    {
        $validatedData = $this->validate();

        $user = Auth::user();
        if (! $user) {
            return;
        }

        $avatarPath = $user->avatar;
        if ($this->avatar) {
            $fileExtension = $this->avatar->getClientOriginalExtension() ?: $this->avatar->guessExtension() ?: 'jpg';
            $fileName = Str::uuid()->toString().'.'.$fileExtension;

            $newAvatarPath = $this->avatar->storeAs('avatars', $fileName, 'public');

            if (is_string($avatarPath) && $avatarPath !== '') {
                Storage::disk('public')->delete($avatarPath);
            }

            $avatarPath = $newAvatarPath;
            $this->avatar_url = $this->resolveAvatarUrl($avatarPath);
        }

        $user->update([
            'full_name' => $validatedData['full_name'],
            'phone_number' => $validatedData['phone_number'] ?: null,
            'gender' => $validatedData['gender'] ?: null,
            'birthday' => $validatedData['birthday_display']
                ? Carbon::createFromFormat('d/m/Y', $validatedData['birthday_display'])->format('Y-m-d')
                : null,
            'address' => $validatedData['address'] ?: null,
            'avatar' => $avatarPath,
        ]);

        $this->avatar = null;
        $this->avatar_initials = $this->buildInitials($validatedData['full_name']);

        session()->flash('success', 'Cập nhật thông tin cá nhân thành công.');

        $this->redirectRoute('admin.admin-profile', navigate: true);
    }

    protected function resolveAvatarUrl(string $avatar): string
    {
        if ($avatar === '') {
            return '';
        }

        if (Str::startsWith($avatar, ['http://', 'https://', '/'])) {
            return $avatar;
        }

        return '/storage/'.$avatar;
    }

    protected function buildInitials(string $text): string
    {
        $value = trim($text);

        if ($value === '') {
            return 'US';
        }

        return Str::of($value)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $part) => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');
    }

    public function render()
    {
        return view('livewire.admin.profile');
    }
}
