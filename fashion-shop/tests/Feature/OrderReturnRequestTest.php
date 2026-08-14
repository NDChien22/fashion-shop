<?php

use App\Enums\OrderReturnRequestStatus;
use App\Enums\OrderReturnRequestType;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShippingStatus;
use App\Livewire\Admin\ReturnRequestManager;
use App\Models\Order;
use App\Models\OrderReturnRequest;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('allows a customer to submit a return request for a delivered order', function (): void {
    Storage::fake('public');

    $customer = User::factory()->create([
        'role' => 'customer',
    ]);

    $order = Order::query()->create([
        'user_id' => $customer->id,
        'order_code' => 'ORD-RET-001',
        'total_amount' => 120000,
        'discount_amount' => 0,
        'final_amount' => 120000,
        'status' => OrderStatus::COMPLETED->value,
        'shipping_status' => ShippingStatus::DELIVERED->value,
        'payment_method' => 'cod',
        'customer_name' => 'Customer Demo',
        'customer_email' => 'customer@example.com',
        'customer_phone' => '0900000000',
        'shipping_address' => 'Test address',
    ]);

    $this->actingAs($customer)
        ->from(route('user.orders'))
        ->post(route('user.orders.return-request', $order), [
            'return_order_id' => $order->id,
            'request_type' => OrderReturnRequestType::RETURN->value,
            'reason' => 'Sản phẩm không đúng kích cỡ mong muốn.',
            'details' => 'Tôi muốn đổi sang size lớn hơn.',
            'evidence_images' => [
                UploadedFile::fake()->image('front.jpg'),
                UploadedFile::fake()->image('defect.jpg'),
            ],
        ])
        ->assertRedirect(route('user.orders'));

    $returnRequest = OrderReturnRequest::query()->firstOrFail();

    expect($returnRequest->order_id)->toBe($order->id)
        ->and($returnRequest->user_id)->toBe($customer->id)
        ->and($returnRequest->request_type?->value)->toBe(OrderReturnRequestType::RETURN->value)
        ->and($returnRequest->status?->value)->toBe(OrderReturnRequestStatus::PENDING->value)
        ->and($returnRequest->evidence_images)->toBeArray()
        ->and($returnRequest->evidence_images)->toHaveCount(2);

    foreach ($returnRequest->evidence_images as $imagePath) {
        $checkPath = ltrim((string) str_replace('storage/', '', $imagePath), '/');
        expect(Storage::disk('public')->exists($checkPath))->toBeTrue();
    }
});

it('blocks customer from submitting return request after 7 days from completion', function (): void {
    Carbon::setTestNow('2026-05-14 10:00:00');

    $customer = User::factory()->create([
        'role' => 'customer',
    ]);

    $order = Order::query()->create([
        'user_id' => $customer->id,
        'order_code' => 'ORD-RET-003',
        'total_amount' => 125000,
        'discount_amount' => 0,
        'final_amount' => 125000,
        'status' => OrderStatus::COMPLETED->value,
        'shipping_status' => ShippingStatus::DELIVERED->value,
        'payment_method' => 'cod',
        'customer_name' => 'Customer Demo',
        'customer_email' => 'customer3@example.com',
        'customer_phone' => '0900000002',
        'shipping_address' => 'Test address 3',
    ]);

    Order::query()->whereKey($order->id)->update([
        'updated_at' => Carbon::now()->subDays(8),
    ]);

    $this->actingAs($customer)
        ->from(route('user.orders'))
        ->post(route('user.orders.return-request', $order), [
            'return_order_id' => $order->id,
            'request_type' => OrderReturnRequestType::RETURN->value,
            'reason' => 'Muốn trả hàng do không phù hợp.',
            'details' => 'Đơn đã nhận và kiểm tra.',
        ])
        ->assertRedirect(route('user.orders'));

    $this->assertDatabaseMissing('order_return_requests', [
        'order_id' => $order->id,
    ]);

    Carbon::setTestNow();
});

