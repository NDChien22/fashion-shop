<?php

namespace App\Livewire\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShippingStatus;
use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class OrderManager extends Component
{
    use WithPagination;

    public string $q = '';

    public string $status = '';

    public string $shipping_status = '';

    public string $payment_status = '';

    protected $queryString = [
        'q' => ['except' => ''],
        'status' => ['except' => ''],
        'shipping_status' => ['except' => ''],
        'payment_status' => ['except' => ''],
    ];

    public function updatingQ(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingShippingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingPaymentStatus(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['q', 'status', 'shipping_status', 'payment_status']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Order::query()
            ->with([
                'payment:id,order_id,status,payment_method,transaction_id,amount',
                'items:id,order_id,product_sku_id,quantity,price',
                'items.productSku:id,product_id,sku,size,color',
                'items.productSku.product:id,name',
                'feedback:id,order_id,user_id,rating,content,created_at',
            ]);

        $keyword = trim($this->q);
        if ($keyword !== '') {
            $query->where(function ($builder) use ($keyword): void {
                $builder->where('order_code', 'like', '%'.$keyword.'%')
                    ->orWhere('customer_name', 'like', '%'.$keyword.'%')
                    ->orWhere('customer_phone', 'like', '%'.$keyword.'%');
            });
        }

        if (in_array($this->status, OrderStatus::values(), true)) {
            $query->where('status', $this->status);
        }

        if (in_array($this->shipping_status, ShippingStatus::values(), true)) {
            $query->where('shipping_status', $this->shipping_status);
        }

        if (in_array($this->payment_status, PaymentStatus::values(), true)) {
            $query->whereHas('payment', function ($builder): void {
                $builder->where('status', $this->payment_status);
            });
        }

        $orders = $query
            ->orderByRaw(
                'CASE WHEN status IN (?, ?, ?) THEN 0 ELSE 1 END, id DESC',
                [
                    OrderStatus::PENDING->value,
                    OrderStatus::PROCESSING->value,
                    OrderStatus::PAYMENT_FAILED->value,
                ]
            )
            ->paginate(12);

        $summary = [
            'total' => (int) Order::query()->count(),
            'pending' => (int) Order::query()->where('status', OrderStatus::PENDING->value)->count(),
            'processing' => (int) Order::query()->where('status', OrderStatus::PROCESSING->value)->count(),
            'completed' => (int) Order::query()->where('status', OrderStatus::COMPLETED->value)->count(),
            'payment_failed' => (int) Order::query()->where('status', OrderStatus::PAYMENT_FAILED->value)->count(),
        ];

        return view('livewire.admin.order-manager', [
            'orders' => $orders,
            'summary' => $summary,
            'orderStatuses' => OrderStatus::cases(),
            'shippingStatuses' => ShippingStatus::cases(),
            'paymentStatuses' => PaymentStatus::cases(),
            'statusLabel' => $this->status !== '' ? OrderStatus::tryFrom($this->status)?->label() : null,
            'shippingStatusLabel' => $this->shipping_status !== '' ? ShippingStatus::tryFrom($this->shipping_status)?->label() : null,
            'paymentStatusLabel' => $this->payment_status !== '' ? PaymentStatus::tryFrom($this->payment_status)?->label() : null,
        ]);
    }
}
