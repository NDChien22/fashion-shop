<div class="fixed bottom-6 right-6 z-[110]">
    <button id="back-to-top"
        class="fixed bottom-24 right-6 w-12 h-12 bg-white text-[#bc9c75] rounded-xl flex items-center justify-center shadow-lg border border-red-50 z-[100] transition-all duration-300 opacity-0 invisible translate-y-10 hover:bg-red-50">
        <i class="ri-arrow-up-line text-2xl font-bold"></i>
    </button>
    <button id="chat-toggle-btn"
        class="w-14 h-14 bg-[#bc9c75] rounded-full flex items-center justify-center shadow-lg transition-transform hover:scale-110 active:scale-95 relative">
        <i id="chat-icon" class="ri-messenger-fill text-white text-3xl"></i>
        <span class="absolute top-0 right-0 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></span>
    </button>

    <div id="support-box"
        class="absolute bottom-16 right-0 w-80 md:w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden hidden flex-col transition-all duration-300">
        <div class="bg-[#bc9c75] p-4 flex justify-between items-center text-white">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=fff&color=bc9c75"
                        class="w-10 h-10 rounded-full border-2 border-white/50" alt="Avatar">
                    <span
                        class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-[#bc9c75] rounded-full"></span>
                </div>
                <div>
                    <p class="font-bold text-sm">Hỗ trợ Fast Fashion</p>
                    <p class="text-[10px] opacity-80">Thường trả lời trong vài phút</p>
                </div>
            </div>
            <button id="close-chat-btn" class="hover:bg-white/20 rounded-full p-1"><i
                    class="ri-close-line text-xl"></i></button>
        </div>

        <div class="h-80 overflow-y-auto p-4 bg-gray-50 flex flex-col gap-3" id="message-container">
            <div class="flex flex-col gap-1 max-w-[80%]">
                <div class="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm border border-gray-100">
                    <p class="text-sm text-gray-700">Chào bạn! Fast Fashion có thể giúp gì cho bạn hôm nay không ạ?</p>
                </div>
                <span class="text-[9px] text-gray-400 ml-1">11:42 PM</span>
            </div>

            <div class="flex flex-col gap-1 max-w-[80%] self-end items-end">
                <div class="bg-[#bc9c75] p-3 rounded-2xl rounded-tr-none shadow-sm text-white">
                    <p class="text-sm">Mình muốn tư vấn size váy Vintage ạ.</p>
                </div>
                <span class="text-[9px] text-gray-400 mr-1">11:43 PM</span>
            </div>
        </div>

        <div class="p-3 border-t bg-white flex items-center gap-2">
            <button class="text-gray-400 hover:text-[#bc9c75]"><i class="ri-image-add-line text-xl"></i></button>
            <input type="text" placeholder="Nhập tin nhắn..."
                class="flex-1 bg-gray-100 border-none rounded-full px-4 py-2 text-sm focus:ring-1 focus:ring-[#bc9c75] outline-none">
            <button
                class="w-8 h-8 bg-[#bc9c75] text-white rounded-full flex items-center justify-center hover:bg-[#a0805a]">
                <i class="ri-send-plane-2-fill text-sm"></i>
            </button>
        </div>
    </div>
</div>
