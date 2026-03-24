<div>
    <div class="flex items-center justify-between mb-6">
        <div class="flex gap-4 flex-1">
            <div class="relative w-full max-w-[50%]">
                <i
                    class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" placeholder="Tìm kiếm sản phẩm..."
                    class="w-full bg-white border border-gray-100 rounded-xl py-2.5 pl-10 pr-4 text-xs focus:ring-1 focus:ring-[#bc9c75] outline-none shadow-sm">
            </div>



            <select id="prod-cate"
                class="bg-white border border-gray-100 rounded-xl px-4 py-2 text-xs shadow-sm outline-none w-40 text-gray-600">
                <option value="">Danh mục</option>
                <option>...</option>
                <option>...</option>
                <option>...</option>
            </select>

            <select id="prod-cate"
                class="bg-white border border-gray-100 rounded-xl px-4 py-2 text-xs shadow-sm outline-none w-40 text-gray-600">
                <option value="">Bộ sưu tập</option>
                <option>...</option>
                <option>...</option>
                <option>...</option>
            </select>
        </div>

        <button onclick="openForm('product')"
            class="px-5 py-2.5 bg-[#bc9c75] text-white rounded-xl text-xs font-bold shadow-md shadow-[#bc9c75]/20 hover:opacity-90 transition-all flex items-center gap-2">
            <i class="fa-solid fa-plus text-[10px]"></i> Thêm sản phẩm
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 overflow-hidden">
        <table class="w-full text-left text-[12px]">
            <thead
                class="bg-[#fcfaf8] border-b border-gray-50 text-gray-400 font-bold uppercase tracking-wider text-[10px]">
                <tr>
                    <th class="px-6 py-4">Sản phẩm</th>
                    <th class="px-6 py-4 text-center">Danh mục</th>
                    <th class="px-6 py-4 text-center">Giá bán</th>
                    <th class="px-6 py-4 text-center">Tồn kho</th>
                    <th class="px-6 py-4 text-center">Trạng thái</th>
                    <th class="px-6 py-4 text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-gray-600">
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4 flex items-center gap-3">

                        <div>
                            <img src="..." onclick="openModal('productDetailModal')"
                                class="w-10 h-10 rounded-lg cursor-pointer hover:opacity-80">
                        </div>
                        <div>
                            <p class="font-bold text-gray-800">Classic Wool Coat</p>
                            <p class="text-[10px] text-gray-400">Mã: FW2026-001</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">Áo khoác</td>
                    <td class="px-6 py-4 text-center font-semibold">2.450.000đ</td>
                    <td class="px-6 py-4 text-center">12</td>
                    <td class="px-6 py-4 text-center">
                        <span class="bg-green-50 text-green-500 px-2 py-1 rounded-full font-bold text-[10px]">Đang
                            bán</span>
                    </td>
                    <td class="px-6 py-4 text-center space-x-2">
                        <button onclick="openForm('product')" class="text-gray-400 hover:text-[#bc9c75]">
                            <i class="fa-regular fa-pen-to-square"></i>
                        </button>
                        <button class="text-gray-400 hover:text-red-500">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
