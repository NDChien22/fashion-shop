<div class="min-h-screen bg-gray-50">
    @include('pages.user.home.components.banner-slider', ['banners' => $banners])
    @include('pages.user.home.components.service-highlights')
    <livewire:user.voucher-offers :key="'voucher-offers-home-page'" />
    @include('pages.user.home.components.featured-collections', [
        'featuredCollections' => $featuredCollections,
    ])
    @include('pages.user.home.components.flash-sale-products', [
        'activeFlashSales' => $activeFlashSales,
        'flashSaleProducts' => $flashSaleProducts,
    ])
    @include('pages.user.home.components.best-seller-products', [
        'bestSellerProducts' => $bestSellerProducts,
    ])
</div>
