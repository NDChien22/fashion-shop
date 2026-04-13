@php
    $isEdit = isset($banner) && $banner;
    $formAction = $isEdit ? route('admin.update-banner', $banner) : route('admin.store-banner');
    $title = old('title', $banner->title ?? '');
    $bannerType = old('banner_type', $banner->banner_type ?? 'category');
    $categoryId = old('category_id', $banner->category_id ?? '');
    $collectionId = old('collection_id', $banner->collection_id ?? '');
    $startDate = old('start_date', isset($banner) && $banner->start_date ? $banner->start_date->format('Y-m-d') : '');
    $endDate = old('end_date', isset($banner) && $banner->end_date ? $banner->end_date->format('Y-m-d') : '');
    $isActive = (bool) old('is_active', isset($banner) ? $banner->is_active : true);
    $bannerPreview = null;

    if (isset($banner) && $banner->image_url) {
        $bannerPreview = \Illuminate\Support\Str::startsWith($banner->image_url, ['http://', 'https://', '/'])
            ? $banner->image_url
            : \Illuminate\Support\Facades\Storage::url($banner->image_url);
    }
@endphp

<div
    class="max-w-5xl mx-auto my-8 bg-white rounded-4xl border border-gray-50 shadow-2xl shadow-gray-200/40 overflow-hidden">
    <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" class="p-8 md:p-10 space-y-8">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-7 space-y-6">
                <div class="rounded-[28px] bg-gray-50/80 border border-gray-100 p-6 space-y-5">
                    <div class="flex items-center gap-3">
                        <span
                            class="w-9 h-9 rounded-xl bg-[#bc9c75] text-white flex items-center justify-center font-black text-xs">1</span>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Thông tin banner</h3>
                            <p class="text-sm text-gray-500">Nhập nội dung chính và xác định cách banner xuất hiện trên
                                trang chủ.</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase tracking-[0.2em] font-black text-gray-400 ml-1">Tiêu
                            đề</label>
                        <input type="text" name="title" value="{{ $title }}"
                            placeholder="Ví dụ: Summer Sale 2026"
                            class="w-full px-4 py-3.5 rounded-2xl border border-gray-200 bg-white outline-none focus:border-[#bc9c75]/40 text-sm font-semibold">
                        @error('title')
                            <p class="text-xs text-red-500 ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase tracking-[0.2em] font-black text-gray-400 ml-1">Loại
                            banner</label>
                        <select name="banner_type" id="banner-type-select"
                            class="w-full px-4 py-3.5 rounded-2xl border border-gray-200 bg-white outline-none focus:border-[#bc9c75]/40 text-sm font-semibold">
                            <option value="all" @selected($bannerType === 'all')>Toàn bộ sản phẩm</option>
                            <option value="category" @selected($bannerType === 'category')>Danh mục sản phẩm</option>
                            <option value="collection" @selected($bannerType === 'collection')>Bộ sưu tập</option>
                        </select>
                        @error('banner_type')
                            <p class="text-xs text-red-500 ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-4 rounded-3xl bg-white p-4 border border-gray-100">
                        <div class="text-[10px] uppercase tracking-[0.2em] font-black text-gray-400 ml-1">Mục tiêu quảng
                            bá</div>

                        <div data-banner-target-panel="all" class="space-y-2 hidden">
                            <div
                                class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-600">
                                Banner này sẽ dẫn đến toàn bộ danh sách sản phẩm.
                            </div>
                        </div>

                        <div data-banner-target-panel="category" class="space-y-2 hidden">
                            <label class="text-[10px] uppercase tracking-[0.2em] font-black text-gray-400 ml-1">Chọn
                                danh mục</label>
                            <select name="category_id"
                                class="w-full px-4 py-3.5 rounded-2xl border border-gray-200 bg-white outline-none focus:border-[#bc9c75]/40 text-sm font-semibold">
                                <option value="">-- Chọn danh mục --</option>
                                @foreach ($categoryOptions ?? [] as $categoryOption)
                                    <option value="{{ $categoryOption->id }}" @selected((string) $categoryId === (string) $categoryOption->id)>
                                        {{ $categoryOption->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-xs text-red-500 ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div data-banner-target-panel="collection" class="space-y-2 hidden">
                            <label class="text-[10px] uppercase tracking-[0.2em] font-black text-gray-400 ml-1">Chọn bộ
                                sưu tập</label>
                            <select name="collection_id"
                                class="w-full px-4 py-3.5 rounded-2xl border border-gray-200 bg-white outline-none focus:border-[#bc9c75]/40 text-sm font-semibold">
                                <option value="">-- Chọn bộ sưu tập --</option>
                                @foreach ($collectionOptions ?? [] as $collectionOption)
                                    <option value="{{ $collectionOption->id }}" @selected((string) $collectionId === (string) $collectionOption->id)>
                                        {{ $collectionOption->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('collection_id')
                                <p class="text-xs text-red-500 ml-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] uppercase tracking-[0.2em] font-black text-gray-400 ml-1">Bắt
                                đầu</label>
                            <input type="date" name="start_date" value="{{ $startDate }}"
                                class="w-full px-4 py-3.5 rounded-2xl border border-gray-200 bg-white outline-none focus:border-[#bc9c75]/40 text-sm font-semibold">
                            @error('start_date')
                                <p class="text-xs text-red-500 ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] uppercase tracking-[0.2em] font-black text-gray-400 ml-1">Kết
                                thúc</label>
                            <input type="date" name="end_date" value="{{ $endDate }}"
                                class="w-full px-4 py-3.5 rounded-2xl border border-gray-200 bg-white outline-none focus:border-[#bc9c75]/40 text-sm font-semibold">
                            @error('end_date')
                                <p class="text-xs text-red-500 ml-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <label class="flex items-center gap-3 cursor-pointer select-none pt-1">
                        <input type="checkbox" name="is_active" value="1" @checked($isActive)
                            class="w-4 h-4 rounded border-gray-300 text-[#bc9c75] focus:ring-[#bc9c75]">
                        <span class="text-sm font-semibold text-gray-700">Kích hoạt banner</span>
                    </label>
                </div>
            </div>

            <div class="lg:col-span-5 space-y-6">
                <div class="rounded-[28px] bg-gray-50/80 border border-gray-100 p-6 space-y-5">
                    <div class="flex items-center gap-3">
                        <span
                            class="w-9 h-9 rounded-xl bg-[#bc9c75] text-white flex items-center justify-center font-black text-xs">2</span>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Hình banner</h3>
                            <p class="text-sm text-gray-500">Tải ảnh lên để hiển thị trên slider trang chủ.</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase tracking-[0.2em] font-black text-gray-400 ml-1">Ảnh
                            banner</label>
                        <label class="block">
                            <input type="file" name="image_url" accept="image/jpeg,image/png,image/webp"
                                class="hidden" id="banner-image-input">
                            <div
                                class="rounded-[28px] border-2 border-dashed border-gray-200 bg-white p-4 cursor-pointer hover:border-[#bc9c75]/40 transition">
                                <div
                                    class="aspect-16/10 rounded-3xl overflow-hidden bg-gray-100 flex items-center justify-center">
                                    <img id="banner-image-preview"
                                        src="{{ $bannerPreview ?? 'https://placehold.co/1200x750/f3f4f6/9ca3af?text=Banner+preview' }}"
                                        alt="Preview banner" class="w-full h-full object-cover">
                                </div>
                                <div class="mt-4 text-center">
                                    <div class="text-sm font-semibold text-gray-700">Nhấn để chọn hình ảnh</div>
                                    <div class="text-xs text-gray-400 mt-1">Hỗ trợ JPG, JPEG, PNG, WEBP. Khuyến nghị
                                        1600x700, tối đa 2MB.
                                    </div>
                                </div>
                            </div>
                        </label>
                        @error('image_url')
                            <p class="text-xs text-red-500 ml-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="rounded-[28px] border border-gray-100 bg-white p-6 shadow-lg shadow-gray-100/60">
                    <h4 class="text-sm font-bold text-gray-800 uppercase tracking-[0.2em]">Ghi chú</h4>
                    <p class="mt-3 text-sm text-gray-500 leading-7">
                        Banner được kích hoạt dựa trên <span class="font-semibold text-gray-800">trạng thái hoạt
                            động</span> và
                        <span class="font-semibold text-gray-800">lịch trình thời gian</span> mà bạn đã đặt.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-6 border-t border-gray-100">
            <a href="{{ route('admin.banner-manager') }}"
                class="px-6 py-3 rounded-2xl bg-gray-100 text-gray-600 font-bold uppercase text-[11px] tracking-[0.2em] hover:bg-gray-200 transition">
                Hủy bỏ
            </a>
            <button type="submit"
                class="px-8 py-3 rounded-2xl bg-[#bc9c75] text-white font-black uppercase text-[11px] tracking-[0.2em] shadow-lg shadow-[#bc9c75]/20 hover:scale-[1.02] active:scale-95 transition-all">
                {{ $isEdit ? 'Cập nhật banner' : 'Tạo banner' }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
    <script>
        (function() {
            const input = document.getElementById('banner-image-input');
            const preview = document.getElementById('banner-image-preview');
            const bannerTypeSelect = document.getElementById('banner-type-select');
            const targetPanels = document.querySelectorAll('[data-banner-target-panel]');

            if (!input || !preview) {
                return;
            }

            function syncTargetPanels() {
                const selectedType = bannerTypeSelect ? bannerTypeSelect.value : 'category';

                targetPanels.forEach(function(panel) {
                    const isActive = panel.getAttribute('data-banner-target-panel') === selectedType;
                    panel.classList.toggle('hidden', !isActive);

                    panel.querySelectorAll('input, select, textarea').forEach(function(field) {
                        field.disabled = !isActive;
                    });
                });
            }

            if (bannerTypeSelect) {
                bannerTypeSelect.addEventListener('change', syncTargetPanels);
                syncTargetPanels();
            }

            input.addEventListener('change', function(event) {
                const file = event.target.files && event.target.files[0];

                if (!file) {
                    return;
                }

                preview.src = URL.createObjectURL(file);
            });
        })();
    </script>
@endpush
