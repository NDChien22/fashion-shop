<?php

namespace App\Livewire\Admin;

use App\Enums\OrderReturnRequestStatus;
use App\Enums\OrderReturnRequestType;
use App\Models\OrderReturnRequest;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ReturnRequestManager extends Component
{
    use WithPagination;

    public string $q = '';

    public string $status = '';

    public string $type = '';

    protected $queryString = [
        'q' => ['except' => ''],
        'status' => ['except' => ''],
        'type' => ['except' => ''],
    ];

    public function updatingQ(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingType(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['q', 'status', 'type']);
        $this->resetPage();
    }

    public function render(): View
    {
        $query = OrderReturnRequest::query()
            ->with([
                'order:id,order_code,status,shipping_status,payment_method,guest_name,guest_phone,guest_email,final_amount,created_at',
                'order.payment:id,order_id,status,payment_method,transaction_id,amount',
                'user:id,full_name,email,phone_number',
                'admin:id,user_id,employee_code',
                'admin.user:id,full_name,email,phone_number',
            ]);

        $keyword = trim($this->q);
        if ($keyword !== '') {
            $query->where(function ($builder) use ($keyword): void {
                $builder->where('reason', 'like', '%'.$keyword.'%')
                    ->orWhere('details', 'like', '%'.$keyword.'%')
                    ->orWhereHas('order', function ($orderQuery) use ($keyword): void {
                        $orderQuery->where('order_code', 'like', '%'.$keyword.'%')
                            ->orWhere('guest_name', 'like', '%'.$keyword.'%')
                            ->orWhere('guest_phone', 'like', '%'.$keyword.'%');
                    });
            });
        }

        if (in_array($this->status, OrderReturnRequestStatus::values(), true)) {
            $query->where('status', $this->status);
        }

        if (in_array($this->type, OrderReturnRequestType::values(), true)) {
            $query->where('request_type', $this->type);
        }

        $returnRequests = $query
            ->orderByRaw('CASE WHEN status = ? THEN 0 ELSE 1 END, id DESC', [OrderReturnRequestStatus::PENDING->value])
            ->paginate(10);

        $summary = [
            'total' => (int) OrderReturnRequest::query()->count(),
            'pending' => (int) OrderReturnRequest::query()->where('status', OrderReturnRequestStatus::PENDING->value)->count(),
            'approved' => (int) OrderReturnRequest::query()->where('status', OrderReturnRequestStatus::APPROVED->value)->count(),
            'rejected' => (int) OrderReturnRequest::query()->where('status', OrderReturnRequestStatus::REJECTED->value)->count(),
            'completed' => (int) OrderReturnRequest::query()->where('status', OrderReturnRequestStatus::COMPLETED->value)->count(),
            'today' => (int) OrderReturnRequest::query()->whereBetween('created_at', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()])->count(),
        ];

        return view('livewire.admin.return-request-manager', [
            'returnRequests' => $returnRequests,
            'summary' => $summary,
            'statusOptions' => OrderReturnRequestStatus::cases(),
            'typeOptions' => OrderReturnRequestType::cases(),
            'statusLabel' => $this->status !== '' ? OrderReturnRequestStatus::tryFrom($this->status)?->label() : null,
            'typeLabel' => $this->type !== '' ? OrderReturnRequestType::tryFrom($this->type)?->label() : null,
        ]);
    }
}