it('allows an admin to resolve a return request', function (): void {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $customer = User::factory()->create([
        'role' => 'customer',
    ]);

    $order = Order::query()->create([
        'user_id' => $customer->id,
        'order_code' => 'ORD-RET-002',
        'total_amount' => 98000,
        'discount_amount' => 0,
        'final_amount' => 98000,
        'status' => OrderStatus::COMPLETED->value,
        'shipping_status' => ShippingStatus::DELIVERED->value,
        'payment_method' => 'cod',
        'customer_name' => 'Customer Demo',
        'customer_email' => 'customer2@example.com',
        'customer_phone' => '0900000001',
        'shipping_address' => 'Test address 2',
    ]);

    $returnRequest = OrderReturnRequest::query()->create([
        'order_id' => $order->id,
        'user_id' => $customer->id,
        'request_type' => OrderReturnRequestType::EXCHANGE->value,
        'reason' => 'Áo bị lỗi đường may.',
        'details' => 'Mong shop đổi lại sản phẩm khác cùng mẫu.',
        'status' => OrderReturnRequestStatus::PENDING->value,
    ]);

    $this->actingAs($admin)
        ->from(route('admin.orders'))
        ->put(route('admin.orders.return-request.update', $returnRequest), [
            'status' => OrderReturnRequestStatus::APPROVED->value,
            'admin_note' => 'Đã xác nhận đổi hàng, vui lòng gửi sản phẩm về kho.',
        ])
        ->assertRedirect(route('admin.orders'));

    $this->assertDatabaseHas('order_return_requests', [
        'id' => $returnRequest->id,
        'status' => OrderReturnRequestStatus::APPROVED->value,
        'admin_note' => 'Đã xác nhận đổi hàng, vui lòng gửi sản phẩm về kho.',
        'admin_id' => $admin->id,
    ]);
});

it('blocks admin from resolving return request after 7 days from completion', function (): void {
    Carbon::setTestNow('2026-05-14 10:00:00');

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $customer = User::factory()->create([
        'role' => 'customer',
    ]);

    $order = Order::query()->create([
        'user_id' => $customer->id,
        'order_code' => 'ORD-RET-004',
        'total_amount' => 93000,
        'discount_amount' => 0,
        'final_amount' => 93000,
        'status' => OrderStatus::COMPLETED->value,
        'shipping_status' => ShippingStatus::DELIVERED->value,
        'payment_method' => 'cod',
        'customer_name' => 'Customer Demo',
        'customer_email' => 'customer4@example.com',
        'customer_phone' => '0900000003',
        'shipping_address' => 'Test address 4',
    ]);

    Order::query()->whereKey($order->id)->update([
        'updated_at' => Carbon::now()->subDays(8),
    ]);

    $returnRequest = OrderReturnRequest::query()->create([
        'order_id' => $order->id,
        'user_id' => $customer->id,
        'request_type' => OrderReturnRequestType::RETURN->value,
        'reason' => 'Sản phẩm bị lỗi nhẹ.',
        'details' => 'Mong hỗ trợ đổi/trả.',
        'status' => OrderReturnRequestStatus::PENDING->value,
    ]);

    $this->actingAs($admin)
        ->from(route('admin.orders'))
        ->put(route('admin.orders.return-request.update', $returnRequest), [
            'status' => OrderReturnRequestStatus::APPROVED->value,
            'admin_note' => 'Thử xử lý sau hạn.',
        ])
        ->assertRedirect(route('admin.orders'));

    $returnRequest->refresh();

    expect($returnRequest->status?->value)->toBe(OrderReturnRequestStatus::PENDING->value)
        ->and($returnRequest->admin_id)->toBeNull();

    Carbon::setTestNow();
});

it('filters return requests live with the admin component', function (): void {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $customer = User::factory()->create([
        'role' => 'customer',
    ]);

    $pendingOrder = Order::query()->create([
        'user_id' => $customer->id,
        'order_code' => 'ORD-LIVEWIRE-001',
        'total_amount' => 100000,
        'discount_amount' => 0,
        'final_amount' => 100000,
        'status' => OrderStatus::COMPLETED->value,
        'shipping_status' => ShippingStatus::DELIVERED->value,
        'payment_method' => 'cod',
        'customer_name' => 'Pending Customer',
        'customer_email' => 'pending@example.com',
        'customer_phone' => '0900000010',
        'shipping_address' => 'Pending address',
    ]);

    $approvedOrder = Order::query()->create([
        'user_id' => $customer->id,
        'order_code' => 'ORD-LIVEWIRE-002',
        'total_amount' => 110000,
        'discount_amount' => 0,
        'final_amount' => 110000,
        'status' => OrderStatus::COMPLETED->value,
        'shipping_status' => ShippingStatus::DELIVERED->value,
        'payment_method' => 'cod',
        'customer_name' => 'Approved Customer',
        'customer_email' => 'approved@example.com',
        'customer_phone' => '0900000011',
        'shipping_address' => 'Approved address',
    ]);

    OrderReturnRequest::query()->create([
        'order_id' => $pendingOrder->id,
        'user_id' => $customer->id,
        'request_type' => OrderReturnRequestType::RETURN->value,
        'reason' => 'Cần kiểm tra Livewire pending',
        'details' => 'Bản ghi chờ xử lý',
        'status' => OrderReturnRequestStatus::PENDING->value,
    ]);

    OrderReturnRequest::query()->create([
        'order_id' => $approvedOrder->id,
        'user_id' => $customer->id,
        'request_type' => OrderReturnRequestType::EXCHANGE->value,
        'reason' => 'Cần kiểm tra Livewire approved',
        'details' => 'Bản ghi đã duyệt',
        'status' => OrderReturnRequestStatus::APPROVED->value,
    ]);

    $this->actingAs($admin);

    Livewire::test(ReturnRequestManager::class)
        ->assertSee('ORD-LIVEWIRE-001')
        ->assertSee('ORD-LIVEWIRE-002')
        ->set('status', OrderReturnRequestStatus::APPROVED->value)
        ->assertSee('ORD-LIVEWIRE-002')
        ->assertDontSee('ORD-LIVEWIRE-001');
});

