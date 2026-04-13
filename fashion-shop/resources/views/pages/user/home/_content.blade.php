@include('pages.user.home.components.banner-slider', ['banners' => $banners ?? collect()])
@include('pages.user.home.components.service-highlights')
@livewire('user.voucher-offers')
@include('pages.user.home.components.featured-collections', [
    'featuredCollections' => $featuredCollections ?? collect(),
])
@include('pages.user.home.components.flash-sale-products', [
    'activeFlashSales' => $activeFlashSales ?? collect(),
    'flashSaleProducts' => $flashSaleProducts ?? collect(),
])
@include('pages.user.home.components.best-seller-products', [
    'bestSellerProducts' => $bestSellerProducts ?? collect(),
])
