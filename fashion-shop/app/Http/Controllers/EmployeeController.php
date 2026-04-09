<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class EmployeeController extends Controller
{
    protected const ROLE_PREFIXES = [
        'admin' => 'AD',
        'productmanager' => 'PM',
        'servicescustomer' => 'SC',
    ];

    public function EmployeeManagerView()
    {
        return view('pages.admin.employee-manager.employee-manager');
    }


    // Thêm nhân viên
    public function addEmployeeView()
    {
        return view('pages.admin.employee-manager.add-employee', [
            'employeeCode' => $this->generateEmployeeCode('admin'),
            'defaultUsername' => '',
            'defaultPassword' => '',
        ]);
    }

    public function storeEmployeeHandler(Request $request)
    {
        $validated = $this->validateEmployeeForm($request);
        $role = $this->normalizeRole($validated['role']);
        $generatedUsername = $this->generateUsername($role, $validated['full_name']);
        $birthday = $this->normalizeDmyDate($validated['birthday'] ?? null);
        $hireDate = $this->normalizeDmyDate($validated['hire_date'] ?? null);
        $generatedPassword = $this->generatePassword($validated['full_name'], $birthday);

        DB::transaction(function () use ($validated, $role, $generatedUsername, $generatedPassword, $birthday, $hireDate): void {
            $employeeCode = $this->generateEmployeeCode($role);

            $user = User::create([
                'username' => $generatedUsername,
                'email' => $validated['email'],
                'password' => Hash::make($generatedPassword),
                'full_name' => $validated['full_name'],
                'phone_number' => $validated['phone_number'] ?: null,
                'gender' => $validated['gender'] ?: null,
                'birthday' => $birthday,
                'address' => $validated['address'] ?: null,
                'role' => $role,
            ]);

            Employees::create([
                'user_id' => $user->id,
                'employee_code' => $employeeCode,
                'salary' => $validated['salary'] ?: null,
                'hire_date' => $hireDate,
            ]);
        });

        return redirect()
            ->route('admin.employee-manager')
            ->with('success', 'Thêm nhân viên thành công.');
    }

    public function editEmployeeView(Employees $employee)
    {
        $employee->load('user');

        if (! $employee->user) {
            abort(404);
        }

        return view('pages.admin.employee-manager.edit-employee', [
            'employee' => $employee,
        ]);
    }

    public function updateEmployeeHandler(Request $request, Employees $employee)
    {
        $employee->load('user');

        if (! $employee->user) {
            abort(404);
        }

        $validated = $request->validate([
            'role' => ['required', Rule::in(array_keys(self::ROLE_PREFIXES))],
            'salary' => ['nullable', 'numeric', 'min:0'],
        ], [
            'role.required' => 'Vui lòng chọn chức vụ.',
            'role.in' => 'Chức vụ không hợp lệ.',
            'salary.numeric' => 'Lương phải là số hợp lệ.',
            'salary.min' => 'Lương không được nhỏ hơn 0.',
        ]);

        $role = $this->normalizeRole($validated['role']);

        DB::transaction(function () use ($validated, $employee, $role): void {
            $user = $employee->user;

            if ($user->role !== $role) {
                $user->role = $role;
                $user->save();
            }

            $salary = $validated['salary'] ?? null;

            if ((string) $employee->salary !== (string) $salary) {
                $employee->salary = $salary;
                $employee->save();
            }
        });

        return redirect()
            ->route('admin.edit-employee', $employee)
            ->with('success', 'Cập nhật nhân viên thành công.');
    }

    public function deleteEmployee(Employees $employee)
    {
        $employee->load('user');

        DB::transaction(function () use ($employee): void {
            if ($employee->user) {
                $employee->user->delete();
            } else {
                $employee->delete();
            }
        });

        return redirect()
            ->route('admin.employee-manager')
            ->with('success', 'Xóa nhân viên thành công.');
    }

    protected function validateEmployeeForm(Request $request, ?Employees $employee = null): array
    {
        $userId = $employee?->user_id;

        return $request->validate([
            'employee_code' => $employee === null
                ? ['nullable', 'string', 'max:255']
                : [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('employees', 'employee_code')->ignore($employee?->id),
                ],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'birthday' => ['nullable', 'date_format:d/m/Y'],
            'address' => ['nullable', 'string', 'max:255'],
            'role' => ['required', Rule::in(array_keys(self::ROLE_PREFIXES))],
            'username' => $employee === null
                ? ['nullable', 'string', 'max:255']
                : [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('users', 'username')->ignore($userId),
                ],
            'password' => $employee === null
                ? ['nullable', 'string', 'min:6']
                : ['nullable', 'confirmed', Password::min(6)],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'hire_date' => ['nullable', 'date_format:d/m/Y'],
        ], [
            'full_name.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email này đã được sử dụng.',
            'role.required' => 'Vui lòng chọn chức vụ.',
            'role.in' => 'Chức vụ không hợp lệ.',
            'username.required' => 'Vui lòng nhập username.',
            'username.unique' => 'Username này đã tồn tại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'salary.numeric' => 'Lương phải là số hợp lệ.',
            'salary.min' => 'Lương không được nhỏ hơn 0.',
            'birthday.date_format' => 'Ngày sinh phải theo dạng dd/mm/yyyy.',
            'hire_date.date_format' => 'Ngày vào làm phải theo dạng dd/mm/yyyy.',
        ]);
    }

    protected function generateEmployeeCode(string $role): string
    {
        $prefix = self::ROLE_PREFIXES[$this->normalizeRole($role)] ?? 'NV';

        do {
            $code = $prefix . now()->format('ymd') . Str::upper(Str::random(4));
        } while (Employees::query()->where('employee_code', $code)->exists());

        return $code;
    }

    protected function normalizeRole(string $role): string
    {
        return match ($role) {
            'Admin', 'admin' => 'admin',
            'Manager', 'productmanager' => 'productmanager',
            'Staff', 'servicescustomer' => 'servicescustomer',
            default => $role,
        };
    }

    protected function generateUsername(string $role, string $fullName): string
    {
        $normalizedRole = $this->normalizeRole($role);
        
        // Get role abbreviation (3-4 characters)
        $roleAbbr = match ($normalizedRole) {
            'admin' => 'adm',
            'productmanager' => 'prod',
            'servicescustomer' => 'serv',
            default => mb_strtolower(mb_substr($normalizedRole, 0, 3)),
        };

        // Get name part: initials of all words except last + full last word (lowercase, no accents)
        $parts = preg_split('/\s+/', trim($fullName));
        $nameInitials = '';
        $lastName = '';

        if (count($parts) > 0) {
            // Get initials from all words except the last
            for ($i = 0; $i < count($parts) - 1; $i++) {
                if ($parts[$i] !== '') {
                    $nameInitials .= mb_strtolower(mb_substr($parts[$i], 0, 1));
                }
            }

            // Get full last word
            if (count($parts) > 0) {
                $lastName = mb_strtolower($parts[count($parts) - 1]);
            }
        }

        // Remove accents from all name parts
        $nameInitials = $this->removeAccents($nameInitials);
        $lastName = $this->removeAccents($lastName);

        // Create base username
        $baseUsername = $roleAbbr . $nameInitials . $lastName;

        // Check for uniqueness and append number if needed
        $username = $baseUsername;
        $counter = 1;
        while (User::query()->where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }

    protected function generatePassword(string $fullName, ?string $birthday = null): string
    {
        // Get initials from full name (lowercase)
        $parts = preg_split('/\s+/', trim($fullName));
        $initials = '';
        foreach ($parts as $part) {
            if ($part !== '') {
                $initials .= mb_strtolower(mb_substr($part, 0, 1));
            }
        }

        $initials = $this->removeAccents($initials);

        // Get date in ddmmyy format
        if ($birthday && $birthday !== '') {
            $date = \Carbon\Carbon::createFromFormat('Y-m-d', $birthday);
            $dateStr = $date->format('dmy');
        } else {
            $dateStr = date('dmy');
        }

        return $initials . $dateStr;
    }

    protected function normalizeDmyDate(?string $date): ?string
    {
        if (! $date) {
            return null;
        }

        $normalized = preg_replace('/\D/', '', $date);

        if (strlen($normalized) !== 8) {
            return null;
        }

        $carbonDate = \Carbon\Carbon::createFromFormat('dmY', $normalized);

        return $carbonDate->format('Y-m-d');
    }

    protected function removeAccents(string $str): string
    {
        // Normalize NFD form and remove combining marks
        if (function_exists('normalizer_normalize')) {
            $str = normalizer_normalize($str, \Normalizer::NFD);
            $str = preg_replace('/[\p{Mn}]/u', '', $str);
        }
        return $str;
    }
}
