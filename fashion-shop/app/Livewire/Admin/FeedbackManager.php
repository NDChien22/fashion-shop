<?php

namespace App\Livewire\Admin;

use App\Models\OrderFeedback;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class FeedbackManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $ratingFilter = '';

    public ?int $replyFeedbackId = null;

    public string $replyContent = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'ratingFilter' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRatingFilter(): void
    {
        $this->resetPage();
    }

    public function deleteFeedback(int $feedbackId): void
    {
        $feedback = OrderFeedback::query()->find($feedbackId);

        if (! $feedback) {
            session()->flash('error', 'Không tìm thấy feedback cần xóa.');

            return;
        }

        $feedback->delete();

        session()->flash('success', 'Đã xóa feedback.');
        $this->resetPage();
    }

    public function editReply(int $feedbackId): void
    {
        $feedback = OrderFeedback::query()->find($feedbackId);

        if (! $feedback) {
            session()->flash('error', 'Không tìm thấy feedback cần phản hồi.');

            return;
        }

        $this->replyFeedbackId = $feedback->id;
        $this->replyContent = (string) ($feedback->admin_reply ?? '');
    }

    public function cancelReply(): void
    {
        $this->replyFeedbackId = null;
        $this->replyContent = '';
    }

    public function saveReply(): void
    {
        if (! $this->replyFeedbackId) {
            session()->flash('error', 'Vui lòng chọn feedback cần phản hồi.');

            return;
        }

        $validated = $this->validate([
            'replyContent' => ['required', 'string', 'min:3', 'max:5000'],
        ], [
            'replyContent.required' => 'Vui lòng nhập nội dung phản hồi.',
            'replyContent.min' => 'Nội dung phản hồi phải có ít nhất 3 ký tự.',
            'replyContent.max' => 'Nội dung phản hồi không được vượt quá 5000 ký tự.',
        ]);

        $feedback = OrderFeedback::query()->find($this->replyFeedbackId);

        if (! $feedback) {
            session()->flash('error', 'Không tìm thấy feedback cần phản hồi.');

            return;
        }

        $feedback->update([
            'admin_reply' => trim((string) $validated['replyContent']),
            'admin_replied_at' => now(),
            'admin_reply_by' => Auth::id(),
        ]);

        session()->flash('success', 'Đã lưu phản hồi cho feedback.');
        $this->cancelReply();
        $this->resetPage();
    }

    public function render()
    {
        $query = OrderFeedback::query()
            ->with([
                'order:id,order_code,user_id,guest_name,guest_email,guest_phone,final_amount,created_at',
                'product:id,name,slug',
                'user:id,username,full_name,email',
                'adminReplyUser:id,username,full_name,email',
            ])
            ->when(trim($this->search) !== '', function (Builder $builder): void {
                $keyword = trim($this->search);

                $builder->where(function (Builder $scope) use ($keyword): void {
                    $scope->where('content', 'like', '%'.$keyword.'%')
                        ->orWhereHas('order', function (Builder $orderQuery) use ($keyword): void {
                            $orderQuery->where('order_code', 'like', '%'.$keyword.'%')
                                ->orWhere('guest_name', 'like', '%'.$keyword.'%')
                                ->orWhere('guest_email', 'like', '%'.$keyword.'%')
                                ->orWhere('guest_phone', 'like', '%'.$keyword.'%');
                        })
                        ->orWhereHas('product', function (Builder $productQuery) use ($keyword): void {
                            $productQuery->where('name', 'like', '%'.$keyword.'%');
                        })
                        ->orWhereHas('user', function (Builder $userQuery) use ($keyword): void {
                            $userQuery->where('username', 'like', '%'.$keyword.'%')
                                ->orWhere('full_name', 'like', '%'.$keyword.'%')
                                ->orWhere('email', 'like', '%'.$keyword.'%');
                        });
                });
            })
            ->when(in_array((int) $this->ratingFilter, [1, 2, 3, 4, 5], true), function (Builder $builder): void {
                $builder->where('rating', (int) $this->ratingFilter);
            });

        $feedbacks = $query
            ->latest('id')
            ->paginate(10);

        $summary = [
            'total' => (int) OrderFeedback::query()->count(),
            'average_rating' => round((float) OrderFeedback::query()->avg('rating'), 1),
            'five_star' => (int) OrderFeedback::query()->where('rating', 5)->count(),
            'recent' => (int) OrderFeedback::query()->where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('livewire.admin.feedback-manager', [
            'feedbacks' => $feedbacks,
            'summary' => $summary,
        ]);
    }
}
