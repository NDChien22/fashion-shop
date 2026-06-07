<div class="space-y-6">
    @if (session('success'))
        <div
            class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-600">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-[0.18em] text-gray-400">Tổng feedback</p>
            <h3 class="mt-2 text-2xl font-black text-gray-800">{{ number_format($summary['total']) }}</h3>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-[0.18em] text-gray-400">Điểm trung bình</p>
            <h3 class="mt-2 text-2xl font-black text-[#bc9c75]">
                {{ number_format((float) $summary['average_rating'], 1) }}/5</h3>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-[0.18em] text-gray-400">5 sao</p>
            <h3 class="mt-2 text-2xl font-black text-emerald-600">{{ number_format($summary['five_star']) }}</h3>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-[0.18em] text-gray-400">7 ngày gần đây</p>
            <h3 class="mt-2 text-2xl font-black text-sky-600">{{ number_format($summary['recent']) }}</h3>
        </div>
    </div>

    <div class="rounded-3xl border border-gray-100 bg-white p-4 shadow-sm lg:p-6">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-sm font-black uppercase tracking-[0.12em] text-gray-600">Bộ lọc feedback</h3>
                <p class="mt-1 text-xs text-gray-400">Tìm theo mã đơn, khách hàng, email hoặc nội dung feedback.</p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <input type="text" wire:model.live.debounce.400ms="search" placeholder="Tìm kiếm feedback..."
                    class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-[#bc9c75] focus:outline-none focus:ring-2 focus:ring-[#bc9c75]/10 sm:w-80">

                <select wire:model.live="ratingFilter"
                    class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-[#bc9c75] focus:outline-none focus:ring-2 focus:ring-[#bc9c75]/10 sm:w-44">
                    <option value="">Tất cả số sao</option>
                    <option value="5">5 sao</option>
                    <option value="4">4 sao</option>
                    <option value="3">3 sao</option>
                    <option value="2">2 sao</option>
                    <option value="1">1 sao</option>
                </select>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3 lg:px-6">
            <h3 class="text-sm font-black uppercase tracking-[0.12em] text-gray-600">Danh sách feedback</h3>
            <p class="text-xs text-gray-400">Hiển thị {{ $feedbacks->count() }} /
                {{ number_format($feedbacks->total()) }} feedback</p>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse ($feedbacks as $feedback)
                @php
                    $order = $feedback->order;
                    $product = $feedback->product;
                    $user = $feedback->user;
                @endphp

                <div wire:key="feedback-{{ $feedback->id }}" class="p-4 lg:p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-base font-black text-gray-800">{{ $order?->order_code ?: 'N/A' }}</p>
                                <span
                                    class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                    {{ $feedback->rating }}/5
                                </span>
                                @if ($product)
                                    <span
                                        class="inline-flex rounded-full bg-sky-100 px-2.5 py-1 text-xs font-semibold text-sky-700">
                                        {{ $product->name }}
                                    </span>
                                @endif
                            </div>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ $order?->user?->full_name ?? ($order?->guest_name ?? ($user?->full_name ?? ($user?->username ?? 'Khách vãng lai'))) }}
                                · {{ $order?->user?->email ?? ($order?->guest_email ?? ($user?->email ?? 'N/A')) }}
                            </p>

                            <p class="mt-3 rounded-2xl bg-[#fcfaf8] px-4 py-3 text-sm leading-6 text-gray-700">
                                {{ $feedback->content }}
                            </p>

                            @if ($feedback->admin_reply)
                                <div class="mt-3 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3">
                                    <p class="text-[11px] font-black uppercase tracking-[0.18em] text-emerald-700">
                                        Phản hồi của quản trị
                                    </p>
                                    <p class="mt-2 text-sm leading-6 text-emerald-900">
                                        {{ $feedback->admin_reply }}
                                    </p>
                                    <p class="mt-2 text-xs font-semibold text-emerald-700">
                                        @if ($feedback->adminReplyUser)
                                            {{ $feedback->adminReplyUser->full_name ?: $feedback->adminReplyUser->username ?: $feedback->adminReplyUser->email }}
                                            ·
                                        @endif
                                        {{ $feedback->admin_replied_at?->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            @endif

                            @if ($replyFeedbackId === $feedback->id)
                                <div class="mt-3 rounded-2xl border border-gray-200 bg-gray-50 p-4">
                                    <textarea wire:model.live="replyContent" rows="4"
                                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-[#bc9c75] focus:outline-none focus:ring-2 focus:ring-[#bc9c75]/10"
                                        placeholder="Nhập phản hồi của quản trị..."></textarea>
                                    @error('replyContent')
                                        <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                                    @enderror

                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <button type="button" wire:click="saveReply"
                                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700 transition">
                                            Lưu phản hồi
                                        </button>
                                        <button type="button" wire:click="cancelReply"
                                            class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                                            Hủy
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <div class="mt-3 flex flex-wrap gap-2 text-[11px] font-semibold text-gray-500">
                                <span class="rounded-full bg-slate-100 px-3 py-1">Ngày:
                                    {{ $feedback->created_at?->format('d/m/Y H:i') }}</span>
                                <span class="rounded-full bg-slate-100 px-3 py-1">Đơn:
                                    {{ number_format((float) ($order?->final_amount ?? 0), 0, ',', '.') }}đ</span>
                                @if ($order?->user?->phone_number || $order?->guest_phone)
                                    <span class="rounded-full bg-slate-100 px-3 py-1">SĐT:
                                        {{ $order->user?->phone_number ?? $order->guest_phone }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 lg:justify-end">
                            <button type="button" wire:click="editReply({{ $feedback->id }})"
                                class="inline-flex items-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-semibold text-sky-700 hover:bg-sky-100 transition">
                                <i class="fa-regular fa-pen-to-square"></i>
                                Trả lời
                            </button>
                            <button type="button" wire:click="deleteFeedback({{ $feedback->id }})"
                                onclick="return confirm('Bạn có chắc muốn xóa feedback này?')"
                                class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-100 transition">
                                <i class="fa-regular fa-trash-can"></i>
                                Xóa
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-14 text-center text-gray-500 lg:px-6">
                    Chưa có feedback nào phù hợp bộ lọc hiện tại.
                </div>
            @endforelse
        </div>

        <div class="border-t border-gray-100 px-4 py-3 lg:px-6">
            {{ $feedbacks->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
