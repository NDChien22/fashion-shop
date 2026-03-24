@extends('layouts.admin-layout')
@section('title', 'Hỗ trợ khách hàng')
@section('content')

    <div>
        <div id="support-container" class="flex h-[calc(100vh-140px)] bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden relative">
            
            <div id="side-panel" class="w-full md:w-80 shrink-0 border-r border-gray-50 flex flex-col bg-[#fcfaf8]/50 absolute md:relative z-20 transition-transform duration-300">
                <div class="p-5 bg-white border-b border-gray-50">
                    <h2 class="text-xs font-black text-gray-800 uppercase tracking-widest">Tin nhắn</h2>
                </div>
                <div class="flex-1 overflow-y-auto custom-scrollbar">
                    <div onclick="toggleChat(true)" class="p-4 flex items-center gap-3 bg-white border-l-4 border-[#bc9c75] cursor-pointer hover:bg-gray-50">
                        <div class="w-10 h-10 rounded-xl bg-[#bc9c75]/10 flex items-center justify-center text-[#bc9c75] font-black text-xs">NA</div>
                        <div class="flex-1">
                            <h4 class="text-[11px] font-bold text-gray-800 uppercase">Nguyễn Anh</h4>
                            <p class="text-[10px] text-gray-400 truncate">Voucher này dùng thế nào ạ?</p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="main-chat" class="flex-1 flex flex-col bg-white absolute inset-0 md:relative z-30 translate-x-full md:translate-x-0 transition-transform duration-300">

                <div class="p-4 border-b border-gray-50 bg-white flex justify-between items-center z-10 shadow-sm">
                    <div class="flex items-center gap-3">
                        <button onclick="toggleChat(false)" class="md:hidden w-8 h-8 flex items-center justify-center text-gray-400 hover:bg-gray-100 rounded-full">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        
                        <div class="w-10 h-10 rounded-xl bg-[#bc9c75] text-white flex items-center justify-center font-black text-xs">NA</div>
                        <div>
                            <h4 class="text-[12px] font-black text-gray-800 uppercase">Nguyễn Anh</h4>
                            <span class="text-[9px] text-green-500 font-bold uppercase tracking-tighter animate-pulse">● Đang hoạt động</span>
                        </div>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-6 bg-[#fcfaf8]/30 custom-scrollbar" id="message-container">
                    <div class="flex items-end gap-2 max-w-[90%] md:max-w-[70%]">
                        <div class="bg-white border border-gray-100 p-3 sm:p-3.5 rounded-2xl rounded-bl-none shadow-sm text-[12px] text-gray-700 leading-relaxed">
                            Chào shop, mình muốn hỏi về mã giảm giá SUMMER2026 sao mình áp dụng không được?
                        </div>
                    </div>

                    <div class="flex flex-row-reverse items-end gap-2 ml-auto max-w-[90%] md:max-w-[70%]">
                        <div class="bg-[#bc9c75] p-3 sm:p-3.5 rounded-2xl rounded-br-none text-white text-[12px] shadow-md shadow-[#bc9c75]/20 font-medium">
                            Chào bạn, shop xin lỗi về sự cố này. Bạn vui lòng kiểm tra xem đơn hàng đã đạt 500k chưa ạ?
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-white border-t border-gray-50 shadow-[0_-5px_15px_-5px_rgba(0,0,0,0.05)]">
                    <div class="flex items-center gap-2 sm:gap-3 bg-gray-50 p-1.5 pl-3 sm:pl-4 rounded-2xl border border-gray-100 focus-within:bg-white focus-within:border-[#bc9c75]/30 transition-all">
                        <button class="text-gray-400 hover:text-[#bc9c75] hidden sm:block"><i class="fa-regular fa-face-smile text-lg"></i></button>
                        <input type="text" placeholder="Nhập phản hồi..." class="flex-1 bg-transparent border-none outline-none text-[12px] font-medium py-2 min-w-0">
                        <button class="bg-[#bc9c75] text-white px-4 sm:px-6 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest shadow-md shadow-[#bc9c75]/30 flex items-center gap-2 hover:scale-105 active:scale-95 transition-all">
                            <span class="hidden xs:inline">Gửi</span> <i class="fa-solid fa-paper-plane text-[9px]"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