it('marks COD orders as paid when admin completes the order', function (): void {
    Mail::fake();

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $customer = User::factory()->create([
        'role' => 'customer',
    ]);

    $order = Order::query()->create([
        'user_id' => $customer->id,
        'order_code' => 'ORD-COD-001',
        'total_amount' => 145000,
        'discount_amount' => 0,
        'final_amount' => 145000,
        'status' => OrderStatus::PROCESSING->value,
        'shipping_status' => ShippingStatus::PENDING->value,
        'payment_method' => 'cod',
        'customer_name' => 'Customer Demo',
        'customer_email' => 'customer-cod@example.com',
        'customer_phone' => '0900000004',
        'shipping_address' => 'Test address 5',
    ]);

    Payment::query()->create([
        'order_id' => $order->id,
        'amount' => 145000,
        'payment_method' => 'cod',
        'transaction_id' => 'COD-TXN-001',
        'status' => PaymentStatus::PENDING->value,
    ]);

    $this->actingAs($admin)
        ->from(route('admin.orders'))
        ->put(route('admin.orders.update', $order), [
            'status' => OrderStatus::COMPLETED->value,
            'shipping_status' => ShippingStatus::PENDING->value,
        ])
        ->assertRedirect(route('admin.orders'));

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => OrderStatus::COMPLETED->value,
        'shipping_status' => ShippingStatus::DELIVERED->value,
    ]);

    $this->assertDatabaseHas('payments', [
        'order_id' => $order->id,
        'status' => PaymentStatus::PAID->value,
    ]);
});

it('marks completed return requests as refunded for return orders', function (): void {
    Mail::fake();

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $customer = User::factory()->create([
        'role' => 'customer',
    ]);

    $order = Order::query()->create([
        'user_id' => $customer->id,
        'order_code' => 'ORD-REF-001',
        'total_amount' => 188000,
        'discount_amount' => 0,
        'final_amount' => 188000,
        'status' => OrderStatus::COMPLETED->value,
        'shipping_status' => ShippingStatus::DELIVERED->value,
        'payment_method' => 'cod',
        'customer_name' => 'Customer Demo',
        'customer_email' => 'customer-refund@example.com',
        'customer_phone' => '0900000005',
        'shipping_address' => 'Test address 6',
    ]);

    Payment::query()->create([
        'order_id' => $order->id,
        'amount' => 188000,
        'payment_method' => 'cod',
        'transaction_id' => 'COD-TXN-002',
        'status' => PaymentStatus::PAID->value,
    ]);

    $returnRequest = OrderReturnRequest::query()->create([
        'order_id' => $order->id,
        'user_id' => $customer->id,
        'request_type' => OrderReturnRequestType::RETURN->value,
        'reason' => 'Muốn trả hàng và hoàn tiền.',
        'details' => 'Sản phẩm không phù hợp.',
        'status' => OrderReturnRequestStatus::PENDING->value,
    ]);

    $this->actingAs($admin)
        ->from(route('admin.orders'))
        ->put(route('admin.orders.return-request.update', $returnRequest), [
            'status' => OrderReturnRequestStatus::COMPLETED->value,
            'admin_note' => 'Đã xử lý hoàn tiền cho đơn trả hàng.',
        ])
        ->assertRedirect(route('admin.orders'));

    $this->assertDatabaseHas('order_return_requests', [
        'id' => $returnRequest->id,
        'status' => OrderReturnRequestStatus::COMPLETED->value,
    ]);

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => OrderStatus::RETURNED->value,
    ]);

    $this->assertDatabaseHas('payments', [
        'order_id' => $order->id,
        'status' => PaymentStatus::REFUNDED->value,
    ]);
});
