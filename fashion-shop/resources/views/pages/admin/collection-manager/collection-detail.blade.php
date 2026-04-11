@extends('layouts.admin-layout')
@section('title', 'Thông tin bộ sưu tập')

@section('page-header')
    <h1 class="text-xl font-semibold text-gray-800">Bộ sưu tập</h1>

    <p class="text-xs text-gray-400 mt-1">
        <span class="cursor-pointer hover:text-[#bc9c75] transition">Trang chính</span> /
        <span class="cursor-pointer hover:text-[#bc9c75] transition">Bộ sưu tập</span> /
        <span class="text-[#bc9c75] font-medium">{{ $collection->name }}</span>
    </p>
@endsection

@section('content')

    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 lg:p-5">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="space-y-2">
                    <a href="{{ route('admin.product-collections') }}"
                        class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-all">
                        <i class="fa-solid fa-arrow-left"></i>
                        Quay lại danh sách
                    </a>
                    <h2 class="text-2xl font-semibold text-gray-800">{{ $collection->name }}</h2>
                    <div class="flex flex-wrap gap-2">
                        <span
                            class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $collection->is_active ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-gray-100 text-gray-500 border border-gray-200' }}">
                            {{ $collection->is_active ? 'Đang hoạt động' : 'Đã tắt' }}
                        </span>
                        <span
                            class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-blue-50 text-blue-600 border border-blue-100">
                            {{ $products->total() }} sản phẩm
                        </span>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.edit-collection', $collection->slug) }}"
                        class="inline-flex items-center gap-2 bg-blue-50 text-blue-600 px-4 py-2.5 rounded-xl font-semibold hover:bg-blue-100 transition-all">
                        <i class="fa-regular fa-pen-to-square"></i>
                        Chỉnh sửa
                    </a>
                    <button type="button" id="openProductSelectionModal"
                        class="inline-flex items-center gap-2 bg-[#bc9c75] text-white px-4 py-2.5 rounded-xl font-semibold hover:brightness-95 transition-all">
                        <i class="fa-solid fa-plus"></i>
                        Thêm sản phẩm
                    </button>
                </div>
            </div>
        </div>

        @if ($collection->thumbnail_url || $collection->description)
            <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
                @if ($collection->thumbnail_url)
                    <div class="xl:col-span-3 bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                        <img src="{{ asset('storage/' . $collection->thumbnail_url) }}" alt="{{ $collection->name }}"
                            class="w-full h-72 object-cover">
                    </div>
                @endif

                <div
                    class="{{ $collection->thumbnail_url ? 'xl:col-span-2' : 'xl:col-span-5' }} bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-xs uppercase tracking-wider font-bold text-gray-400 mb-3">Mô tả</h3>
                    <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">
                        {{ $collection->description ?: 'Bộ sưu tập hiện chưa có mô tả.' }}</p>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700">Sản phẩm trong bộ sưu tập</h3>
                <span class="text-xs font-semibold text-gray-400">Tổng: {{ $products->total() }}</span>
            </div>

            @if ($products->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach ($products as $product)
                        @php
                            $resolveImageUrl = function (?string $path) {
                                if (!$path) {
                                    return null;
                                }

                                $trimmedPath = trim($path);
                                if (
                                    str_starts_with($trimmedPath, 'http://') ||
                                    str_starts_with($trimmedPath, 'https://')
                                ) {
                                    return $trimmedPath;
                                }

                                return asset(ltrim($trimmedPath, '/'));
                            };

                            $mainImage = $resolveImageUrl($product->main_image_url) ?: '/asset/img/product-1.jpg';
                            $galleryImages = collect(
                                is_array($product->gallery_image_urls ?? null) ? $product->gallery_image_urls : [],
                            )
                                ->filter(fn($path) => is_string($path) && trim($path) !== '')
                                ->map(fn($path) => $resolveImageUrl($path))
                                ->filter()
                                ->values()
                                ->all();
                            $previewImages = collect([$mainImage])
                                ->merge($galleryImages)
                                ->unique()
                                ->values()
                                ->all();
                            $skuRows = collect($product->skus ?? [])
                                ->map(
                                    fn($sku) => [
                                        'size' => $sku->size ?: '-',
                                        'color' => $sku->color ?: '-',
                                        'stock' => (int) ($sku->stock ?? 0),
                                    ],
                                )
                                ->values()
                                ->all();
                        @endphp
                        <article
                            class="collection-product-row p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 hover:bg-gray-50 transition-colors cursor-pointer"
                            role="button" tabindex="0" data-name="{{ $product->name }}"
                            data-code="{{ $product->product_code }}" data-sku="{{ $product->skus_count ?? 0 }}"
                            data-status="{{ $product->is_active ? 'Đang bán' : 'Ẩn' }}" data-image="{{ $mainImage }}"
                            data-edit-url="{{ route('admin.edit-product', $product->slug) }}"
                            data-description="{{ e($product->description ?: 'Chưa có mô tả sản phẩm.') }}"
                            data-category="{{ $product->category?->name ?: '-' }}"
                            data-collection="{{ $product->collection?->name ?: 'Chưa có bộ sưu tập' }}"
                            data-price="{{ number_format((float) ($product->base_price ?? 0), 0, ',', '.') }}đ"
                            data-updated="{{ $product->updated_at?->diffForHumans() ?: '-' }}"
                            data-remove-url="{{ route('admin.remove-product-from-collection', $collection->slug) }}"
                            data-product-id="{{ $product->id }}">
                            <div class="flex items-center gap-4 min-w-0">
                                <div
                                    class="w-20 h-24 bg-gray-100 rounded-2xl border border-gray-100 overflow-hidden shrink-0 flex items-center justify-center">
                                    @if ($product->main_image_url)
                                        <img src="{{ asset($product->main_image_url) }}" alt="{{ $product->name }}"
                                            class="w-full h-full object-cover"
                                            onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                    @endif
                                    <div
                                        class="{{ $product->main_image_url ? 'hidden' : '' }} w-full h-full flex items-center justify-center text-gray-300">
                                        <i class="fa-solid fa-image text-2xl"></i>
                                    </div>
                                </div>

                                <div class="min-w-0">
                                    <h4 class="font-semibold text-gray-800 truncate">{{ $product->name }}</h4>
                                    <p class="text-sm text-gray-400 mt-0.5">Mã: <span
                                            class="text-[#bc9c75]">{{ $product->product_code }}</span></p>
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        <span
                                            class="text-[10px] px-2 py-1 rounded-full font-semibold uppercase tracking-wide {{ $product->is_active ? 'bg-green-50 text-green-600' : 'bg-gray-100 text-gray-500' }}">
                                            {{ $product->is_active ? 'Đang bán' : 'Ẩn' }}
                                        </span>
                                        <span
                                            class="text-[10px] px-2 py-1 rounded-full font-semibold uppercase tracking-wide bg-blue-50 text-blue-600">
                                            {{ $product->skus_count ?? 0 }} SKU
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 self-end sm:self-auto">
                                <a href="{{ route('admin.edit-product', $product->slug) }}"
                                    class="w-9 h-9 rounded-lg flex items-center justify-center text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('admin.remove-product-from-collection', $collection->slug) }}"
                                    method="POST" data-confirm-delete="1"
                                    data-confirm-message="Bạn chắc chắn muốn xóa sản phẩm này khỏi bộ sưu tập?">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <button type="submit"
                                        class="w-9 h-9 rounded-lg flex items-center justify-center text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all border-0 bg-transparent cursor-pointer">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if ($products->hasPages())
                    <div class="p-6 border-t border-gray-100 flex justify-center">
                        {{ $products->links() }}
                    </div>
                @endif
            @else
                <div class="p-12 text-center">
                    <i class="fa-solid fa-inbox text-6xl text-gray-200 mb-4"></i>
                    <p class="text-gray-500 font-semibold">Chưa có sản phẩm nào trong bộ sưu tập này</p>
                </div>
            @endif
        </div>
    </div>

    <div id="productSelectionModal" class="hidden fixed inset-0 bg-black/50 z-50 p-4 sm:p-6">
        <div class="h-full w-full flex items-center justify-center">
            <div
                class="bg-white rounded-3xl w-full max-w-3xl max-h-[88vh] overflow-hidden shadow-2xl border border-gray-100">
                <div class="sticky top-0 bg-white p-6 border-b border-gray-100 flex justify-between items-center z-10">
                    <h3 class="font-semibold text-lg text-gray-800">Chọn sản phẩm để thêm vào bộ sưu tập</h3>
                    <button type="button" id="closeProductSelectionModal"
                        class="w-9 h-9 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 border-0 bg-transparent cursor-pointer">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                @if ($availableProducts->count() > 0)
                    <form action="{{ route('admin.add-products-to-collection', $collection->slug) }}" method="POST"
                        class="h-[calc(88vh-88px)] flex flex-col">
                        @csrf

                        <div class="p-5 border-b border-gray-100">
                            <input type="text" id="product-search" placeholder="Tìm theo tên sản phẩm..."
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#bc9c75] focus:ring-4 focus:ring-[#bc9c75]/5 outline-none">
                        </div>

                        <div id="product-list" class="flex-1 overflow-y-auto p-4 space-y-2">
                            @foreach ($availableProducts as $product)
                                <label
                                    class="collection-product-item flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 cursor-pointer border border-transparent hover:border-gray-100"
                                    data-product-name="{{ strtolower($product->name) }}">
                                    <input type="checkbox" name="product_ids[]" value="{{ $product->id }}"
                                        class="w-4 h-4 rounded">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-700 truncate">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-400">Mã: {{ $product->product_code }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div class="p-5 border-t border-gray-100 flex justify-end gap-3">
                            <button type="button" id="cancelProductSelectionModal"
                                class="px-6 py-2.5 rounded-xl font-semibold text-gray-500 hover:text-gray-700 hover:bg-gray-100 border-0 bg-transparent cursor-pointer">
                                Hủy
                            </button>
                            <button type="submit"
                                class="bg-[#bc9c75] text-white px-6 py-2.5 rounded-xl font-semibold hover:brightness-95 border-0 cursor-pointer">
                                Thêm đã chọn
                            </button>
                        </div>
                    </form>
                @else
                    <div class="p-12 text-center">
                        <p class="text-gray-500 font-semibold mb-5">Không có sản phẩm để thêm.</p>
                        <button type="button" id="fallbackCloseProductSelectionModal"
                            class="px-6 py-2.5 rounded-xl font-semibold text-gray-500 hover:text-gray-700 hover:bg-gray-100 border-0 bg-transparent cursor-pointer">
                            Đóng
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-admin.product-detail-modal id="collectionProductDetailModal" title="Chi tiết sản phẩm" maxWidth="max-w-4xl"
        contentClass="grid grid-cols-1 md:grid-cols-2">
        <x-admin.product-detail-content mode="static" prefix="collectionPreview" :showRemove="true"
            removeButtonText="Xóa" removeConfirmMessage="Bạn chắc chắn muốn xóa sản phẩm này khỏi bộ sưu tập?" />
    </x-admin.product-detail-modal>

    @php
        $productModalDataMap = $products
            ->getCollection()
            ->mapWithKeys(function ($product) {
                $resolveImageUrl = function (?string $path) {
                    if (!$path) {
                        return null;
                    }

                    $trimmedPath = trim($path);
                    if (str_starts_with($trimmedPath, 'http://') || str_starts_with($trimmedPath, 'https://')) {
                        return $trimmedPath;
                    }

                    return asset(ltrim($trimmedPath, '/'));
                };

                $mainImage = $resolveImageUrl($product->main_image_url) ?: '/asset/img/product-1.jpg';
                $galleryImages = collect(
                    is_array($product->gallery_image_urls ?? null) ? $product->gallery_image_urls : [],
                )
                    ->filter(fn($path) => is_string($path) && trim($path) !== '')
                    ->map(fn($path) => $resolveImageUrl($path))
                    ->filter()
                    ->values()
                    ->all();

                $previewImages = collect([$mainImage])
                    ->merge($galleryImages)
                    ->unique()
                    ->values()
                    ->all();
                $skuRows = collect($product->skus ?? [])
                    ->map(
                        fn($sku) => [
                            'size' => $sku->size ?: '-',
                            'color' => $sku->color ?: '-',
                            'stock' => (int) ($sku->stock ?? 0),
                        ],
                    )
                    ->values()
                    ->all();

                return [
                    $product->id => [
                        'previewImages' => $previewImages,
                        'skus' => $skuRows,
                    ],
                ];
            })
            ->all();
    @endphp

    <script>
        const productModalDataMap = @json($productModalDataMap);

        const modal = document.getElementById('productSelectionModal');
        const openModalButton = document.getElementById('openProductSelectionModal');
        const closeModalButton = document.getElementById('closeProductSelectionModal');
        const cancelModalButton = document.getElementById('cancelProductSelectionModal');
        const fallbackCloseButton = document.getElementById('fallbackCloseProductSelectionModal');

        const openModal = () => {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        };

        const closeModal = () => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };

        if (openModalButton) openModalButton.addEventListener('click', openModal);
        if (closeModalButton) closeModalButton.addEventListener('click', closeModal);
        if (cancelModalButton) cancelModalButton.addEventListener('click', closeModal);
        if (fallbackCloseButton) fallbackCloseButton.addEventListener('click', closeModal);

        modal.addEventListener('click', function(event) {
            if (event.target === modal) closeModal();
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
        });

        const searchInput = document.getElementById('product-search');
        if (searchInput) {
            searchInput.addEventListener('input', function(event) {
                const keyword = event.target.value.trim().toLowerCase();
                const items = document.querySelectorAll('.collection-product-item');

                items.forEach(function(item) {
                    const productName = item.dataset.productName || '';
                    item.style.display = productName.includes(keyword) ? 'flex' : 'none';
                });
            });
        }

        const detailModal = document.getElementById('collectionProductDetailModal');
        const detailImage = document.getElementById('collectionPreviewImage');
        const detailFallback = document.getElementById('collectionPreviewFallback');
        const detailName = document.getElementById('collectionPreviewName');
        const detailCode = document.getElementById('collectionPreviewCode');
        const detailDescription = document.getElementById('collectionPreviewDescription');
        const detailStatus = document.getElementById('collectionPreviewStatusLabel');
        const detailDot = document.getElementById('collectionPreviewDot');
        const detailUpdated = document.getElementById('collectionPreviewUpdated');
        const detailCategory = document.getElementById('collectionPreviewCategory');
        const detailCollection = document.getElementById('collectionPreviewCollection');
        const detailPrice = document.getElementById('collectionPreviewPrice');
        const detailSkus = document.getElementById('collectionPreviewSkus');
        const detailThumbs = document.getElementById('collectionPreviewThumbs');
        const detailPrevBtn = document.getElementById('collectionPreviewPrevBtn');
        const detailNextBtn = document.getElementById('collectionPreviewNextBtn');
        const detailEditLink = document.getElementById('collectionPreviewEditLink');
        const detailRemoveForm = document.getElementById('collectionPreviewRemoveForm');
        const detailRemoveProductId = document.getElementById('collectionPreviewRemoveProductId');

        let detailImages = [];
        let detailCurrentImageIndex = 0;

        const renderDetailImage = () => {
            const imageUrl = detailImages[detailCurrentImageIndex] || '';

            if (imageUrl) {
                detailImage.src = imageUrl;
                detailImage.classList.remove('hidden');
                detailFallback.classList.add('hidden');
            } else {
                detailImage.src = '';
                detailImage.classList.add('hidden');
                detailFallback.classList.remove('hidden');
            }

            if (detailImages.length > 1) {
                detailPrevBtn.classList.remove('hidden');
                detailNextBtn.classList.remove('hidden');
            } else {
                detailPrevBtn.classList.add('hidden');
                detailNextBtn.classList.add('hidden');
            }

            detailThumbs.innerHTML = '';
            detailImages.forEach((image, index) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'shrink-0 rounded-lg overflow-hidden border-2 ' +
                    (index === detailCurrentImageIndex ? 'border-[#bc9c75]' : 'border-gray-200');

                const img = document.createElement('img');
                img.src = image;
                img.alt = 'Preview';
                img.className = 'w-16 h-16 object-cover ' +
                    (index === detailCurrentImageIndex ? 'opacity-100' : 'opacity-60 hover:opacity-90');

                button.appendChild(img);
                button.addEventListener('click', function() {
                    detailCurrentImageIndex = index;
                    renderDetailImage();
                });
                detailThumbs.appendChild(button);
            });
        };

        detailPrevBtn.addEventListener('click', function() {
            if (!detailImages.length) return;
            detailCurrentImageIndex = detailCurrentImageIndex > 0 ? detailCurrentImageIndex - 1 : detailImages
                .length - 1;
            renderDetailImage();
        });

        detailNextBtn.addEventListener('click', function() {
            if (!detailImages.length) return;
            detailCurrentImageIndex = detailCurrentImageIndex < detailImages.length - 1 ? detailCurrentImageIndex +
                1 : 0;
            renderDetailImage();
        });

        const openDetailModal = (row) => {
            detailName.textContent = row.dataset.name || '';
            detailCode.textContent = 'Mã: ' + (row.dataset.code || '-');
            detailDescription.textContent = row.dataset.description || 'Chưa có mô tả sản phẩm.';
            detailCategory.textContent = row.dataset.category || '-';
            detailCollection.textContent = row.dataset.collection || 'Chưa có bộ sưu tập';
            detailPrice.textContent = row.dataset.price || '0đ';
            detailUpdated.textContent = 'Cập nhật cuối: ' + (row.dataset.updated || '-');
            detailEditLink.href = row.dataset.editUrl || '#';
            detailRemoveForm.action = row.dataset.removeUrl || '#';
            detailRemoveProductId.value = row.dataset.productId || '';

            const isActive = (row.dataset.status || '') === 'Đang bán';
            detailStatus.textContent = isActive ? 'Đang hiển thị trên Web' : 'Ngừng hiển thị';
            detailDot.className = 'w-2 h-2 rounded-full ' +
                (isActive ? 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]' :
                    'bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.5)]');

            const productId = row.dataset.productId || '';
            const modalData = productModalDataMap[productId] || {
                previewImages: [],
                skus: []
            };

            const parsedImages = Array.isArray(modalData.previewImages) ? modalData.previewImages : [];
            detailImages = Array.isArray(parsedImages) && parsedImages.length ? parsedImages : [row.dataset.image ||
                ''
            ];
            detailCurrentImageIndex = 0;
            renderDetailImage();

            const parsedSkus = Array.isArray(modalData.skus) ? modalData.skus : [];

            detailSkus.innerHTML = '';
            if (Array.isArray(parsedSkus) && parsedSkus.length) {
                parsedSkus.forEach((sku) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="px-4 py-2 font-bold italic">${sku.size ?? '-'}</td>
                        <td class="px-4 py-2 text-gray-600">${sku.color ?? '-'}</td>
                        <td class="px-4 py-2 text-right font-bold text-gray-800">${String(sku.stock ?? 0).padStart(2, '0')}</td>
                    `;
                    detailSkus.appendChild(tr);
                });
            } else {
                const tr = document.createElement('tr');
                tr.innerHTML = '<td colspan="3" class="px-4 py-3 text-center text-gray-400">Chưa có biến thể.</td>';
                detailSkus.appendChild(tr);
            }

            detailModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        };

        const closeDetailModal = () => {
            detailModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };

        document.querySelectorAll('.collection-product-row').forEach((row) => {
            row.addEventListener('click', function(event) {
                if (event.target.closest('a, button, form, input, label')) return;
                openDetailModal(row);
            });

            row.addEventListener('keydown', function(event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    openDetailModal(row);
                }
            });
        });

        const detailHeaderClose = detailModal.querySelector('[data-modal-close="collectionProductDetailModal"]');
        if (detailHeaderClose) {
            detailHeaderClose.addEventListener('click', closeDetailModal);
        }

        detailModal.addEventListener('click', function(event) {
            if (event.target === detailModal) closeDetailModal();
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && !detailModal.classList.contains('hidden')) {
                closeDetailModal();
            }
        });
    </script>

@endsection
