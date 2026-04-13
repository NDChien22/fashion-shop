@extends('layouts.user-static-layout')
@section('title', 'Giới thiệu')

@section('main-content')
    <div class="animate-fadeIn pb-20 font-sans text-gray-800">
        <div class="text-center py-12">
            <p class="text-[10px] tracking-[0.3em] text-[#c5a059] uppercase font-bold mb-3">Hệ thống cửa hàng Fast Fashion
            </p>
            <h1 class="text-3xl md:text-5xl font-bold tracking-tight text-gray-900">Tỏa Sáng Phong Cách, Tự Tin Mỗi Ngày!
            </h1>
        </div>

        <section class="max-w-6xl mx-auto px-4 mb-16">
            <div
                class="relative rounded-[2.5rem] overflow-hidden shadow-2xl border border-gray-100 flex flex-col md:flex-row bg-white">
                <div class="md:w-3/5 w-full h-[350px] md:h-[500px]">
                    <img src="https://images.unsplash.com/photo-1525507119028-ed4c629a60a3?q=80&w=2000"
                        class="w-full h-full object-cover" alt="Fast Fashion Unisex Collection">
                </div>
                <div class="md:w-2/5 w-full p-8 md:p-12 flex flex-col justify-center bg-[#fdf0ed]/60">
                    <div class="text-left">
                        <h2 class="text-[#c5a059] text-xl font-serif italic mb-2">Bộ sưu tập</h2>
                        <h3
                            class="text-5xl md:text-6xl font-black mb-6 tracking-tighter text-gray-900 leading-none uppercase">
                            Chào Hè</h3>
                        <p class="text-gray-600 text-sm mb-8 leading-relaxed italic border-l-2 border-[#c5a059] pl-4">
                            Ra mắt Bộ Sưu Tập thời trang Nam & Nữ đa dạng - <br>
                            Mẫu mã dẫn đầu xu hướng - Đủ màu sắc & kích cỡ - <br>
                            Giúp bạn thoải mái lựa chọn phong cách riêng!
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="max-w-4xl mx-auto text-center px-6 mb-20">
            <p class="text-gray-700 leading-relaxed text-xl font-medium px-4">
                Chào mừng bạn đến với <span class="text-[#c5a059] font-bold">Fast Fashion</span>, nơi mang đến cho bạn
                những bộ trang phục thời thượng và phong cách nhất dành cho <span class="text-[#c5a059] font-bold">Nam
                    và Nữ</span>! Với sứ mệnh giúp mọi người tỏa sáng và tự tin hơn trong từng khoảnh khắc, chúng tôi
                luôn cập nhật những xu hướng mới nhất từ các sàn diễn quốc tế.
            </p>
        </section>

        <section class="max-w-6xl mx-auto px-6 mb-24">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-[#fdf0ed] p-8 rounded-2xl text-center shadow-sm border border-[#fce4de]">
                    <span class="text-[#c5a059] text-3xl font-black block mb-1">2</span>
                    <p class="text-[11px] text-gray-500 uppercase font-bold tracking-wider">Năm kinh nghiệm</p>
                </div>
                <div class="bg-[#fdf0ed] p-8 rounded-2xl text-center shadow-sm border border-[#fce4de]">
                    <span class="text-[#c5a059] text-3xl font-black block mb-1">200</span>
                    <p class="text-[11px] text-gray-500 uppercase font-bold tracking-wider">Nhân viên</p>
                </div>
                <div class="bg-[#fdf0ed] p-8 rounded-2xl text-center shadow-sm border border-[#fce4de]">
                    <span class="text-[#c5a059] text-3xl font-black block mb-1">3000+</span>
                    <p class="text-[11px] text-gray-400 uppercase font-bold tracking-wider">Khách hàng</p>
                </div>
                <div class="bg-[#fdf0ed] p-8 rounded-2xl text-center shadow-sm border border-[#fce4de]">
                    <span class="text-[#c5a059] text-3xl font-black block mb-1">8</span>
                    <p class="text-[11px] text-gray-400 uppercase font-bold tracking-wider">Cửa hàng</p>
                </div>
            </div>
        </section>

        <section
            class="max-w-6xl mx-auto px-6 mb-32 grid md:grid-cols-2 gap-16 items-center bg-[#fdf0ed]/40 rounded-[3rem] p-8 md:p-12">
            <div class="relative">
                <div class="absolute -inset-4 bg-[#ff4d24]/10 rounded-4xl -rotate-3"></div>
                <img src="https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?q=80&w=2070"
                    class="relative z-10 rounded-4xl shadow-xl h-[400px] w-full object-cover border-4 border-white"
                    alt="Store Front">
            </div>
            <div class="space-y-6">
                <span class="text-[#c5a059] font-black text-xs uppercase tracking-widest block">Câu chuyện thương
                    hiệu</span>
                <h3 class="text-3xl font-bold text-gray-900 uppercase tracking-tighter italic">Hệ Thống Fast Fashion</h3>
                <p class="text-gray-600 text-base leading-loose">
                    Tại <b>Fast Fashion</b>, bạn sẽ tìm thấy một bộ sưu tập đa dạng từ trang phục Unisex năng động, áo sơ
                    mi thanh lịch đến các mẫu quần thời trang hiện đại. Chúng tôi chú trọng đến chất lượng sản phẩm, đảm
                    bảo mỗi món đồ đều mang lại sự thoải mái và vẻ đẹp bền vững cho cả Nam và Nữ.
                </p>
            </div>
        </section>

        <section class="max-w-6xl mx-auto px-6 grid md:grid-cols-2 gap-16 items-center pb-20">
            <div class="space-y-10">
                <div>
                    <span class="text-gray-400 text-[10px] font-bold uppercase tracking-[0.3em] mb-3 block">Lý do chọn
                        chúng tôi</span>
                    <h3 class="text-4xl font-bold text-gray-900 tracking-tighter uppercase">Tại sao chọn <span
                            class="text-[#c5a059]">Fast Fashion?</span></h3>
                </div>
                <div class="grid grid-cols-2 gap-10">
                    <div class="border-b border-gray-100 pb-4">
                        <h4 class="font-black text-2xl text-gray-900 mb-1">98%</h4>
                        <p class="text-xs text-gray-500 uppercase font-bold">Sự hài lòng</p>
                    </div>
                    <div class="border-b border-gray-100 pb-4">
                        <h4 class="font-black text-2xl text-gray-900 mb-1">1000+</h4>
                        <p class="text-xs text-gray-500 uppercase font-bold">Người dùng hàng ngày</p>
                    </div>
                    <div class="border-b border-gray-100 pb-4">
                        <h4 class="font-black text-2xl text-gray-900 mb-1">FREE+</h4>
                        <p class="text-xs text-gray-500 uppercase font-bold">Giao hàng miễn phí</p>
                    </div>
                    <div class="border-b border-gray-100 pb-4">
                        <h4 class="font-black text-2xl text-gray-900 mb-1">24/7</h4>
                        <p class="text-xs text-gray-500 uppercase font-bold">Hỗ trợ tận tâm</p>
                    </div>
                </div>
            </div>
            <div class="rounded-[2.5rem] overflow-hidden shadow-2xl h-[500px]">
                <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800"
                    class="w-full h-full object-cover hover:scale-110 transition-transform duration-700"
                    alt="Fashion Design">
            </div>
        </section>
    </div>
@endsection
