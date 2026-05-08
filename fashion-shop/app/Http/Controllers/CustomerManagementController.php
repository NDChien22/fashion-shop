<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CustomerManagementController extends Controller
{
    public function index(Request $request)
    {
        $ordersAggregate = Order::query()
            ->selectRaw('user_id, COUNT(*) as orders_count, COALESCE(SUM(final_amount), 0) as total_spent')
            ->where('user_id', '>', 0)
            ->where('status', OrderStatus::COMPLETED->value)
            ->groupBy('user_id');

        $query = User::query()
            ->where('role', 'customer')
            ->leftJoinSub($ordersAggregate, 'orders_agg', function ($join): void {
                $join->on('orders_agg.user_id', '=', 'users.id');
            })
            ->select([
                'users.*',
                DB::raw('COALESCE(orders_agg.orders_count, 0) as orders_count'),
                DB::raw('COALESCE(orders_agg.total_spent, 0) as total_spent'),
            ]);

        $keyword = trim((string) $request->query('q', ''));
        if ($keyword !== '') {
            $query->where(function ($builder) use ($keyword): void {
                $builder->where('users.username', 'like', '%'.$keyword.'%')
                    ->orWhere('users.full_name', 'like', '%'.$keyword.'%')
                    ->orWhere('users.email', 'like', '%'.$keyword.'%')
                    ->orWhere('users.phone_number', 'like', '%'.$keyword.'%');
            });
        }

        $hasMembershipTables = Schema::hasTable('customer_membership_levels') && Schema::hasTable('membership_levels');

        $membershipOptions = collect();
        if ($hasMembershipTables) {
            $membershipOptions = DB::table('membership_levels')
                ->orderBy('min_points')
                ->pluck('name');

            $membershipFilter = trim((string) $request->query('membership', ''));
            if ($membershipFilter !== '') {
                $query->whereExists(function ($subQuery) use ($membershipFilter): void {
                    $subQuery
                        ->select(DB::raw(1))
                        ->from('customer_membership_levels as cml')
                        ->join('membership_levels as ml', 'ml.id', '=', 'cml.membership_level_id')
                        ->whereColumn('cml.user_id', 'users.id')
                        ->where('ml.name', $membershipFilter);
                });
            }
        }

        $customers = $query
            ->orderByDesc('users.id')
            ->paginate(12)
            ->withQueryString();

        $membershipsByUserId = collect();
        if ($hasMembershipTables && $customers->isNotEmpty()) {
            $membershipsByUserId = DB::table('customer_membership_levels as cml')
                ->leftJoin('membership_levels as ml', 'ml.id', '=', 'cml.membership_level_id')
                ->whereIn('cml.user_id', $customers->pluck('id'))
                ->select('cml.user_id', 'cml.customer_code', 'cml.points', 'ml.name as membership_name')
                ->get()
                ->keyBy('user_id');
        }

        $customers->getCollection()->transform(function ($customer) use ($membershipsByUserId) {
            $membership = $membershipsByUserId->get($customer->id);
            $customer->customer_code = $membership->customer_code ?? null;
            $customer->membership_name = $membership->membership_name ?? null;
            $customer->membership_points = (int) ($membership->points ?? 0);

            return $customer;
        });

        $summary = [
            'total_customers' => (int) User::query()->where('role', 'customer')->count(),
            'total_orders' => (int) Order::query()->where('user_id', '>', 0)->count(),
            'total_spending' => (float) (Order::query()
                ->where('user_id', '>', 0)
                ->where('status', OrderStatus::COMPLETED->value)
                ->sum('final_amount') ?? 0),
        ];

        return view('pages.admin.customer-manager.customer-manager', [
            'customers' => $customers,
            'summary' => $summary,
            'membershipOptions' => $membershipOptions,
            'hasMembershipTables' => $hasMembershipTables,
        ]);
    }
}
