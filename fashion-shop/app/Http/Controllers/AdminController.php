<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturnRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AdminController extends Controller
{
    private array $invalidOrderStatuses;

    // order status
    public function __construct()
    {
        $this->invalidOrderStatuses = [
            OrderStatus::CANCELLED->value,
            OrderStatus::PAYMENT_FAILED->value,
            OrderStatus::RETURNED->value,
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

    public function ReturnRequestManagerView(Request $request)
    {
        return view('pages.admin.return-request-manager.return-request-manager');
    }

    // dashboard
    public function DashboardView()
    {
        $today = Carbon::today();
        $todayStart = $today->copy()->startOfDay();
        $todayEnd = $today->copy()->endOfDay();

        $isInTodayRange = function (?Carbon $date) use ($todayStart, $todayEnd): bool {
            return $date?->betweenIncluded($todayStart, $todayEnd) ?? false;
        };

        $validOrders = Order::query()
            ->whereNotIn('status', $this->invalidOrderStatuses)
            ->get();

        $todayOrders = $validOrders->filter(function (Order $order) use ($isInTodayRange): bool {
            return $isInTodayRange($order->created_at);
        });

        $todayOrderIds = $todayOrders->pluck('id')->all();

        $todayOrderItems = OrderItem::query()
            ->with('order:id,created_at,status')
            ->get()
            ->filter(function (OrderItem $item) use ($todayOrderIds, $isInTodayRange): bool {
                if (! $item->order) {
                    return false;
                }

                return in_array((int) $item->order_id, $todayOrderIds, true)
                    && $isInTodayRange($item->order->created_at)
                    && ! in_array((string) $item->order->status, $this->invalidOrderStatuses, true);
            });

        $todayUsers = User::query()
            ->get()
            ->filter(function (User $user) use ($isInTodayRange): bool {
                return $isInTodayRange($user->created_at) && (is_null($user->role) || $user->role !== 'admin');
            });

        $todayReturnRequests = OrderReturnRequest::query()
            ->get()
            ->filter(function (OrderReturnRequest $returnRequest) use ($isInTodayRange): bool {
                return $isInTodayRange($returnRequest->created_at);
            });

        $stats = [
            'new_orders_today' => (int) $todayOrders->count(),
            'revenue_today' => (float) $todayOrders->sum('final_amount'),
            'products_sold_today' => (int) $todayOrderItems->sum('quantity'),
            'new_customers_today' => (int) $todayUsers->count(),
            'pending_orders' => Order::query()->where('status', OrderStatus::PENDING->value)->count(),
            'return_requests_total' => OrderReturnRequest::query()->count(),
            'return_requests_today' => (int) $todayReturnRequests->count(),
            'return_requests_pending' => OrderReturnRequest::query()->where('status', 'pending')->count(),
            'return_requests_approved' => OrderReturnRequest::query()->where('status', 'approved')->count(),
            'return_requests_rejected' => OrderReturnRequest::query()->where('status', 'rejected')->count(),
            'return_requests_completed' => OrderReturnRequest::query()->where('status', 'completed')->count(),
            'returned_orders' => Order::query()->where('status', OrderStatus::RETURNED->value)->count(),
            'exchanged_orders' => Order::query()->where('status', OrderStatus::EXCHANGED->value)->count(),
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

    // revenue
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

    // doanh thu theo tháng
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
