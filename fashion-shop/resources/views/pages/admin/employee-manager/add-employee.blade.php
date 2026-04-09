@extends('layouts.admin-layout')
@section('title', 'Thêm nhân viên')

@section('page-header')
    <h1 id="page-title" class="text-xl font-semibold text-gray-800">
        Quản lý nhân viên
    </h1>

    <p class="text-xs text-gray-400 mt-1">
        <a href="{{ route('admin.employee-manager') }}" class="cursor-pointer hover:text-[#bc9c75] transition">
            Quản lý nhân viên
        </a>
        / <span id="breadcrumb-current" class="text-[#bc9c75] font-medium">Thêm nhân viên</span>
    </p>
@endsection

@section('content')

    <div>
        <form action="{{ route('admin.store-employee') }}" method="POST"
            class="bg-white p-8 rounded-2xl border border-gray-200 max-w-4xl mx-auto shadow-sm">
            @csrf

            <div id="employee-form" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="text-sm text-gray-600 font-medium">Mã nhân viên</label>
                    <input type="text" name="employee_code" value="{{ old('employee_code', $employeeCode) }}"
                        id="emp-code" readonly
                        class="w-full mt-1 bg-gray-100 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed">
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Họ và tên</label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" id="emp-fullname"
                        class="w-full mt-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#bc9c75]/20 outline-none transition-all">

                    @error('full_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" id="emp-email"
                        class="w-full mt-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#bc9c75]/20 outline-none transition-all">
                    @error('email')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Số điện thoại</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number') }}" id="emp-phone"
                        class="w-full mt-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#bc9c75]/20 outline-none transition-all">
                    @error('phone_number')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror

                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Giới tính</label>
                    <select id="emp-gender" name="gender"
                        class="w-full mt-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#bc9c75]/20 outline-none transition-all">
                        <option value="male" {{ old('gender', 'male') === 'male' ? 'selected' : '' }}>Nam</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Nữ</option>
                        <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Khác</option>
                    </select>
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Ngày sinh</label>
                    <input type="text" name="birthday" value="{{ old('birthday') }}" id="emp-birthday" maxlength="10"
                        inputmode="numeric" placeholder="dd/mm/yyyy"
                        class="w-full mt-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#bc9c75]/20 outline-none transition-all">
                    @error('birthday')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Chức vụ</label>
                    @php $selectedRole = strtolower((string) old('role', 'admin')); @endphp
                    <select id="emp-role" name="role" onchange="updateEmployeeCode()"
                        class="w-full mt-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#bc9c75]/20 outline-none transition-all">
                        <option value="admin" {{ $selectedRole === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="productmanager" {{ $selectedRole === 'productmanager' ? 'selected' : '' }}>
                            Product Manager
                        </option>
                        <option value="servicescustomer" {{ $selectedRole === 'servicescustomer' ? 'selected' : '' }}>
                            Services Customer
                        </option>
                    </select>
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" id="emp-username" readonly
                        class="w-full mt-1 bg-gray-100 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed">
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Mật khẩu</label>
                    <input type="text" name="password" id="emp-password" readonly
                        class="w-full mt-1 bg-gray-100 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed">
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Xác nhận mật khẩu</label>
                    <input type="text" name="password_confirmation" id="emp-password-confirmation" readonly
                        class="w-full mt-1 bg-gray-100 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-500 outline-none cursor-not-allowed">
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Lương</label>
                    <input type="number" min="0" step="0.01" name="salary" value="{{ old('salary') }}"
                        id="emp-salary"
                        class="w-full mt-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#bc9c75]/20 outline-none transition-all">
                    @error('salary')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p
                    @enderror
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-medium">Ngày vào làm</label>
                    <input type="text" name="hire_date" value="{{ old('hire_date') }}" id="emp-hire-date" maxlength="10"
                        inputmode="numeric" placeholder="dd/mm/yyyy"
                        class="w-full mt-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#bc9c75]/20 outline-none transition-all">
                    @error('hire_date')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-2">
                    <label class="text-sm text-gray-600 font-medium">Địa chỉ</label>
                    <input type="text" name="address" value="{{ old('address') }}" id="emp-address"
                        class="w-full mt-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#bc9c75]/20 outline-none transition-all">
                    @error('address')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-between items-center mt-8 border-t pt-5">
                <a href="{{ route('admin.employee-manager') }}"
                    class="text-gray-400 text-sm hover:text-gray-700 font-medium transition-colors">
                    ← Quay lại danh sách
                </a>
                <button type="submit" id="btn-submit-employee"
                    class="bg-[#bc9c75] text-white px-8 py-2.5 rounded-xl text-sm font-bold hover:shadow-lg hover:shadow-[#bc9c75]/20 transition-all active:scale-95">
                    Thêm nhân viên
                </button>
            </div>
        </form>
    </div>

    <script>
        (function() {
            const codeInput = document.getElementById('emp-code');
            const roleSelect = document.getElementById('emp-role');
            const fullNameInput = document.getElementById('emp-fullname');
            const birthdayInput = document.getElementById('emp-birthday');
            const usernameInput = document.getElementById('emp-username');
            const passwordInput = document.getElementById('emp-password');
            const passwordConfInput = document.getElementById('emp-password-confirmation');

            const prefixMap = {
                admin: 'adm',
                productmanager: 'prod',
                servicescustomer: 'serv',
            };

            const codePrefix = {
                admin: 'AD',
                productmanager: 'PM',
                servicescustomer: 'SC',
            };

            function randomCode(length) {
                const alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ0123456789';
                let value = '';

                for (let index = 0; index < length; index += 1) {
                    value += alphabet[Math.floor(Math.random() * alphabet.length)];
                }

                return value;
            }

            function removeAccents(str) {
                // Proper Vietnamese accent removal mapping
                const replacements = {
                    'À': 'A',
                    'Á': 'A',
                    'Ả': 'A',
                    'Ã': 'A',
                    'Ạ': 'A',
                    'à': 'a',
                    'á': 'a',
                    'ả': 'a',
                    'ã': 'a',
                    'ạ': 'a',
                    'Ă': 'A',
                    'Ắ': 'A',
                    'Ằ': 'A',
                    'Ẳ': 'A',
                    'Ẵ': 'A',
                    'Ặ': 'A',
                    'ă': 'a',
                    'ắ': 'a',
                    'ằ': 'a',
                    'ẳ': 'a',
                    'ẵ': 'a',
                    'ặ': 'a',
                    'Â': 'A',
                    'Ấ': 'A',
                    'Ầ': 'A',
                    'Ẩ': 'A',
                    'Ẫ': 'A',
                    'Ậ': 'A',
                    'â': 'a',
                    'ấ': 'a',
                    'ầ': 'a',
                    'ẩ': 'a',
                    'ẫ': 'a',
                    'ậ': 'a',
                    'È': 'E',
                    'É': 'E',
                    'Ẻ': 'E',
                    'Ẽ': 'E',
                    'Ẹ': 'E',
                    'è': 'e',
                    'é': 'e',
                    'ẻ': 'e',
                    'ẽ': 'e',
                    'ẹ': 'e',
                    'Ê': 'E',
                    'Ế': 'E',
                    'Ề': 'E',
                    'Ể': 'E',
                    'Ễ': 'E',
                    'Ệ': 'E',
                    'ê': 'e',
                    'ế': 'e',
                    'ề': 'e',
                    'ể': 'e',
                    'ễ': 'e',
                    'ệ': 'e',
                    'Ì': 'I',
                    'Í': 'I',
                    'Ỉ': 'I',
                    'Ĩ': 'I',
                    'Ị': 'I',
                    'ì': 'i',
                    'í': 'i',
                    'ỉ': 'i',
                    'ĩ': 'i',
                    'ị': 'i',
                    'Ò': 'O',
                    'Ó': 'O',
                    'Ỏ': 'O',
                    'Õ': 'O',
                    'Ọ': 'O',
                    'ò': 'o',
                    'ó': 'o',
                    'ỏ': 'o',
                    'õ': 'o',
                    'ọ': 'o',
                    'Ô': 'O',
                    'Ố': 'O',
                    'Ồ': 'O',
                    'Ổ': 'O',
                    'Ỗ': 'O',
                    'Ộ': 'O',
                    'ô': 'o',
                    'ố': 'o',
                    'ồ': 'o',
                    'ổ': 'o',
                    'ỗ': 'o',
                    'ộ': 'o',
                    'Ơ': 'O',
                    'Ớ': 'O',
                    'Ờ': 'O',
                    'Ở': 'O',
                    'Ỡ': 'O',
                    'Ợ': 'O',
                    'ơ': 'o',
                    'ớ': 'o',
                    'ờ': 'o',
                    'ở': 'o',
                    'ỡ': 'o',
                    'ợ': 'o',
                    'Ù': 'U',
                    'Ú': 'U',
                    'Ủ': 'U',
                    'Ũ': 'U',
                    'Ụ': 'U',
                    'ù': 'u',
                    'ú': 'u',
                    'ủ': 'u',
                    'ũ': 'u',
                    'ụ': 'u',
                    'Ư': 'U',
                    'Ứ': 'U',
                    'Ừ': 'U',
                    'Ử': 'U',
                    'Ữ': 'U',
                    'Ự': 'U',
                    'ư': 'u',
                    'ứ': 'u',
                    'ừ': 'u',
                    'ử': 'u',
                    'ữ': 'u',
                    'ự': 'u',
                    'Ỳ': 'Y',
                    'Ý': 'Y',
                    'Ỷ': 'Y',
                    'Ỹ': 'Y',
                    'Ỵ': 'Y',
                    'ỳ': 'y',
                    'ý': 'y',
                    'ỷ': 'y',
                    'ỹ': 'y',
                    'ỵ': 'y',
                    'Đ': 'D',
                    'đ': 'd'
                };

                let result = '';
                for (let i = 0; i < str.length; i++) {
                    result += replacements[str[i]] || str[i];
                }
                return result;
            }

            window.updateEmployeeCode = function updateEmployeeCode() {
                const prefix = codePrefix[roleSelect.value] || 'NV';
                const datePart = new Date().toISOString().slice(2, 10).replace(/-/g, '');

                codeInput.value = (prefix + datePart + randomCode(4)).toUpperCase();
                updateCredentials();
            };

            window.updateCredentials = function updateCredentials() {
                const fullName = fullNameInput.value.trim();
                const birthday = birthdayInput.value;
                const role = roleSelect.value;

                // Generate username from role abbreviation + initials of words except last + full last word
                if (fullName !== '') {
                    const roleAbbr = prefixMap[role] || 'usr';
                    const words = fullName.split(/\s+/);

                    let nameInitials = '';
                    let lastName = '';

                    // Get initials from all words except the last
                    for (let i = 0; i < words.length - 1; i++) {
                        if (words[i].length > 0) {
                            nameInitials += words[i].charAt(0).toLowerCase();
                        }
                    }

                    // Get full last word
                    if (words.length > 0) {
                        lastName = words[words.length - 1].toLowerCase();
                    }

                    nameInitials = removeAccents(nameInitials);
                    lastName = removeAccents(lastName);

                    usernameInput.value = roleAbbr + nameInitials + lastName;
                } else {
                    usernameInput.value = '';
                }

                // Generate password from name initials (lowercase) + birthday
                if (fullName !== '') {
                    const words = fullName.trim().split(/\s+/);
                    let initials = '';
                    words.forEach((word) => {
                        if (word.length > 0) {
                            initials += word.charAt(0).toLowerCase();
                        }
                    });
                    initials = removeAccents(initials);

                    let dateStr = '';
                    if (birthday !== '') {
                        const normalizedBirthday = birthday.replace(/\D/g, '');
                        if (normalizedBirthday.length === 8) {
                            dateStr = normalizedBirthday.slice(0, 4) + normalizedBirthday.slice(-2);
                        }
                    } else {
                        const now = new Date();
                        const d = String(now.getDate()).padStart(2, '0');
                        const m = String(now.getMonth() + 1).padStart(2, '0');
                        const y = String(now.getFullYear()).slice(-2);
                        dateStr = d + m + y;
                    }

                    passwordInput.value = initials + dateStr;
                    passwordConfInput.value = initials + dateStr;
                } else {
                    passwordInput.value = '';
                    passwordConfInput.value = '';
                }
            };

            roleSelect.addEventListener('change', updateEmployeeCode);
            fullNameInput.addEventListener('input', updateCredentials);
            birthdayInput.addEventListener('change', updateCredentials);

            window.updateEmployeeCode();
        })();
    </script>

@endsection
