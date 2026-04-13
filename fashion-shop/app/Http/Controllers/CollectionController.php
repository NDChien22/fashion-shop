<?php

namespace App\Http\Controllers;

use App\Models\Collections;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CollectionController extends Controller
{
    public function showCollectionManager()
    {
        $collections = Collections::withCount('products')->orderBy('created_at', 'desc')->get();
        return view('pages.admin.collection-manager.collection-manager', compact('collections'));
    }

    public function addCollectionForm()
    {
        return view('pages.admin.collection-manager.add-collection');
    }

    public function addCollectionHandler(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'is_active' => 'nullable|boolean',
        ]);

        $collection = new Collections();
        $collection->name = $validated['name'];
        $collection->description = $validated['description'] ?? null;
        $collection->is_active = $request->has('is_active') ? 1 : 0;

        // Handle file upload
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $path = $file->store('collections', 'public');
            $collection->thumbnail_url = $path;
        }

        $collection->save();

        return redirect()->route('admin.product-collections')->with('success', 'Tạo bộ sưu tập thành công!');
    }

    public function showCollectionDetail(Collections $collection)
    {
        $products = $collection->products()
            ->with([
                'category:id,name',
                'collection:id,name',
                'skus:id,product_id,size,color,stock',
            ])
            ->withCount('skus')
            ->paginate(10);
        
        // Get products not in this collection
        $availableProducts = Products::where(function($query) use ($collection) {
            $query->whereNull('collection_id')
                  ->orWhere('collection_id', '!=', $collection->id);
        })
        ->where('is_active', 1)
        ->orderBy('name')
        ->get();
        
        return view('pages.admin.collection-manager.collection-detail', compact('collection', 'products', 'availableProducts'));
    }

    public function editCollectionForm(Collections $collection)
    {
        return view('pages.admin.collection-manager.edit-collection', compact('collection'));
    }

    public function updateCollectionHandler(Request $request, Collections $collection)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'is_active' => 'nullable|boolean',
        ]);

        $collection->name = $validated['name'];
        $collection->description = $validated['description'] ?? null;
        $collection->is_active = $request->has('is_active') ? 1 : 0;

        // Handle file upload
        if ($request->hasFile('thumbnail')) {
            // Delete old file if exists
            if ($collection->thumbnail_url && Storage::exists('public/' . $collection->thumbnail_url)) {
                Storage::delete('public/' . $collection->thumbnail_url);
            }
            
            $file = $request->file('thumbnail');
            $path = $file->store('collections', 'public');
            $collection->thumbnail_url = $path;
        }

        $collection->save();

        return redirect()->route('admin.product-collections')->with('success', 'Cập nhật bộ sưu tập thành công!');
    }

    public function deleteCollectionHandler(Collections $collection)
    {
        // Delete thumbnail if exists
        if ($collection->thumbnail_url && Storage::exists('public/' . $collection->thumbnail_url)) {
            Storage::delete('public/' . $collection->thumbnail_url);
        }

        $collection->delete();

        return redirect()->route('admin.product-collections')->with('success', 'Xóa bộ sưu tập thành công!');
    }

    public function removeProductFromCollection(Request $request, Collections $collection)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        Products::whereKey($validated['product_id'])->update([
            'collection_id' => null,
        ]);

        return redirect()->route('admin.show-collection', $collection->slug)->with('success', 'Xóa sản phẩm khỏi bộ sưu tập thành công!');
    }

    public function addProductToCollection(Request $request, Collections $collection)
    {
        $validated = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        Products::whereIn('id', $validated['product_ids'])->update([
            'collection_id' => $collection->id,
        ]);

        return redirect()->route('admin.show-collection', $collection->slug)->with('success', 'Thêm sản phẩm vào bộ sưu tập thành công!');
    }

    public function getProductsNotInCollection($collectionId)
    {
        $products = Products::where(function($query) use ($collectionId) {
            $query->whereNull('collection_id')
                  ->orWhere('collection_id', '!=', $collectionId);
        })
        ->where('is_active', 1)
        ->select('id', 'name', 'product_code')
        ->orderBy('name')
        ->get();

        return response()->json($products);
    }
}

