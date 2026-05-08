<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AdminController extends Controller
{
    private array $invalidOrderStatuses;

    public function __construct()
    {
        $this->invalidOrderStatuses = [
            OrderStatus::CANCELLED->value,
            OrderStatus::PAYMENT_FAILED->value,
        ];
    }

    public function ProfileView()
    {
        return view('pages.admin.account-manager.admin-profile');
    }

    public function AccountManagerView()
    {
        return view('pages.admin.account-manager.account-manager');
    }

    public function SupportManagerView()
    {
        return view('pages.admin.customer-services.customer-service');
    }

    public function FeedbackManagerView()
    {
        return view('pages.admin.feedback-manager.feedback-manager');
    }

    public function DashboardView()
    {
        $today = Carbon::today();

        $validOrders = Order::query()->whereNotIn('status', $this->invalidOrderStatuses);

        $stats = [
            'new_orders_today' => (clone $validOrders)
                ->whereDate('created_at', $today)
                ->count(),
            'revenue_today' => (float) (clone $validOrders)
                ->whereDate('created_at', $today)
                ->sum('final_amount'),
            'products_sold_today' => (int) OrderItem::query()
                ->whereHas('order', function ($query) use ($today) {
                    $query->whereDate('created_at', $today)
                        ->whereNotIn('status', $this->invalidOrderStatuses);
                })
                ->sum('quantity'),
            'new_customers_today' => User::query()
                ->whereDate('created_at', $today)
                ->where(function ($query) {
                    $query->whereNull('role')->orWhere('role', '!=', 'admin');
                })
                ->count(),
            'pending_orders' => Order::query()->where('status', OrderStatus::PENDING->value)->count(),
        ];

        $recentOrders = Order::query()
            ->with(['items:id,order_id,quantity'])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        return view('pages.admin.dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }

    public function RevenueView()
    {
        $period = request('period', 'month');
        $days = $period === 'week' ? 7 : 30;

        $endDate = Carbon::today()->endOfDay();
        $startDate = Carbon::today()->subDays($days - 1)->startOfDay();

        $currentPeriodOrders = Order::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotIn('status', $this->invalidOrderStatuses);

        $previousStartDate = (clone $startDate)->subDays($days);
        $previousEndDate = (clone $startDate)->subSecond();
        $previousRevenue = (float) Order::query()
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->whereNotIn('status', $this->invalidOrderStatuses)
            ->sum('final_amount');

        $netRevenue = (float) (clone $currentPeriodOrders)->sum('final_amount');
        $grossRevenue = (float) (clone $currentPeriodOrders)->sum('total_amount');
        $discountTotal = (float) (clone $currentPeriodOrders)->sum('discount_amount');
        $orderCount = (int) (clone $currentPeriodOrders)->count();
        $avgOrderValue = $orderCount > 0 ? $netRevenue / $orderCount : 0;

        $growthPercent = $previousRevenue > 0
            ? (($netRevenue - $previousRevenue) / $previousRevenue) * 100
            : ($netRevenue > 0 ? 100 : 0);

        $monthlyRevenue = $this->buildMonthlyRevenueSeries();

        return view('pages.admin.revenue-manager.revenue-manager', [
            'period' => $period,
            'summary' => [
                'net_revenue' => $netRevenue,
                'gross_revenue' => $grossRevenue,
                'discount_total' => $discountTotal,
                'order_count' => $orderCount,
                'avg_order_value' => $avgOrderValue,
                'growth_percent' => $growthPercent,
            ],
            'monthlyRevenue' => $monthlyRevenue,
        ]);
    }

    public function ProductManagerView()
    {
        return view('pages.admin.product-manager.product-manager');
    }

    private function buildMonthlyRevenueSeries(): Collection
    {
        $series = collect();

        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->startOfMonth()->subMonths($i);
            $monthEnd = (clone $monthStart)->endOfMonth();

            $value = (float) Order::query()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->whereNotIn('status', $this->invalidOrderStatuses)
                ->sum('final_amount');

            $series->push([
                'label' => 'Tháng '.$monthStart->format('n'),
                'value' => $value,
            ]);
        }

        $maxValue = max((float) $series->max('value'), 1);

        return $series->map(function (array $point) use ($maxValue) {
            $heightPercent = (int) max(10, round(($point['value'] / $maxValue) * 100));
            $point['height_percent'] = $heightPercent;

            return $point;
        });
    }
}
