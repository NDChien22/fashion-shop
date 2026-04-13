<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Collections;
use App\Models\ProductSkus;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function addProductForm(){
        $categories = Categories::query()
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        $parentCategories = Categories::query()
            ->whereNull('parent_id')
            ->where('is_active', 1)
            ->with([
                'children' => function ($query) {
                    $query->where('is_active', 1)->orderBy('name');
                },
            ])
            ->orderBy('name')
            ->get(['id', 'name']);

        $collections = Collections::query()
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('pages.admin.product-manager.add-product', [
            'categories' => $categories,
            'parentCategories' => $parentCategories,
            'showCategorySetup' => false,
            'collections' => $collections,
        ]);
    }

    public function addCategoryHandler(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['parent', 'child'])],
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'parent_id.exists' => 'Danh mục cha không hợp lệ.',
        ]);

        $isParent = $validated['type'] === 'parent';
        if (! $isParent && empty($validated['parent_id'])) {
            throw ValidationException::withMessages([
                'parent_id' => 'Vui lòng chọn parent_cate trước khi thêm cate.',
            ]);
        }

        $name = trim($validated['name']);
        $parentId = $isParent ? null : (int) $validated['parent_id'];

        $exists = Categories::query()
            ->where('name', $name)
            ->where('parent_id', $parentId)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'name' => $isParent
                    ? 'parent_cate này đã tồn tại.'
                    : 'cate này đã tồn tại trong parent_cate đã chọn.',
            ]);
        }

        Categories::create([
            'parent_id' => $parentId,
            'name' => $name,
            'is_active' => 1,
        ]);

        return redirect()
            ->route('admin.add-product')
            ->with('success', $isParent ? 'Thêm parent_cate thành công.' : 'Thêm cate thành công.');
    }

    public function editProductForm(Products $product){
        $product->load(['skus:id,product_id,size,color,stock']);
        $redirectTo = url()->previous();

        $parentCategories = Categories::query()
            ->whereNull('parent_id')
            ->where('is_active', 1)
            ->with([
                'children' => function ($query) {
                    $query->where('is_active', 1)->orderBy('name');
                },
            ])
            ->orderBy('name')
            ->get(['id', 'name']);

        $collections = Collections::query()
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('pages.admin.product-manager.edit-product', [
            'product' => $product,
            'parentCategories' => $parentCategories,
            'collections' => $collections,
            'redirectTo' => $redirectTo,
        ]);
    }

    public function updateProductHandler(Request $request, Products $product)
    {
        $validated = $request->validate([
            'product_code' => ['required', 'string', 'max:50', Rule::unique('products', 'product_code')->ignore($product->id)],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'category_id' => 'nullable|integer|exists:categories,id',
            'collection_id' => 'nullable|integer|exists:collections,id',
            'is_active' => 'required|in:0,1',
            'main_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gallery_images' => 'nullable|array|max:10',
            'gallery_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'removed_gallery_images' => 'nullable|array',
            'removed_gallery_images.*' => 'string',
            'variants' => 'required|array|min:1',
            'variants.*.size' => 'required|string|max:50',
            'variants.*.color' => 'required|string|max:50',
            'variants.*.stock' => 'required|integer|min:0',
        ], [
            'product_code.required' => 'Vui lòng nhập mã sản phẩm.',
            'product_code.unique' => 'Mã sản phẩm đã tồn tại.',
            'gallery_images.max' => 'Chỉ được tải tối đa 10 ảnh phụ.',
            'gallery_images.*.image' => 'Mỗi ảnh phụ phải là file ảnh hợp lệ.',
            'variants.required' => 'Vui lòng thêm ít nhất 1 biến thể sản phẩm.',
            'variants.*.size.required' => 'Size của biến thể là bắt buộc.',
            'variants.*.color.required' => 'Màu của biến thể là bắt buộc.',
            'variants.*.stock.required' => 'Số lượng tồn của biến thể là bắt buộc.',
        ]);

        $normalizedVariants = collect($validated['variants'])
            ->map(function (array $variant) {
                return [
                    'size' => trim($variant['size']),
                    'color' => trim($variant['color']),
                    'stock' => (int) $variant['stock'],
                ];
            })
            ->values();

        $duplicateVariant = $normalizedVariants
            ->groupBy(fn (array $variant) => Str::lower($variant['size']) . '|' . Str::lower($variant['color']))
            ->first(fn ($group) => $group->count() > 1);

        if ($duplicateVariant) {
            throw ValidationException::withMessages([
                'variants' => 'Không được thêm biến thể trùng size và màu.',
            ]);
        }

        $mainImageUrl = $product->main_image_url;
        if ($request->hasFile('main_image')) {
            $this->deletePublicStorageFile($product->main_image_url);
            $mainImagePath = $request->file('main_image')->store('products/main', 'public');
            $mainImageUrl = 'storage/' . $mainImagePath;
        }

        $existingGallery = is_array($product->gallery_image_urls) ? $product->gallery_image_urls : [];
        $removedGalleryImages = collect($request->input('removed_gallery_images', []))
            ->filter(fn ($path) => is_string($path) && trim($path) !== '')
            ->values()
            ->all();

        $removedGalleryImages = array_values(array_intersect($removedGalleryImages, $existingGallery));

        $galleryImageUrls = array_values(array_filter(
            $existingGallery,
            fn ($path) => ! in_array($path, $removedGalleryImages, true)
        ));

        if ($request->hasFile('gallery_images')) {
            $newGalleryCount = count($request->file('gallery_images'));
            if (count($galleryImageUrls) + $newGalleryCount > 10) {
                throw ValidationException::withMessages([
                    'gallery_images' => 'Tổng số ảnh album không được vượt quá 10 ảnh.',
                ]);
            }

            foreach ($request->file('gallery_images') as $image) {
                $path = $image->store('products/gallery', 'public');
                $galleryImageUrls[] = 'storage/' . $path;
            }
        }

        DB::transaction(function () use ($product, $validated, $normalizedVariants, $mainImageUrl, $galleryImageUrls) {
            $product->update([
                'product_code' => trim($validated['product_code']),
                'category_id' => isset($validated['category_id']) && $validated['category_id'] !== ''
                    ? (int) $validated['category_id']
                    : null,
                'collection_id' => isset($validated['collection_id']) && $validated['collection_id'] !== ''
                    ? (int) $validated['collection_id']
                    : null,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'base_price' => $validated['base_price'],
                'main_image_url' => $mainImageUrl,
                'gallery_image_urls' => empty($galleryImageUrls) ? null : $galleryImageUrls,
                'is_active' => (int) $validated['is_active'],
            ]);

            $product->skus()->delete();

            foreach ($normalizedVariants as $variant) {
                ProductSkus::create([
                    'product_id' => $product->id,
                    'sku' => $this->generateUniqueSku($product->id),
                    'size' => $variant['size'],
                    'color' => $variant['color'],
                    'stock' => (int) $variant['stock'],
                ]);
            }
        });

        foreach ($removedGalleryImages as $removedPath) {
            $this->deletePublicStorageFile($removedPath);
        }

        $redirectTo = (string) $request->input('redirect_to', '');

        if ($redirectTo !== '' && str_starts_with($redirectTo, url('/'))) {
            return redirect()
                ->to($redirectTo);
        }

        return redirect()
            ->route('admin.edit-product', $product->slug)
            ->with('success', 'Cập nhật sản phẩm thành công.');
    }

    private function deletePublicStorageFile(?string $path): void
    {
        if (! $path) {
            return;
        }

        $normalizedPath = ltrim($path, '/');
        if (str_starts_with($normalizedPath, 'storage/')) {
            $normalizedPath = substr($normalizedPath, strlen('storage/'));
        }

        if ($normalizedPath !== '' && Storage::disk('public')->exists($normalizedPath)) {
            Storage::disk('public')->delete($normalizedPath);
        }
    }

    public function addProductHandler(Request $request){
        $validated = $request->validate([
            'product_code' => 'required|string|max:50|unique:products,product_code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'category_id' => 'nullable|integer|exists:categories,id',
            'collection_id' => 'nullable|integer|exists:collections,id',
            'is_active' => 'required|in:0,1',
            'main_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gallery_images' => 'nullable|array|max:10',
            'gallery_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'variants' => 'required|array|min:1',
            'variants.*.size' => 'required|string|max:50',
            'variants.*.color' => 'required|string|max:50',
            'variants.*.stock' => 'required|integer|min:0',
        ], [
            'product_code.required' => 'Vui lòng nhập mã sản phẩm.',
            'product_code.unique' => 'Mã sản phẩm đã tồn tại.',
            'gallery_images.max' => 'Chỉ được tải tối đa 10 ảnh phụ.',
            'gallery_images.*.image' => 'Mỗi ảnh phụ phải là file ảnh hợp lệ.',
            'variants.required' => 'Vui lòng thêm ít nhất 1 biến thể sản phẩm.',
            'variants.*.size.required' => 'Size của biến thể là bắt buộc.',
            'variants.*.color.required' => 'Màu của biến thể là bắt buộc.',
            'variants.*.stock.required' => 'Số lượng tồn của biến thể là bắt buộc.',
        ]);

        $normalizedVariants = collect($validated['variants'])
            ->map(function (array $variant) {
                return [
                    'size' => trim($variant['size']),
                    'color' => trim($variant['color']),
                    'stock' => (int) $variant['stock'],
                ];
            })
            ->values();

        $duplicateVariant = $normalizedVariants
            ->groupBy(fn (array $variant) => Str::lower($variant['size']) . '|' . Str::lower($variant['color']))
            ->first(fn ($group) => $group->count() > 1);

        if ($duplicateVariant) {
            throw ValidationException::withMessages([
                'variants' => 'Không được thêm biến thể trùng size và màu.',
            ]);
        }

        $mainImageUrl = null;
        if ($request->hasFile('main_image')) {
            $mainImagePath = $request->file('main_image')->store('products/main', 'public');
            $mainImageUrl = 'storage/' . $mainImagePath;
        }

        $galleryImageUrls = [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $path = $image->store('products/gallery', 'public');
                $galleryImageUrls[] = 'storage/' . $path;
            }
        }

        DB::transaction(function () use ($validated, $normalizedVariants, $mainImageUrl, $galleryImageUrls) {
            $product = Products::create([
                'product_code' => trim($validated['product_code']),
                'category_id' => isset($validated['category_id']) && $validated['category_id'] !== ''
                    ? (int) $validated['category_id']
                    : null,
                'collection_id' => $validated['collection_id'] ?? null,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'base_price' => $validated['base_price'],
                'main_image_url' => $mainImageUrl,
                'gallery_image_urls' => empty($galleryImageUrls) ? null : $galleryImageUrls,
                'is_active' => (int) $validated['is_active'],
            ]);

            foreach ($normalizedVariants as $variant) {
                ProductSkus::create([
                    'product_id' => $product->id,
                    'sku' => $this->generateUniqueSku($product->id),
                    'size' => $variant['size'],
                    'color' => $variant['color'],
                    'stock' => (int) $variant['stock'],
                ]);
            }
        });

        return redirect()
            ->route('admin.add-product')
            ->with('success', 'Thêm sản phẩm thành công.');
    }

    private function generateUniqueSku(int $productId): string
    {
        do {
            $sku = 'PRD-' . $productId . '-' . Str::upper(Str::random(6));
        } while (ProductSkus::query()->where('sku', $sku)->exists());

        return $sku;
    }
}
