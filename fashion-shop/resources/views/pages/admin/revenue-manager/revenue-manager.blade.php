@extends('layouts.admin-layout')
@section('title', 'Báo cáo doanh thu')

@section('page-header')
    <h1 id="page-title" class="text-xl font-semibold text-gray-800">
        Báo cáo doanh thu
    </h1>

    <p class="text-xs text-gray-400 mt-1">
        <span class="cursor-pointer hover:text-[#bc9c75] transition">
            Trang chính
        </span>
        /
        <span id="breadcrumb-current" class="text-[#bc9c75] font-medium">Doanh thu</span>
    </p>
@endsection

@section('content')
    <div class="space-y-6">
        @php
            $growthPositive = $summary['growth_percent'] >= 0;
            $maxBarValue = max((float) $monthlyRevenue->max('value'), 1);
            $discountRate =
                $summary['gross_revenue'] > 0 ? ($summary['discount_total'] / $summary['gross_revenue']) * 100 : 0;
            $selectedPeriodLabel = $period === 'week' ? '7 ngày gần nhất' : '30 ngày gần nhất';
            $peakMonth = $monthlyRevenue->sortByDesc('value')->first();
            $latestMonth = $monthlyRevenue->last();
            $averageMonthlyRevenue = (float) $monthlyRevenue->avg('value');
        @endphp

        <section
            class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#F9F5F0] via-[#F5EFE6] to-[#EFE6DA] px-8 py-5 text-[#2d2116] shadow-xl">
            <div class="relative z-10 space-y-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                    <div class="max-w-2xl space-y-4">
                        <div
                            class="inline-flex items-center gap-2 rounded-full border border-[#d8c5b0] bg-white/70 text-[#8b6c4b] shadow-sm px-3 py-1 text-[11px] font-bold uppercase tracking-[0.3em]">
                            <span
                                class="h-2 w-2 rounded-full {{ $growthPositive ? 'bg-emerald-400' : 'bg-rose-400' }}"></span>
                            Phân tích doanh thu
                        </div>

                        <div class="space-y-3">
                            <h2 class="text-xl md:text-3xl font-black text-[#2d2116]">
                                Toàn cảnh doanh thu trong {{ $selectedPeriodLabel }}
                            </h2>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <form method="GET" action="{{ route('admin.revenue') }}"
                                class="inline-flex rounded-xl border border-[#d9c7b5] bg-white p-1 shadow-sm">

                                <button type="submit" name="period" value="week"
                                    class="rounded-lg px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.12em] transition-all {{ $period === 'week' ? 'bg-[#bc9c75] text-white' : 'text-[#7b6248] hover:bg-[#f8f3ee]' }}">
                                    <i class="fa-solid fa-calendar-week mr-1"></i>
                                    7 ngày
                                </button>

                                <button type="submit" name="period" value="month"
                                    class="rounded-lg px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.12em] transition-all {{ $period === 'month' ? 'bg-[#bc9c75] text-white' : 'text-[#7b6248] hover:bg-[#f8f3ee]' }}">
                                    <i class="fa-solid fa-calendar-days mr-1"></i>
                                    30 ngày
                                </button>
                            </form>

                            <a href="{{ route('admin.orders') }}"
                                class="inline-flex items-center gap-2 rounded-2xl border border-white/12 bg-[#d8b28b] px-4 py-2 text-sm font-semibold text-[#2d2116] transition hover:bg-[#e4c4a3]">
                                <i class="fa-solid fa-receipt text-xs"></i>
                                Xem chi tiết đơn hàng
                            </a>
                        </div>
                    </div>

                    <div class="grid w-full max-w-xl grid-cols-1 gap-3 sm:grid-cols-2">
                        <div
                            class="rounded-3xl border border-[#e5d6c7] bg-white/80 p-5 shadow-[0_12px_30px_rgba(188,156,117,0.12)]">
                            <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-[#8b7355]">Doanh thu thuần</p>
                            <h3 class="mt-3 text-3xl font-black text-[#2d2116] tracking-tight">
                                {{ number_format((float) $summary['net_revenue'], 0, ',', '.') }}đ
                            </h3>
                            <p
                                class="mt-3 inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold {{ $growthPositive ? 'text-emerald-500' : 'text-rose-500' }}">
                                <i class="fa-solid {{ $growthPositive ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                                {{ $growthPositive ? '+' : '' }}{{ number_format((float) $summary['growth_percent'], 1) }}%
                                so với kỳ trước
                            </p>
                        </div>

                        <div
                            class="rounded-3xl shadow-[0_12px_30px_rgba(188,156,117,0.12)] p-5 text-[#3b2b1d] border border-[#e5d6c7] bg-white/80">
                            <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-[#7b6248]">Tháng cao nhất trong
                                6 tháng</p>
                            <h3 class="mt-3 text-2xl font-black tracking-tight">
                                {{ $peakMonth['label'] ?? '--' }}
                            </h3>
                            <p class="mt-2 text-sm font-semibold text-[#5b4734]">
                                {{ number_format((float) ($peakMonth['value'] ?? 0), 0, ',', '.') }}đ
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 xl:grid-cols-4">
                    <article
                        class="rounded-3xl bg-white/75 border-[#e5d6c7] backdrop-blur-md ring-1 ring-white/10 p-5 backdrop-blur">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-[#8b7355]">Tổng đơn hợp lệ
                                </p>
                                <h3 class="mt-3 text-3xl text-[#2d2116] font-black">
                                    {{ number_format((int) $summary['order_count']) }}
                                </h3>
                            </div>
                            <span
                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#f5ede4] text-[#8b6c4b] text-xl">
                                <i class="fa-solid fa-bag-shopping"></i>
                            </span>
                        </div>
                        <p class="mt-4 text-sm leading-6 text-[#6b5b4d]">Số đơn tạo doanh thu thực trong kỳ theo dõi hiện
                            tại.</p>
                    </article>

                    <article class="rounded-3xl border bg-white/75 border-[#e5d6c7] p-5 backdrop-blur">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-[#8b7355]">Voucher đã dùng
                                </p>
                                <h3 class="mt-3 text-3xl font-black text-[#ff3d3d]">
                                    -{{ number_format((float) $summary['discount_total'], 0, ',', '.') }}đ
                                </h3>
                            </div>
                            <span
                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#f5ede4] text-[#8b6c4b] text-xl">
                                <i class="fa-solid fa-ticket"></i>
                            </span>
                        </div>
                        <div class="mt-4 h-2 overflow-hidden rounded-full bg-[#f5ede4] text-[#8b6c4b]">
                            <div class="h-full rounded-full bg-gradient-to-r from-rose-300 via-rose-400 to-rose-500"
                                style="width: {{ min(100, round($discountRate)) }}%"></div>
                        </div>
                        <p class="mt-3 text-sm leading-6 text-[#6b5b4d]">Voucher chiếm
                            {{ number_format($discountRate, 1) }}% trên doanh thu gốc.</p>
                    </article>

                    <article class="rounded-3xl border bg-white/75 border-[#e5d6c7] p-5 backdrop-blur">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-[#8b7355]">Giá trị đơn
                                    trung bình</p>
                                <h3 class="mt-3 text-3xl font-black text-[#2d2116]">
                                    {{ number_format((float) $summary['avg_order_value'], 0, ',', '.') }}đ
                                </h3>
                            </div>
                            <span
                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#f5ede4] text-[#8b6c4b] text-xl">
                                <i class="fa-solid fa-layer-group"></i>
                            </span>
                        </div>
                        <p class="mt-4 text-sm leading-6 text-[#6b5b4d]">Giá trị đơn trung bình phản ánh chất lượng doanh
                            thu
                            trong kỳ.</p>
                    </article>

                    <article class="rounded-3xl border bg-white/75 border-[#e5d6c7] p-5 backdrop-blur">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-[#8b7355]">Doanh thu gốc
                                </p>
                                <h3 class="mt-3 text-3xl font-black text-[#2d2116]">
                                    {{ number_format((float) $summary['gross_revenue'], 0, ',', '.') }}đ
                                </h3>
                            </div>
                            <span
                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#f5ede4] text-[#8b6c4b] text-xl">
                                <i class="fa-solid fa-wallet"></i>
                            </span>
                        </div>
                        <p class="mt-4 text-sm leading-6 text-[#6b5b4d]">Doanh thu trước khi trừ voucher và các mức giảm giá
                            áp dụng.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-6">
            <div
                class="w-full rounded-[2rem] border border-[#efe5db] bg-white p-6 shadow-[0_25px_70px_rgba(15,23,42,0.06)] sm:p-7">
                @php
                    $chartWidth = 1200;
                    $chartHeight = 380;
                    $chartPaddingLeft = 56;
                    $chartPaddingRight = 18;
                    $chartPaddingTop = 40;
                    $chartPaddingBottom = 34;
                    $plotWidth = $chartWidth - $chartPaddingLeft - $chartPaddingRight;
                    $plotHeight = $chartHeight - $chartPaddingTop - $chartPaddingBottom;
                    $chartBaseY = $chartPaddingTop + $plotHeight;
                    $chartMaxX = $chartWidth - $chartPaddingRight;
                    $formatCompactMoney = function (float $value): string {
                        if ($value >= 1000000000) {
                            return number_format($value / 1000000000, 1, ',', '.') . 'B';
                        }

                        if ($value >= 1000000) {
                            return number_format($value / 1000000, 1, ',', '.') . 'M';
                        }

                        if ($value >= 1000) {
                            return number_format($value / 1000, 0, ',', '.') . 'K';
                        }

                        return number_format($value, 0, ',', '.');
                    };
                    $chartItems = $monthlyRevenue->values();
                    $chartCount = max($chartItems->count(), 1);
                    $chartPoints = $chartItems->map(function ($item, $index) use (
                        $chartCount,
                        $chartPaddingLeft,
                        $plotWidth,
                        $chartPaddingTop,
                        $plotHeight,
                        $maxBarValue,
                    ) {
                        $x =
                            $chartCount === 1
                                ? $chartPaddingLeft + $plotWidth / 2
                                : $chartPaddingLeft + ($plotWidth / ($chartCount - 1)) * $index;

                        $ratio = $maxBarValue > 0 ? (float) $item['value'] / $maxBarValue : 0;
                        $y = $chartPaddingTop + $plotHeight - $plotHeight * $ratio;

                        return [
                            'label' => $item['label'],
                            'value' => (float) $item['value'],
                            'x' => round($x, 2),
                            'y' => round($y, 2),
                        ];
                    });
                    $linePoints = $chartPoints->map(fn($point) => $point['x'] . ',' . $point['y'])->implode(' ');
                    $firstPoint = $chartPoints->first();
                    $lastPoint = $chartPoints->last();
                    $areaPoints =
                        $firstPoint && $lastPoint
                            ? $linePoints .
                                ' ' .
                                $lastPoint['x'] .
                                ',' .
                                $chartBaseY .
                                ' ' .
                                $firstPoint['x'] .
                                ',' .
                                $chartBaseY
                            : '';
                    $averageRatio = $maxBarValue > 0 ? $averageMonthlyRevenue / $maxBarValue : 0;
                    $averageLineY = round($chartPaddingTop + $plotHeight - $plotHeight * $averageRatio, 2);
                    $chartGrid = collect(range(0, 4))->map(function ($step) use (
                        $chartPaddingTop,
                        $plotHeight,
                        $chartBaseY,
                        $maxBarValue,
                    ) {
                        $value = $maxBarValue - ($maxBarValue / 4) * $step;
                        $y = round($chartPaddingTop + ($plotHeight / 4) * $step, 2);

                        return [
                            'value' => $value,
                            'y' => $y,
                            'is_base' => abs($y - $chartBaseY) < 0.01,
                        ];
                    });
                @endphp

                <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-[0.3em] text-[#bc9c75]">Xu hướng doanh thu</p>
                        <h4 class="mt-2 text-2xl font-black tracking-tight text-gray-900">Biểu đồ doanh thu 6 tháng gần nhất
                        </h4>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <div
                            class="rounded-2xl border border-[#efe5db] bg-[#f8f3ee] px-4 py-3 text-sm text-gray-600 shadow-[0_8px_22px_rgba(188,156,117,0.08)]">
                            <p class="font-semibold text-gray-800">Mốc cao nhất</p>
                            <p class="mt-1 text-[#8b6c4b]">{{ number_format($maxBarValue, 0, ',', '.') }}đ</p>
                        </div>
                        <div
                            class="rounded-2xl border border-[#d8e5cf] bg-[#f8fcf5] px-4 py-3 text-sm text-gray-600 shadow-[0_8px_18px_rgba(95,111,82,0.08)]">
                            <p class="font-semibold text-gray-800">Trung bình 6 tháng</p>
                            <p class="mt-1 text-[#5f6f52]">
                                {{ number_format($averageMonthlyRevenue, 0, ',', '.') }}đ</p>
                        </div>
                    </div>
                </div>

                <div
                    class="mt-8 rounded-[1.75rem] border border-[#f1e7dc] bg-[linear-gradient(180deg,#fffdfb_0%,#faf6f1_100%)] p-4 sm:p-5">
                    <div
                        class="flex flex-col gap-3 border-b border-[#efe5db] pb-4 text-xs font-semibold text-gray-500 sm:flex-row sm:flex-wrap sm:items-center sm:gap-4">
                        <span class="inline-flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-[#bc9c75]"></span>
                            Doanh thu theo tháng
                        </span>
                        <span class="inline-flex items-center gap-2">
                            <span class="h-px w-6 border-t-2 border-dashed border-[#6e845b]"></span>
                            Trung bình 6 tháng
                        </span>
                        <span class="inline-flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-[#5f6f52]"></span>
                            Tháng mới nhất
                        </span>
                    </div>

                    <div class="mt-5">
                        <div>
                            <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" preserveAspectRatio="xMidYMid meet"
                                class="w-full h-[380px]">
                                <defs>
                                    <linearGradient id="revenue-area-fill" x1="0" x2="0" y1="0"
                                        y2="1">
                                        <stop offset="0%" stop-color="#bc9c75" stop-opacity="0.45" />
                                        <stop offset="50%" stop-color="#bc9c75" stop-opacity="0.18" />
                                        <stop offset="100%" stop-color="#bc9c75" stop-opacity="0.02" />
                                    </linearGradient>
                                    <filter id="revenue-line-shadow" x="-20%" y="-20%" width="140%" height="140%">
                                        <feDropShadow dx="0" dy="12" stdDeviation="16"
                                            flood-color="#bc9c75" flood-opacity="0.35" />
                                    </filter>
                                </defs>

                                @foreach ($chartGrid as $gridLine)
                                    <line x1="{{ $chartPaddingLeft }}" y1="{{ $gridLine['y'] }}"
                                        x2="{{ $chartWidth - $chartPaddingRight }}" y2="{{ $gridLine['y'] }}"
                                        stroke="{{ $gridLine['is_base'] ? '#d9c8b5' : '#eee5db' }}" stroke-width="1"
                                        stroke-dasharray="{{ $gridLine['is_base'] ? '0' : '6 8' }}" />
                                    <text x="{{ $chartPaddingLeft - 10 }}" y="{{ $gridLine['y'] + 4 }}" text-anchor="end"
                                        font-size="10" fill="#9a8b7a" font-weight="700">
                                        {{ $formatCompactMoney((float) $gridLine['value']) }}đ
                                    </text>
                                @endforeach

                                <line x1="{{ $chartPaddingLeft }}" y1="{{ $averageLineY }}"
                                    x2="{{ $chartWidth - $chartPaddingRight }}" y2="{{ $averageLineY }}"
                                    stroke="#6e845b" stroke-width="2" stroke-dasharray="7 8" opacity="0.85" />
                                <text x="{{ $chartWidth - $chartPaddingRight }}" y="{{ $averageLineY - 8 }}"
                                    text-anchor="end" font-size="12" fill="#5f6f52" font-weight="900">
                                    Trung bình
                                </text>

                                @if ($areaPoints !== '')
                                    <polygon points="{{ $areaPoints }}" fill="url(#revenue-area-fill)" />
                                    <polyline points="{{ $linePoints }}" fill="none" stroke="#bc9c75"
                                        stroke-width="5" stroke-linecap="round" stroke-linejoin="round"
                                        filter="url(#revenue-line-shadow)" />
                                @endif

                                @foreach ($chartPoints as $point)
                                    @php
                                        $isPeakMonth = ($peakMonth['label'] ?? null) === $point['label'];
                                        $isLatestMonth = ($latestMonth['label'] ?? null) === $point['label'];
                                        $pointFill = $isPeakMonth
                                            ? '#8d6238'
                                            : ($isLatestMonth
                                                ? '#5f6f52'
                                                : '#bc9c75');
                                        $pointStroke = $isPeakMonth ? '#f0ddc4' : '#ffffff';
                                        $valueLabelAnchor = 'middle';
                                        $valueLabelX = $point['x'];

                                        if ($point['x'] < $chartPaddingLeft + 36) {
                                            $valueLabelAnchor = 'start';
                                            $valueLabelX = $point['x'] + 10;
                                        } elseif ($point['x'] > $chartMaxX - 36) {
                                            $valueLabelAnchor = 'end';
                                            $valueLabelX = $point['x'] - 10;
                                        }
                                    @endphp
                                    <line x1="{{ $point['x'] }}" y1="{{ $point['y'] }}" x2="{{ $point['x'] }}"
                                        y2="{{ $chartBaseY }}" stroke="#eadfd2" stroke-width="1"
                                        stroke-dasharray="4 8" />
                                    <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="10"
                                        fill="{{ $pointFill }}" stroke="{{ $pointStroke }}" stroke-width="3" />
                                    <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="18"
                                        fill="{{ $pointFill }}" opacity="0.12" />
                                    @if ($point['value'] > 0)
                                        <text x="{{ $valueLabelX }}" y="{{ max(20, $point['y'] - 18) }}"
                                            text-anchor="{{ $valueLabelAnchor }}" font-size="10" fill="#6f5a44"
                                            font-weight="800">
                                            {{ number_format($point['value'], 0, ',', '.') }}đ
                                        </text>
                                    @endif
                                    <text x="{{ $point['x'] }}" y="{{ $chartHeight - 10 }}" text-anchor="middle"
                                        font-size="12" fill="#7d6a58" font-weight="800">
                                        {{ $point['label'] }}
                                    </text>
                                @endforeach
                            </svg>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
                        @foreach ($chartPoints as $point)
                            @php
                                $isPeakMonth = ($peakMonth['label'] ?? null) === $point['label'];
                                $isLatestMonth = ($latestMonth['label'] ?? null) === $point['label'];
                            @endphp
                            <div
                                class="rounded-2xl border {{ $isPeakMonth ? 'border-[#d8bea0] bg-[#fff8f1]' : 'border-gray-100 bg-white/80' }} p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-black uppercase tracking-[0.2em] text-gray-400">
                                            {{ $point['label'] }}</p>
                                        <p class="mt-2 text-lg font-black text-gray-900">
                                            {{ number_format($point['value'], 0, ',', '.') }}đ</p>
                                    </div>
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.18em] {{ $isPeakMonth ? 'bg-[#bc9c75] text-white' : ($isLatestMonth ? 'bg-[#e3ecdb] text-[#5f6f52]' : 'bg-gray-100 text-gray-500') }}">
                                        {{ $isPeakMonth ? 'Cao nhất' : ($isLatestMonth ? 'Mới nhất' : 'Tháng') }}
                                    </span>
                                </div>
                                <div class="mt-3 h-2 overflow-hidden rounded-full bg-[#f4ede6]">
                                    <div class="h-full rounded-full {{ $isLatestMonth ? 'bg-[#5f6f52]' : 'bg-[#bc9c75]' }}"
                                        style="width: {{ min(100, round(($point['value'] / $maxBarValue) * 100)) }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
