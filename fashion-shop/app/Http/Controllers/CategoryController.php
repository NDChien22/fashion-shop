<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Products;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $parentCategories = Categories::query()
            ->whereNull('parent_id')
            ->withCount('products')
            ->with([
                'children' => function ($query) {
                    $query->withCount('products')->orderBy('name');
                },
            ])
            ->orderBy('name')
            ->get();

        return view('pages.admin.product-category-manager.category-manager', [
            'parentCategories' => $parentCategories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => ['required', Rule::in(['parent', 'child'])],
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'parent_id.exists' => 'Danh mục cha không hợp lệ.',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator, 'category')
                ->withInput()
                ->with('category_modal_open', true);
        }

        $validated = $validator->validated();

        $isParent = $validated['type'] === 'parent';
        if (! $isParent && empty($validated['parent_id'])) {
            return redirect()
                ->back()
                ->withErrors([
                    'parent_id' => 'Vui lòng chọn parent_cate trước khi thêm cate.',
                ], 'category')
                ->withInput()
                ->with('category_modal_open', true);
        }

        $name = trim($validated['name']);
        $parentId = $isParent ? null : (int) $validated['parent_id'];

        $exists = Categories::query()
            ->where('name', $name)
            ->where('parent_id', $parentId)
            ->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->withErrors([
                    'name' => $isParent
                        ? 'parent_cate này đã tồn tại.'
                        : 'cate này đã tồn tại trong parent_cate đã chọn.',
                ], 'category')
                ->withInput()
                ->with('category_modal_open', true);
        }

        Categories::create([
            'parent_id' => $parentId,
            'name' => $name,
            'is_active' => 1,
        ]);

        $message = $isParent ? 'Thêm parent_cate thành công.' : 'Thêm cate thành công.';

        return redirect()
            ->back()
            ->with('success', $message)
            ->with('category_success', $message)
            ->with('category_modal_open', true);
    }

    public function destroy(int $id): RedirectResponse
    {
        $category = Categories::query()->findOrFail($id);

        DB::transaction(function () use ($category) {
            Products::query()
                ->where('category_id', $category->id)
                ->update(['category_id' => null]);

            Categories::query()
                ->where('parent_id', $category->id)
                ->update(['parent_id' => $category->parent_id]);

            $category->delete();
        });

        return redirect()->back()->with('success', 'Xóa danh mục thành công. Sản phẩm được giữ lại và bỏ gán danh mục.');
    }
}
