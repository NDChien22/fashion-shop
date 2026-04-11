<div>
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="relative w-full sm:w-80">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <i class="fa-solid fa-magnifying-glass text-gray-400 text-[10px]"></i>
            </span>
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Tìm theo tên, mã, email, username..."
                class="w-full bg-gray-50 border border-gray-200 rounded-xl py-2.5 pl-10 pr-4 text-xs focus:outline-none focus:ring-1 focus:ring-[#bc9c75] transition-all">
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <a href="{{ route('admin.add-employee') }}"
                class="px-5 py-2.5 bg-[#bc9c75] text-white rounded-xl text-xs font-bold shadow-md shadow-[#bc9c75]/20 hover:opacity-90 transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-user-plus text-[10px]"></i> Thêm nhân viên
            </a>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-50 overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse min-w-175">
                <thead class="bg-[#fcfaf8] border-b border-gray-50">
                    <tr>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nhân viên
                        </th>
                        <th
                            class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                            Chức vụ</th>
                        <th
                            class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                            Liên hệ</th>
                        <th
                            class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                            Ngày gia nhập</th>
                        <th
                            class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                            Thao tác</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-50 text-[12px]">
                    @forelse ($employees as $employee)
                        @php
                            $user = $employee->user;
                            $avatar = (string) ($user?->avatar ?? '');
                            $avatarUrl = '';
                            $roleValue = strtolower((string) ($user?->role ?? ''));
                            $roleLabel = match ($roleValue) {
                                'admin' => 'Admin',
                                'productmanager', 'manager' => 'Product Manager',
                                'servicescustomer', 'staff' => 'Services Customer',
                                default => $user?->role ?? 'Chưa cập nhật',
                            };
                            $roleClass = match ($roleValue) {
                                'admin' => 'bg-red-50 text-red-500 border-red-100',
                                'productmanager', 'manager' => 'bg-amber-50 text-amber-600 border-amber-100',
                                'servicescustomer', 'staff' => 'bg-sky-50 text-sky-600 border-sky-100',
                                default => 'bg-slate-50 text-slate-500 border-slate-100',
                            };

                            if ($avatar !== '') {
                                if (
                                    str_starts_with($avatar, 'http://') ||
                                    str_starts_with($avatar, 'https://') ||
                                    str_starts_with($avatar, '/')
                                ) {
                                    $avatarUrl = $avatar;
                                } else {
                                    $avatarUrl = '/storage/' . ltrim($avatar, '/');
                                }
                            }

                            $initials = collect(
                                preg_split(
                                    '/\s+/',
                                    trim((string) ($user?->full_name ?: $user?->username ?: $user?->email)),
                                ),
                            )
                                ->filter()
                                ->take(2)
                                ->map(fn($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                                ->implode('');
                        @endphp

                        <tr class="hover:bg-gray-50/50 transition-all group cursor-pointer"
                            wire:click="showEmployeeDetail({{ $employee->id }})">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if ($avatarUrl !== '')
                                        <img src="{{ $avatarUrl }}"
                                            alt="{{ $user?->full_name ?? $employee->employee_code }}"
                                            class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm">
                                    @else
                                        <div
                                            class="w-10 h-10 rounded-full bg-[#bc9c75]/10 flex items-center justify-center text-[#bc9c75] font-black border-2 border-white shadow-sm">
                                            {{ $initials !== '' ? $initials : 'NV' }}
                                        </div>
                                    @endif

                                    <div>
                                        <div class="font-black text-gray-800 uppercase tracking-tighter">
                                            {{ $user?->full_name ?: 'Chưa cập nhật' }}
                                        </div>
                                        <div class="text-[10px] text-gray-400">Mã nhân viên:
                                            {{ $employee->employee_code }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span
                                    class="px-3 py-1 rounded-lg font-black text-[9px] uppercase tracking-widest border {{ $roleClass }}">
                                    {{ $roleLabel }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <div class="font-bold text-gray-600">{{ $user?->phone_number ?: 'Chưa cập nhật' }}
                                </div>
                                <div class="text-[10px] text-gray-400">{{ $user?->email ?: 'Chưa cập nhật' }}</div>
                            </td>

                            <td class="px-6 py-4 text-center text-gray-400 font-medium">
                                {{ $employee->hire_date?->format('d/m/Y') ?? 'Chưa cập nhật' }}
                            </td>

                            <td class="px-6 py-4 text-center space-x-2" wire:click.stop>
                                <a href="{{ route('admin.edit-employee', $employee) }}"
                                    class="text-gray-400 hover:text-[#bc9c75] transition-all inline-flex"
                                    title="Chỉnh sửa">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>

                                <form action="{{ route('admin.delete-employee', $employee) }}" method="POST"
                                    class="inline" data-confirm-delete="1"
                                    data-confirm-message="Bạn có chắc muốn xóa nhân viên này không?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-gray-400 hover:text-red-500 transition-all inline-flex"
                                        title="Xóa">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                Chưa có nhân viên nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-50">
            {{ $employees->links() }}
        </div>
    </div>


    {{-- Modal chi tiết nhân viên --}}
    @if ($showDetailModal && $selectedEmployee)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
            wire:click.self="closeDetailModal">
            <div
                class="bg-white w-full max-w-4xl rounded-4xl shadow-2xl overflow-hidden relative animate-in fade-in zoom-in duration-300">
                <button type="button" wire:click="closeDetailModal"
                    class="absolute top-5 right-5 w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 shadow-sm flex items-center justify-center z-10 transition-all">
                    <i class="fa-solid fa-xmark text-gray-500"></i>
                </button>

                <div class="grid grid-cols-1 md:grid-cols-2">
                    <div class="p-8 bg-[#fcfaf8] flex flex-col items-center border-r border-gray-50 min-h-130">
                        @php
                            $detailInitials = collect(
                                preg_split('/\s+/', trim((string) ($selectedEmployee['full_name'] ?? 'NV'))),
                            )
                                ->filter()
                                ->take(2)
                                ->map(fn($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                                ->implode('');
                        @endphp

                        <div
                            class="w-32 h-32 rounded-3xl bg-linear-to-br from-[#bc9c75] to-[#d9b98d] text-white flex items-center justify-center text-4xl font-black shadow-lg shadow-[#bc9c75]/25 ring-4 ring-white overflow-hidden mb-4">
                            @if (!empty($selectedEmployee['avatar_url']))
                                <img src="{{ $selectedEmployee['avatar_url'] }}"
                                    alt="{{ $selectedEmployee['full_name'] }}" class="w-full h-full object-cover">
                            @else
                                {{ $detailInitials !== '' ? $detailInitials : 'NV' }}
                            @endif
                        </div>

                        <h2 class="text-xl font-black text-gray-800 text-center uppercase tracking-tight">
                            {{ $selectedEmployee['full_name'] }}
                        </h2>
                        <p class="text-xs text-gray-400 mt-1">Mã nhân viên: {{ $selectedEmployee['employee_code'] }}
                        </p>

                        <span
                            class="mt-3 px-4 py-1.5 bg-[#bc9c75]/10 text-[#bc9c75] text-[10px] font-black rounded-full uppercase tracking-[0.2em]">
                            @php
                                $selectedRoleValue = strtolower((string) ($selectedEmployee['role'] ?? ''));
                                $selectedRoleLabel = match ($selectedRoleValue) {
                                    'admin' => 'Admin',
                                    'productmanager', 'manager' => 'Product Manager',
                                    'servicescustomer', 'staff' => 'Services Customer',
                                    default => $selectedEmployee['role'] ?: 'Chưa cập nhật',
                                };
                            @endphp
                            {{ $selectedRoleLabel }}
                        </span>

                        <div class="mt-8 w-full space-y-3">
                            <div class="p-4 bg-white rounded-2xl border border-gray-100 shadow-sm">
                                <p class="text-[10px] text-gray-400 uppercase tracking-widest">Trạng thái</p>
                                <p class="text-sm font-bold text-green-500 flex items-center gap-2 mt-1">
                                    <span class="w-2 h-2 rounded-full bg-green-500"></span> Đang làm việc
                                </p>
                            </div>

                            <div class="p-4 bg-white rounded-2xl border border-gray-100 shadow-sm">
                                <p class="text-[10px] text-gray-400 uppercase tracking-widest">Số điện thoại</p>
                                <p class="text-sm font-bold text-gray-800 mt-1">
                                    {{ $selectedEmployee['phone_number'] ?: 'Chưa cập nhật' }}</p>
                            </div>

                            <div class="p-4 bg-white rounded-2xl border border-gray-100 shadow-sm">
                                <p class="text-[10px] text-gray-400 uppercase tracking-widest">Email</p>
                                <p class="text-sm font-bold text-gray-800 mt-1 break-all">
                                    {{ $selectedEmployee['email'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8 max-h-[85vh] overflow-y-auto bg-white">
                        <div class="mb-6 border-b border-gray-50 pb-4">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Thông tin liên
                                hệ & hồ sơ</span>
                        </div>

                        <div
                            class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8 bg-gray-50 rounded-3xl border border-gray-100 p-4">
                            <div class="p-3">
                                <p class="text-[10px] text-gray-400 font-bold uppercase">Username</p>
                                <p class="text-sm font-bold text-gray-700 mt-1">{{ $selectedEmployee['username'] }}</p>
                            </div>
                            <div class="p-3">
                                <p class="text-[10px] text-gray-400 font-bold uppercase">Chức vụ</p>
                                <p class="text-sm font-bold text-gray-700 mt-1">
                                    {{ $selectedRoleLabel }}</p>
                            </div>
                            <div class="p-3">
                                <p class="text-[10px] text-gray-400 font-bold uppercase">Ngày sinh</p>
                                <p class="text-sm font-bold text-gray-700 mt-1">{{ $selectedEmployee['birthday'] }}</p>
                            </div>
                            <div class="p-3">
                                <p class="text-[10px] text-gray-400 font-bold uppercase">Ngày vào làm</p>
                                <p class="text-sm font-bold text-gray-700 mt-1">{{ $selectedEmployee['hire_date'] }}
                                </p>
                            </div>
                            <div class="p-3 col-span-2">
                                <p class="text-[10px] text-gray-400 font-bold uppercase">Địa chỉ thường trú</p>
                                <p class="text-sm font-semibold text-gray-600 leading-relaxed mt-1">
                                    {{ $selectedEmployee['address'] ?: 'Chưa cập nhật' }}</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Hợp đồng
                                    & lương</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                        <p class="text-[10px] text-gray-400">Ngày gia nhập</p>
                                        <p class="font-bold text-gray-800 text-sm mt-1">
                                            {{ $selectedEmployee['hire_date'] }}</p>
                                    </div>
                                    <div class="p-4 bg-[#2d2d2d] rounded-2xl">
                                        <p class="text-[10px] text-gray-500">Lương cơ bản</p>
                                        <p class="font-bold text-[#bc9c75] text-sm mt-1">
                                            {{ $selectedEmployee['salary'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
@endif
</div>
