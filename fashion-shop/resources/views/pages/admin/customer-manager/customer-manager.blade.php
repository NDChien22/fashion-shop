@extends('layouts.admin-layout')
@section('title', 'Quản lý khách hàng')

@section('page-header')
    <h1 id="page-title" class="text-xl font-semibold text-gray-800">
        Quản lý khách hàng
    </h1>

    <p class="text-xs text-gray-400 mt-1">
        <span class="cursor-pointer hover:text-[#bc9c75] transition">
            Trang chính
        </span>
        /
        <span id="breadcrumb-current" class="text-[#bc9c75] font-medium">Khách hàng</span>
    </p>
@endsection

@section('content')
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl shadow-sm p-4">
                <p class="text-xs text-gray-400 uppercase">Tổng khách hàng</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($summary['total_customers']) }}</h3>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4">
                <p class="text-xs text-gray-400 uppercase">Tổng đơn hàng</p>
                <h3 class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($summary['total_orders']) }}</h3>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4">
                <p class="text-xs text-gray-400 uppercase">Tổng chi tiêu</p>
                <h3 class="text-2xl font-bold text-[#bc9c75] mt-1">
                    {{ number_format((float) $summary['total_spending'], 0, ',', '.') }}đ
                </h3>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.customers') }}" class="bg-white rounded-xl shadow-sm p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="relative">
                    <i
                        class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="q" value="{{ request('q') }}"
                        placeholder="Tìm theo tên, email, SĐT, username"
                        class="w-full rounded-lg border border-gray-200 py-2.5 pl-10 pr-4 text-sm focus:outline-none focus:border-[#bc9c75]">
                </div>

                <select name="membership"
                    class="w-full rounded-lg border border-gray-200 py-2.5 px-3 text-sm focus:outline-none focus:border-[#bc9c75]"
                    @disabled(!$hasMembershipTables)>
                    <option value="">Tất cả hạng thành viên</option>
                    @foreach ($membershipOptions as $membershipName)
                        <option value="{{ $membershipName }}" @selected(request('membership') === $membershipName)>
                            {{ $membershipName }}
                        </option>
                    @endforeach
                </select>

                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 rounded-lg bg-[#bc9c75] text-white text-sm font-semibold px-3 py-2.5 hover:opacity-90 transition">
                        Lọc
                    </button>
                    <a href="{{ route('admin.customers') }}"
                        class="rounded-lg border border-gray-200 text-sm font-semibold px-3 py-2.5 hover:bg-gray-50 transition">
                        Reset
                    </a>
                </div>
            </div>

            @if (!$hasMembershipTables)
                <p class="mt-2 text-xs text-amber-600">Chưa có bảng membership trong DB, bộ lọc hạng sẽ tạm thời không hoạt
                    động.</p>
            @endif
        </form>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead
                        class="bg-[#fcfaf8] border-b border-gray-50 text-gray-400 font-bold uppercase tracking-wider text-[10px]">
                        <tr class="border-b border-gray-50">
                            <th class="text-left py-4 px-6 font-black">Khách hàng</th>
                            <th class="text-left py-4 px-4 font-black">Hạng</th>
                            <th class="text-left py-4 px-4 font-black">Liên hệ</th>
                            <th class="text-center py-4 px-4 font-black">Đơn hàng</th>
                            <th class="text-right py-4 px-4 font-black">Tổng chi tiêu</th>
                            <th class="text-center py-4 px-4 font-black">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-[12px]">
                        @forelse ($customers as $customer)
                            <tr class="hover:bg-[#fffbf7]/50 transition-all group">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center border border-gray-100 overflow-hidden">
                                            @if (!empty($customer->avatar))
                                                <img src="{{ str_starts_with($customer->avatar, 'http') ? $customer->avatar : asset('storage/' . ltrim($customer->avatar, '/')) }}"
                                                    alt="avatar" class="w-full h-full object-cover">
                                            @else
                                                <i class="fa-solid fa-user text-[#bc9c75] text-sm"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="text-[13px] font-black text-gray-800 uppercase tracking-tight">
                                                {{ $customer->full_name ?: $customer->username }}
                                            </h4>
                                            <p class="text-[10px] text-gray-400 font-medium italic">
                                                Mã KH: {{ $customer->customer_code ?: 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <span
                                        class="bg-[#fffbf2] text-[#bc9c75] border border-[#bc9c75]/20 px-2.5 py-1 rounded-lg text-[10px] font-bold">
                                        {{ $customer->membership_name ?: 'Thành viên mới' }}
                                    </span>
                                    <p class="text-[10px] text-gray-400 mt-1">
                                        {{ number_format((int) $customer->membership_points) }} điểm</p>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="text-[11px] text-gray-700 font-semibold">
                                        {{ $customer->phone_number ?: 'N/A' }}</div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">{{ $customer->email }}</div>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <div class="text-[12px] font-black text-gray-700">
                                        {{ number_format((int) $customer->orders_count) }}
                                        <span class="text-[9px] font-medium text-gray-400 ml-0.5">đơn</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <div class="text-[13px] font-black text-[#bc9c75]">
                                        {{ number_format((float) $customer->total_spent, 0, ',', '.') }}đ
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    @php
                                        $customerModalData = [
                                            'customer_code' => $customer->customer_code ?: 'N/A',
                                            'full_name' => $customer->full_name ?: $customer->username,
                                            'username' => $customer->username,
                                            'email' => $customer->email,
                                            'phone_number' => $customer->phone_number ?: 'N/A',
                                            'address' => $customer->address ?: 'N/A',
                                            'gender' => $customer->gender ?: 'N/A',
                                            'birthday' => $customer->birthday
                                                ? \Illuminate\Support\Carbon::parse($customer->birthday)->format(
                                                    'd/m/Y',
                                                )
                                                : 'N/A',
                                            'membership_name' => $customer->membership_name ?: 'Thành viên mới',
                                            'membership_points' => number_format((int) $customer->membership_points),
                                            'orders_count' => number_format((int) $customer->orders_count),
                                            'total_spent' =>
                                                number_format((float) $customer->total_spent, 0, ',', '.') . 'đ',
                                            'created_at' => $customer->created_at
                                                ? \Illuminate\Support\Carbon::parse($customer->created_at)->format(
                                                    'd/m/Y H:i',
                                                )
                                                : 'N/A',
                                        ];
                                    @endphp

                                    <button type="button"
                                        class="rounded-lg border border-[#d8c2a1] text-[#9c7747] text-xs font-semibold px-3 py-1.5 hover:bg-[#fdf8f2] transition"
                                        data-customer='@json($customerModalData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)'
                                        onclick="openCustomerModalFromButton(this)">
                                        Xem
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-gray-500">Không tìm thấy khách hàng phù
                                    hợp.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-gray-100">
                {{ $customers->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openCustomerModalFromButton(button) {
            const raw = button.getAttribute('data-customer');
            if (!raw) return;

            let data;
            try {
                data = JSON.parse(raw);
            } catch (error) {
                return;
            }

            const modal = document.getElementById('customer-detail-modal');
            if (!modal) return;

            modal.querySelector('[data-customer-code]').textContent = data.customer_code || '-';
            modal.querySelector('[data-customer-name]').textContent = data.full_name || '-';
            modal.querySelector('[data-customer-username]').textContent = data.username || '-';
            modal.querySelector('[data-customer-email]').textContent = data.email || '-';
            modal.querySelector('[data-customer-phone]').textContent = data.phone_number || '-';
            modal.querySelector('[data-customer-address]').textContent = data.address || '-';
            modal.querySelector('[data-customer-gender]').textContent = data.gender || '-';
            modal.querySelector('[data-customer-birthday]').textContent = data.birthday || '-';
            modal.querySelector('[data-customer-membership]').textContent = data.membership_name || '-';
            modal.querySelector('[data-customer-points]').textContent = data.membership_points || '0';
            modal.querySelector('[data-customer-orders]').textContent = data.orders_count || '0';
            modal.querySelector('[data-customer-spent]').textContent = data.total_spent || '0đ';
            modal.querySelector('[data-customer-created]').textContent = data.created_at || '-';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeCustomerModal() {
            const modal = document.getElementById('customer-detail-modal');
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }

        window.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeCustomerModal();
            }
        });
    </script>

    <div id="customer-detail-modal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div class="bg-white w-full max-w-4xl rounded-4xl shadow-2xl overflow-hidden relative">
            <button type="button" onclick="closeCustomerModal()"
                class="absolute top-5 right-5 w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center z-20 transition-colors">
                <i class="fa-solid fa-xmark text-gray-500"></i>
            </button>

            <div class="grid grid-cols-1 md:grid-cols-2">
                <div
                    class="p-8 bg-linear-to-br from-[#bc9c75] to-[#8d7558] text-white flex flex-col justify-between min-h-115">
                    <div class="flex justify-between items-start gap-3">
                        <div>
                            <p class="text-[10px] uppercase tracking-[0.16em] text-white/70">Mã khách hàng</p>
                            <p class="text-sm font-bold tracking-wider mt-1" data-customer-code>-</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] uppercase tracking-[0.16em] text-white/70">Hạng</p>
                            <p class="text-[11px] font-black uppercase tracking-[0.15em] mt-1" data-customer-membership>-
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col items-center text-center py-8">
                        <p class="text-[10px] uppercase tracking-[0.16em] text-white/70 mb-3">Khách hàng</p>
                        <h3 class="text-3xl font-black uppercase leading-tight tracking-tight" data-customer-name>-</h3>
                        <div
                            class="mt-8 w-28 h-28 rounded-3xl border-4 border-white/90 bg-white/20 flex items-center justify-center">
                            <i class="fa-solid fa-user text-4xl text-white"></i>
                        </div>
                        <p class="mt-4 text-[11px] font-semibold text-white/90">@<span data-customer-username>-</span></p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] uppercase tracking-[0.16em] text-white/70">Tổng chi tiêu</p>
                            <p class="text-lg font-bold mt-1" data-customer-spent>0đ</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-[0.16em] text-white/70">Điểm tích lũy</p>
                            <p class="text-lg font-bold mt-1"><span data-customer-points>0</span> pts</p>
                        </div>
                    </div>
                </div>

                <div class="p-8 max-h-[85vh] overflow-y-auto bg-white">
                    <div class="mb-5 border-b border-gray-100 pb-4">
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.16em]">Hồ sơ cá nhân</h4>
                    </div>

                    <div class="grid grid-cols-2 gap-0 mb-6 bg-gray-50 rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="p-4 border-r border-b border-gray-100">
                            <p class="text-[10px] uppercase tracking-[0.12em] text-gray-400">Giới tính</p>
                            <p class="text-sm font-bold text-gray-700 mt-1" data-customer-gender>-</p>
                        </div>
                        <div class="p-4 border-b border-gray-100">
                            <p class="text-[10px] uppercase tracking-[0.12em] text-gray-400">Ngày sinh</p>
                            <p class="text-sm font-bold text-gray-700 mt-1" data-customer-birthday>-</p>
                        </div>
                        <div class="p-4 border-r border-b border-gray-100">
                            <p class="text-[10px] uppercase tracking-[0.12em] text-gray-400">Email</p>
                            <p class="text-sm font-semibold text-gray-700 mt-1 break-all" data-customer-email>-</p>
                        </div>
                        <div class="p-4 border-b border-gray-100">
                            <p class="text-[10px] uppercase tracking-[0.12em] text-gray-400">Số điện thoại</p>
                            <p class="text-sm font-semibold text-gray-700 mt-1" data-customer-phone>-</p>
                        </div>
                        <div class="p-4 col-span-2">
                            <p class="text-[10px] uppercase tracking-[0.12em] text-gray-400">Địa chỉ</p>
                            <p class="text-sm font-semibold text-gray-700 mt-1 leading-relaxed" data-customer-address>-</p>
                        </div>
                    </div>

                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.16em] mb-3">Thống kê mua sắm</h4>
                    <div class="space-y-3">
                        <div
                            class="flex items-center justify-between p-3 rounded-xl border border-gray-100 bg-white shadow-sm">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-[#bc9c75]/10 flex items-center justify-center text-[#bc9c75]">
                                    <i class="fa-solid fa-bag-shopping text-[10px]"></i>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold text-gray-800">Tổng số đơn hàng</p>
                                    <p class="text-[9px] text-gray-400">Tạo tài khoản: <span data-customer-created>-</span>
                                    </p>
                                </div>
                            </div>
                            <span class="text-sm font-black text-gray-700"><span data-customer-orders>0</span> đơn</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endpush
