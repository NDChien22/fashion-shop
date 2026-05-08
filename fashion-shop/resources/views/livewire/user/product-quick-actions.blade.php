<div class="flex items-center gap-2" wire:key="product-quick-actions-{{ $productId }}">
    <button type="button" wire:click.stop.prevent="openSkuPicker" wire:loading.attr="disabled" wire:target="openSkuPicker"
        class="flex-1 h-9 bg-[#111111] text-white text-[11px] font-bold uppercase rounded-xl border border-[#111111] hover:bg-[#bc9c75] hover:border-[#bc9c75] active:scale-[0.98] transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-[#bc9c75]/40 shadow-sm">
        <span wire:loading.remove wire:target="openSkuPicker">Thêm vào giỏ</span>
        <span wire:loading wire:target="openSkuPicker">Đang mở...</span>
    </button>

    <button type="button" wire:click.stop.prevent="toggleWishlist" wire:loading.attr="disabled"
        wire:target="toggleWishlist"
        class="h-9 w-9 rounded-xl bg-white border border-[#e7ddcf] {{ $wishlisted ? 'text-red-500' : 'text-gray-700' }} hover:bg-[#bc9c75] hover:border-[#bc9c75] hover:text-white active:scale-[0.98] transition-all duration-200 flex items-center justify-center disabled:opacity-70 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-[#bc9c75]/40 shadow-sm"
        aria-label="Yêu thích sản phẩm">
        <i class="{{ $wishlisted ? 'ri-heart-fill' : 'ri-heart-line' }} text-base"></i>
    </button>
</div>
