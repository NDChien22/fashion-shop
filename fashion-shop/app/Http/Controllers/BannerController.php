<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Categories;
use App\Models\Collections;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    public function bannerManagerView()
    {
        $banners = Banner::query()
            ->with(['category:id,name,slug', 'collection:id,name,slug'])
            ->orderByDesc('is_active')
            ->orderByDesc('id')
            ->get();

        return view('pages.admin.banner-manager.banner-manager', [
            'banners' => $banners,
        ]);
    }

    public function addBannerView()
    {
        return view('pages.admin.banner-manager.add-banner', $this->getBannerFormOptions());
    }

    public function editBannerView(Banner $banner)
    {
        $banner->loadMissing(['category:id,name,slug', 'collection:id,name,slug']);

        return view('pages.admin.banner-manager.edit-banner', [
            'banner' => $banner,
            ...$this->getBannerFormOptions(),
        ]);
    }

    public function storeBannerHandler(Request $request)
    {
        $validated = $this->validateBannerData($request);

        $validated['image_url'] = $this->storeBannerImage($request);
        $payload = $this->mapBannerPayload($validated);

        Banner::query()->create($payload);

        return redirect()
            ->route('admin.banner-manager')
            ->with('success', 'Tạo banner thành công.');
    }

    public function updateBannerHandler(Request $request, Banner $banner)
    {
        $validated = $this->validateBannerData($request, $banner);

        if ($request->hasFile('image_url')) {
            $newImagePath = $this->storeBannerImage($request);
            $this->deleteStoredBannerImage($banner->getRawOriginal('image_url'));
            $validated['image_url'] = $newImagePath;
        } else {
            unset($validated['image_url']);
        }

        $banner->update($this->mapBannerPayload($validated, $banner));

        return redirect()
            ->route('admin.banner-manager')
            ->with('success', 'Cập nhật banner thành công.');
    }

    public function deleteBannerHandler(Banner $banner)
    {
        $this->deleteStoredBannerImage($banner->getRawOriginal('image_url'));
        $banner->delete();

        return redirect()
            ->route('admin.banner-manager')
            ->with('success', 'Đã xóa banner.');
    }

    private function validateBannerData(Request $request, ?Banner $banner = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'banner_type' => ['required', 'in:all,category,collection'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id', 'required_if:banner_type,category'],
            'collection_id' => ['nullable', 'integer', 'exists:collections,id', 'required_if:banner_type,collection'],
            'image_url' => [$banner ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề banner.',
            'banner_type.required' => 'Vui lòng chọn loại banner.',
            'category_id.required_if' => 'Vui lòng chọn danh mục cần quảng bá.',
            'collection_id.required_if' => 'Vui lòng chọn bộ sưu tập cần quảng bá.',
            'image_url.required' => 'Vui lòng tải lên hình banner.',
            'image_url.image' => 'Banner phải là một hình ảnh hợp lệ.',
            'image_url.mimes' => 'Banner chỉ hỗ trợ jpg, jpeg, png, webp.',
            'image_url.max' => 'Banner không được vượt quá 2MB.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.',
        ]);
    }

    private function mapBannerPayload(array $validated, ?Banner $banner = null): array
    {
        $bannerType = $validated['banner_type'];

        return [
            'title' => trim($validated['title']),
            'banner_type' => $bannerType,
            'category_id' => $bannerType === 'category' ? ($validated['category_id'] ?? null) : null,
            'collection_id' => $bannerType === 'collection' ? ($validated['collection_id'] ?? null) : null,
            'image_url' => $validated['image_url'] ?? $banner?->getRawOriginal('image_url'),
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
        ];
    }

    private function getBannerFormOptions(): array
    {
        $categoryOptions = Categories::query()
            ->select('id', 'name', 'slug')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $collectionOptions = Collections::query()
            ->select('id', 'name', 'slug')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return [
            'categoryOptions' => $categoryOptions,
            'collectionOptions' => $collectionOptions,
        ];
    }

    private function storeBannerImage(Request $request): string
    {
        $imageFile = $request->file('image_url');
        $extension = $imageFile?->getClientOriginalExtension() ?: 'jpg';
        $fileName = sprintf('banner-%s.%s', Str::uuid()->toString(), $extension);

        return $imageFile?->storeAs('banners', $fileName, 'public') ?? '';
    }

    private function deleteStoredBannerImage(?string $path): void
    {
        if (!is_string($path) || $path === '' || Str::startsWith($path, ['http://', 'https://', '/'])) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}