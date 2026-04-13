<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Collections;
use App\Models\FlashSale;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FlashSaleController extends Controller
{
    public function flashSaleManagerView()
    {
        return view('pages.admin.flash-sale-manager.flash-sale-manager');
    }

    public function addFlashSaleView()
    {
        return view('pages.admin.flash-sale-manager.add-flash-sale', $this->getFlashSaleFormOptions());
    }

    public function editFlashSaleView(FlashSale $flashSale)
    {
        return view('pages.admin.flash-sale-manager.edit-flash-sale', [
            'flashSale' => $flashSale,
            ...$this->getFlashSaleFormOptions(),
        ]);
    }

    public function storeFlashSaleHandler(Request $request)
    {
        $validated = $this->validateFlashSaleData($request);

        FlashSale::query()->create($this->mapFlashSalePayload($validated));

        return redirect()
            ->route('admin.flash-sale-manager')
            ->with('success', 'Tạo flash sale thành công.');
    }

    public function updateFlashSaleHandler(Request $request, FlashSale $flashSale)
    {
        $validated = $this->validateFlashSaleData($request, $flashSale);

        $flashSale->update($this->mapFlashSalePayload($validated));

        return redirect()
            ->route('admin.flash-sale-manager')
            ->with('success', 'Cập nhật flash sale thành công.');
    }

    public function deleteFlashSaleHandler(FlashSale $flashSale)
    {
        $flashSale->delete();

        return redirect()
            ->route('admin.flash-sale-manager')
            ->with('success', 'Đã xóa flash sale.');
    }

    private function getFlashSaleFormOptions(): array
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

    private function validateFlashSaleData(Request $request, ?FlashSale $flashSale = null): array
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('flash_sales', 'name')->ignore($flashSale?->id),
            ],
            'discount_type' => ['required', Rule::in(['percent', 'fixed'])],
            'discount_value' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'scope' => ['required', Rule::in(['all', 'category', 'collection', 'product'])],
            'category_id' => ['nullable', 'integer', 'exists:categories,id', 'required_if:scope,category'],
            'collection_id' => ['nullable', 'integer', 'exists:collections,id', 'required_if:scope,collection'],
            'product_id' => ['nullable', 'integer', 'exists:products,id', 'required_if:scope,product'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Vui lòng nhập tên chương trình.',
            'name.unique' => 'Tên chương trình đã tồn tại.',
            'discount_type.required' => 'Vui lòng chọn kiểu giảm giá.',
            'discount_value.required' => 'Vui lòng nhập mức giảm.',
            'discount_value.min' => 'Mức giảm phải lớn hơn 0.',
            'scope.required' => 'Vui lòng chọn phạm vi áp dụng.',
            'category_id.required_if' => 'Vui lòng chọn danh mục áp dụng.',
            'collection_id.required_if' => 'Vui lòng chọn bộ sưu tập áp dụng.',
            'product_id.required_if' => 'Vui lòng chọn sản phẩm áp dụng.',
            'usage_limit.min' => 'Giới hạn số lượng bán phải lớn hơn 0.',
            'start_date.required' => 'Vui lòng nhập ngày bắt đầu.',
            'end_date.required' => 'Vui lòng nhập ngày kết thúc.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.',
        ]);

        $validator->after(function ($validator) use ($request): void {
            $discountType = (string) $request->input('discount_type');
            $discountValue = (float) $request->input('discount_value', 0);

            if ($discountType === 'percent' && $discountValue > 100) {
                $validator->errors()->add('discount_value', 'Giảm theo phần trăm không được vượt quá 100%.');
            }
        });

        return $validator->validate();
    }

    private function mapFlashSalePayload(array $validated): array
    {
        $scope = $validated['scope'];

        return [
            'name' => trim($validated['name']),
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'scope' => $scope,
            'category_id' => $scope === 'category' ? ($validated['category_id'] ?? null) : null,
            'collection_id' => $scope === 'collection' ? ($validated['collection_id'] ?? null) : null,
            'product_id' => $scope === 'product' ? ($validated['product_id'] ?? null) : null,
            'usage_limit' => $validated['usage_limit'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ];
    }
}