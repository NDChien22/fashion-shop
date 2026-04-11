<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Collections;
use App\Models\Products;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class VoucherController extends Controller
{
    public function VoucherManagerView()
    {
        return view('pages.admin.voucher-manager.voucher-manager');
    }

    public function addVoucherView()
    {
        return view('pages.admin.voucher-manager.add-voucher', $this->getVoucherFormOptions());
    }

    public function editVoucherView(Voucher $voucher)
    {
        return view('pages.admin.voucher-manager.edit-voucher', [
            'voucher' => $voucher,
            ...$this->getVoucherFormOptions(),
        ]);
    }

    private function getVoucherFormOptions(): array
    {
        $categories = Categories::query()
            ->select('id', 'name')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $collections = Collections::query()
            ->select('id', 'name')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $products = Products::query()
            ->select('id', 'name', 'product_code')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return [
            'categoryOptions' => $categories,
            'collectionOptions' => $collections,
            'productOptions' => $products,
        ];
    }

    public function storeVoucherHandler(Request $request)
    {
        $validated = $this->validateCreateVoucherData($request);

        Voucher::query()->create($this->mapVoucherPayload($validated));

        return redirect()
            ->route('admin.voucher-manager')
            ->with('success', 'Tạo voucher thành công.');
    }

    public function updateVoucherHandler(Request $request, Voucher $voucher)
    {
        $validated = $request->validate([
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ], [
            'usage_limit.min' => 'Số lượng phát hành phải lớn hơn 0.',
            'start_date.required' => 'Vui lòng nhập ngày bắt đầu.',
            'end_date.required' => 'Vui lòng nhập ngày kết thúc.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.',
        ]);

        $voucher->update([
            'usage_limit' => $validated['usage_limit'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
        ]);

        return redirect()
            ->route('admin.voucher-manager')
            ->with('success', 'Cập nhật voucher thành công.');
    }

    private function validateCreateVoucherData(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('vouchers', 'code'),
            ],
            'category' => ['required', Rule::in(['all', 'category', 'collection', 'product'])],
            'category_id' => ['nullable', 'integer', 'min:1', 'required_if:category,category'],
            'collection_id' => ['nullable', 'integer', 'min:1', 'required_if:category,collection'],
            'product_id' => ['nullable', 'integer', 'min:1', 'required_if:category,product'],
            'discount_type' => ['required', Rule::in(['percent', 'fixed', 'shipping'])],
            'discount_value' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'min_order_value' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'max_discount' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999999.99',
                'required_if:discount_type,percent',
                'prohibited_unless:discount_type,percent',
            ],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'code.required' => 'Vui lòng nhập mã voucher.',
            'code.unique' => 'Mã voucher đã tồn tại.',
            'category.required' => 'Vui lòng chọn phạm vi áp dụng.',
            'category_id.required_if' => 'Vui lòng chọn danh mục hợp lệ.',
            'collection_id.required_if' => 'Vui lòng chọn bộ sưu tập hợp lệ.',
            'product_id.required_if' => 'Vui lòng chọn sản phẩm hợp lệ.',
            'discount_type.required' => 'Vui lòng chọn loại giảm giá.',
            'discount_value.required' => 'Vui lòng nhập mức giảm.',
            'discount_value.min' => 'Mức giảm phải lớn hơn 0.',
            'discount_value.max' => 'Mức giảm không được vượt quá 9,999,999,999.99.',
            'min_order_value.required' => 'Vui lòng nhập giá trị đơn tối thiểu.',
            'min_order_value.max' => 'Đơn tối thiểu không được vượt quá 9,999,999,999.99.',
            'max_discount.required_if' => 'Vui lòng nhập mức giảm tối đa khi chọn giảm theo phần trăm.',
            'max_discount.prohibited_unless' => 'Giảm tối đa chỉ dùng cho voucher phần trăm.',
            'max_discount.max' => 'Giảm tối đa không được vượt quá 9,999,999,999.99.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.',
        ]);

        $validator->after(function ($validator) use ($request): void {
            $discountType = (string) $request->input('discount_type');
            $discountValue = (float) $request->input('discount_value', 0);
            $category = (string) $request->input('category');

            if ($discountType === 'percent' && $discountValue > 100) {
                $validator->errors()->add('discount_value', 'Giảm theo phần trăm không được vượt quá 100%.');
            }

            if ($discountType === 'shipping' && $category !== 'all') {
                $validator->errors()->add('category', 'Voucher giảm phí vận chuyển chỉ áp dụng cho toàn bộ đơn hàng.');
            }
        });

        return $validator->validate();
    }

    private function mapVoucherPayload(array $validated): array
    {
        $isShippingVoucher = $validated['discount_type'] === 'shipping';

        return [
            'code' => strtoupper(trim($validated['code'])),
            'category' => $isShippingVoucher ? 'all' : $validated['category'],
            'category_id' => $isShippingVoucher
                ? null
                : ($validated['category'] === 'category' ? ($validated['category_id'] ?? null) : null),
            'collection_id' => $isShippingVoucher
                ? null
                : ($validated['category'] === 'collection' ? ($validated['collection_id'] ?? null) : null),
            'product_id' => $isShippingVoucher
                ? null
                : ($validated['category'] === 'product' ? ($validated['product_id'] ?? null) : null),
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'min_order_value' => $validated['min_order_value'],
            'max_discount' => $validated['discount_type'] === 'percent' ? ($validated['max_discount'] ?? null) : null,
            'usage_limit' => $validated['usage_limit'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ];
    }
}
