<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusNotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public const EVENT_PLACED = 'placed';

    public const EVENT_DELIVERED = 'delivered';

    public const EVENT_COMPLETED = 'completed';

    public const EVENT_CANCELLED = 'cancelled';

    public function __construct(
        public Order $order,
        public string $event,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectByEvent(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-status-notification',
            with: [
                'order' => $this->order,
                'event' => $this->event,
                'eventLabel' => $this->eventLabelByEvent(),
                'trackingUrl' => $this->trackingUrl(),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }

    private function subjectByEvent(): string
    {
        return match ($this->event) {
            self::EVENT_PLACED => 'FashionShop - Đặt hàng thành công',
            self::EVENT_DELIVERED => 'FashionShop - Đơn hàng đã được giao',
            self::EVENT_COMPLETED => 'FashionShop - Đơn hàng đã hoàn thành',
            self::EVENT_CANCELLED => 'FashionShop - Đơn hàng đã hủy',
            default => 'FashionShop - Cập nhật đơn hàng',
        };
    }

    private function eventLabelByEvent(): string
    {
        return match ($this->event) {
            self::EVENT_PLACED => 'Đặt hàng thành công',
            self::EVENT_DELIVERED => 'Đơn hàng đã giao',
            self::EVENT_COMPLETED => 'Đơn hàng hoàn thành',
            self::EVENT_CANCELLED => 'Đơn hàng đã hủy',
            default => 'Cập nhật đơn hàng',
        };
    }

    private function trackingUrl(): string
    {
        return route('user.orders', [
            'q' => (string) $this->order->order_code,
        ]);
    }
}
