<?php

namespace App\Livewire\Admin;

use App\Models\Employees;
use Livewire\Component;
use Livewire\WithPagination;

class EmployeeManager extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showDetailModal = false;

    public ?array $selectedEmployee = null;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function showEmployeeDetail(int $employeeId): void
    {
        $employee = Employees::query()
            ->with('user')
            ->find($employeeId);

        if (! $employee || ! $employee->user) {
            session()->flash('error', 'Không tìm thấy thông tin nhân viên.');
            return;
        }

        $user = $employee->user;
        $avatarPath = (string) ($user->avatar ?? '');
        $avatarUrl = '';

        if ($avatarPath !== '') {
            if (
                str_starts_with($avatarPath, 'http://') ||
                str_starts_with($avatarPath, 'https://') ||
                str_starts_with($avatarPath, '/')
            ) {
                $avatarUrl = $avatarPath;
            } else {
                $avatarUrl = '/storage/' . ltrim($avatarPath, '/');
            }
        }

        $this->selectedEmployee = [
            'id' => $employee->id,
            'employee_code' => (string) $employee->employee_code,
            'full_name' => (string) ($user->full_name ?? ''),
            'username' => (string) ($user->username ?? ''),
            'email' => (string) ($user->email ?? ''),
            'phone_number' => (string) ($user->phone_number ?? ''),
            'gender' => (string) ($user->gender ?? ''),
            'role' => (string) ($user->role ?? ''),
            'avatar_url' => $avatarUrl,
            'birthday' => $user->birthday?->format('d/m/Y') ?? 'Chưa cập nhật',
            'address' => (string) ($user->address ?? ''),
            'salary' => $employee->salary !== null && $employee->salary !== ''
                ? number_format((float) $employee->salary, 0, ',', '.') . ' đ'
                : 'Chưa cập nhật',
            'hire_date' => $employee->hire_date?->format('d/m/Y') ?? 'Chưa cập nhật',
        ];

        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedEmployee = null;
    }

    public function render()
    {
        $employees = Employees::query()
            ->with('user')
            ->when(trim($this->search) !== '', function ($query): void {
                $keyword = trim($this->search);

                $query->where('employee_code', 'like', '%' . $keyword . '%')
                    ->orWhere('salary', 'like', '%' . $keyword . '%')
                    ->orWhereHas('user', function ($userQuery) use ($keyword): void {
                        $userQuery->where('full_name', 'like', '%' . $keyword . '%')
                            ->orWhere('username', 'like', '%' . $keyword . '%')
                            ->orWhere('email', 'like', '%' . $keyword . '%')
                            ->orWhere('phone_number', 'like', '%' . $keyword . '%')
                            ->orWhere('role', 'like', '%' . $keyword . '%');
                    });
            })
            ->latest('id')
            ->paginate(10);

        return view('livewire.admin.employee-manager', [
            'employees' => $employees,
        ]);
    }
}
