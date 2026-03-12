function toggleUserMenu(){
    const menu = document.getElementById("userMenu");
    menu.classList.toggle("hidden");
}

document.addEventListener("click", function(e){
    const menu = document.getElementById("userMenu");
    const userArea = e.target.closest(".relative");

    if(!userArea){
        menu.classList.add("hidden");
    }
});

// 2. Cấu hình các trang
const PAGES = {

    'dashboard': { title: 'Tổng quan' },         

    'product-list': { title: 'Danh sách mẫu', file: 'manager-products.html', tpl: 'tpl-product-list' },
    'add-product': { title: 'Thêm sản phẩm', file: 'manager-products.html', tpl: 'tpl-add-product' },
    'edit-product': { title: 'Sửa sản phẩm', file: 'manager-products.html', tpl: 'tpl-edit-product' },
    'productDetailModal': { title: 'Sửa sản phẩm', file: 'manager-products.html', tpl: 'tpl-productDetailModal' },

    'vouchers': { title: 'Voucher', file: 'manager-products.html', tpl: 'tpl-vouchers' },
    'add-voucher': { title: 'Tạo voucher', file: 'manager-products.html', tpl: 'tpl-add-voucher' },
    'edit-voucher': { title: 'Chỉnh sửa Voucher', file: 'manager-products.html', tpl: 'tpl-edit-voucher' },

    'orders': { title: 'Đơn hàng', file: 'manager-orders.html', tpl: 'tpl-orders' },
    'order-detail': { title: 'Chi tiết đơn hàng', file: 'manager-orders.html', tpl: 'tpl-order-detail' },
    'revenue': { title: 'Doanh thu', file: 'manager-orders.html', tpl: 'tpl-revenue' },

    'customers': { title: 'Khách hàng', file: 'manager-customers-users.html', tpl: 'tpl-customers' },
    'support': { title: 'Hỗ trợ khách hàng', file: 'manager-customers-users.html', tpl: 'tpl-support' },
    'hr': { title: 'Quản lý nhân sự', file: 'manager-customers-users.html', tpl: 'tpl-hr' },
    'user-detail': { title: 'Chi tiết nhân viên', file: 'manager-customers-users.html', tpl: 'tpl-user-detail' },
    'add-employee': { title: 'Thêm nhân viên', file: 'manager-customers-users.html', tpl: 'tpl-add-employee' },
    'edit-employee': { title: 'Sửa nhân viên', file: 'manager-customers-users.html', tpl: 'tpl-edit-employee' },
    'role-management': { title: 'Thêm chức vụ', file: 'manager-customers-users.html', tpl: 'tpl-role-management' },

    'quan-ly': { title: 'Hồ sơ cá nhân', file: 'account-management.html', tpl: 'tpl-quan-ly' },
    'thong-tin': { title: 'Hồ sơ cá nhân', file: 'account-management.html', tpl: 'tpl-tn' },
    'account': { title: 'Cài đặt tài khoản', file: 'account-management.html', tpl: 'tpl-account' },

};

// 3. Hàm tải trang chính (Sửa lỗi logic)
const fileCache = {};

async function loadPage(pageId) {
    const contentArea = document.getElementById("content-area");
    if (!contentArea) return;

    // Nếu pageId không tồn tại trong danh sách PAGES, mặc định về dashboard
    const config = PAGES[pageId] || PAGES['dashboard'];
    const activePageId = PAGES[pageId] ? pageId : 'dashboard';

    contentArea.style.opacity = "0.5"; // Hiệu ứng chờ tải
    contentArea.style.pointerEvents = "none";

    try {
        let template = null;

        if (activePageId === "dashboard") {
            template = document.getElementById("tpl-dashboard");
        } else if (config.file) {
            let doc;
            if (fileCache[config.file]) {
                doc = fileCache[config.file];
            } else {
                const response = await fetch(config.file);
                if (!response.ok) throw new Error("Không thể tải file");
                const htmlText = await response.text();
                doc = new DOMParser().parseFromString(htmlText, "text/html");
                fileCache[config.file] = doc;
            }
            template = doc.getElementById(config.tpl);
        }

        contentArea.innerHTML = "";
        contentArea.scrollTop = 0;

        if (template) {
            const node = (template.tagName === "TEMPLATE") ? template.content.cloneNode(true) : template.cloneNode(true);
            contentArea.appendChild(node);
        } else {
            contentArea.innerHTML = `<div class="p-10 text-center text-gray-400">Trang đang được xây dựng...</div>`;
        }

        // Cập nhật Tiêu đề và Breadcrumb
        const pageTitle = document.getElementById("page-title");
        const breadcrumb = document.getElementById("breadcrumb-current");
        const pageHeader = document.querySelector(".ff-page-header");

        if (pageHeader) pageHeader.style.display = (activePageId === "dashboard") ? "none" : "block";
        if (pageTitle) pageTitle.innerText = config.title;
        if (breadcrumb) breadcrumb.innerHTML = `<span class="text-[#bc9c75] font-medium">${config.title}</span>`;

        updateSidebarUI(activePageId);

    } catch (err) {
        console.error("Lỗi:", err);
        contentArea.innerHTML = `<div class="p-10 text-center text-red-400">Lỗi </div>`;
    } finally {
        contentArea.style.opacity = "1";
        contentArea.style.pointerEvents = "auto";
    }
}



// 4. Cập nhật Sidebar
function updateSidebarUI(pageId){
    document.querySelectorAll('.nav-item').forEach(item => {

        if(item.dataset.page === pageId){
            item.classList.add("active");
        }else{
            item.classList.remove("active");
        }

    });

}



function switchTab(tabName){
    // 1. Ẩn tất cả tab
    document.querySelectorAll('.tab-content').forEach(tab=>{
        tab.classList.remove('active');
    });

    // 2. Hiện tab được chọn
    const tab = document.getElementById('tab-' + tabName);
    if(tab){
        tab.classList.add('active');
    }

    // 3. Reset sidebar active
    document.querySelectorAll('.nav-item').forEach(item=>{
        item.classList.remove('active');
    });

    // 4. Active menu tương ứng
    const menu = document.getElementById(tabName);
    if(menu){
        menu.classList.add('active');
    }

}






window.addEventListener("DOMContentLoaded", () => {

    const hash = window.location.hash.replace("#", "");
    const savedPage = localStorage.getItem("currentPage");

    const page = hash || savedPage || "dashboard";

    loadPage(page, false);

});




// Hàm hỗ trợ chuyển đổi giao diện trên các thiết bị
function toggleSidebar(){
    document.getElementById("sidebar").classList.toggle("-translate-x-full")
}

function toggleChat(show) {
    const mainChat = document.getElementById('main-chat');
    const sidePanel = document.getElementById('side-panel');
    
    if (window.innerWidth < 768) { // Chỉ chạy trên Mobile
        if (show) {
            // Hiện khung chat, ẩn danh sách
            mainChat.classList.remove('translate-x-full');
            sidePanel.classList.add('-translate-x-full');
        } else {
            // Hiện danh sách, ẩn khung chat (Quay lại)
            mainChat.classList.add('translate-x-full');
            sidePanel.classList.remove('-translate-x-full');
        }
    }
}

// Đảm bảo khi xoay màn hình sang PC thì layout không bị lỗi ẩn
window.addEventListener('resize', () => {
    if (window.innerWidth >= 768) {
        const mainChat = document.getElementById('main-chat');
        const sidePanel = document.getElementById('side-panel');
        if(mainChat) mainChat.classList.remove('translate-x-full');
        if(sidePanel) sidePanel.classList.remove('-translate-x-full');
    }
});

document.querySelectorAll(".nav-item").forEach(item => {

    item.addEventListener("click", () => {

        if(window.innerWidth < 1024){
            document.getElementById("sidebar")
            .classList.add("-translate-x-full");
        }

    });

});

document.addEventListener("click", function(e){

    const sidebar = document.getElementById("sidebar");
    const toggleBtn = e.target.closest("button[onclick='toggleSidebar()']");

    if(
        !sidebar.contains(e.target) &&
        !toggleBtn &&
        window.innerWidth < 1024
    ){
        sidebar.classList.add("-translate-x-full");
    }

});










// album
function openAlbumModal() {
    const modal = document.getElementById('albumModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden'; // Chống cuộn trang khi mở modal
}
function closeAlbumModal() {
    const modal = document.getElementById('albumModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
}

// Đóng modal khi click ra ngoài vùng trắng
window.onclick = function(event) {
    const modal = document.getElementById('albumModal');
    if (event.target == modal) {
        closeAlbumModal();
    }
}











let variants = [];

// 1. Thêm mới (Luôn giữ nguyên chức năng này)
function addVariant() {
    const size = document.getElementById('size').value.trim();
    const color = document.getElementById('color').value.trim();
    const qty = document.getElementById('qty').value;

    if (!size || !color || !qty) return alert("Vui lòng nhập đủ thông tin!");

    variants.push({ id: Date.now(), size, color, qty, isEditing: false });
    renderTable();
    
    // Reset input
    document.getElementById('size').value = '';
    document.getElementById('color').value = '';
    document.getElementById('qty').value = '';
}

// 2. Chuyển dòng sang chế độ Sửa
function toggleEdit(id) {
    const index = variants.findIndex(v => v.id === id);
    if (index !== -1) {
        variants[index].isEditing = true;
        renderTable();
    }
}

// 3. Lưu giá trị sau khi sửa tại dòng
function saveEdit(id) {
    const index = variants.findIndex(v => v.id === id);
    if (index !== -1) {
        const row = document.querySelector(`tr[data-id="${id}"]`);
        variants[index].size = row.querySelector('.edit-size').value;
        variants[index].color = row.querySelector('.edit-color').value;
        variants[index].qty = row.querySelector('.edit-qty').value;
        variants[index].isEditing = false;
        renderTable();
    }
}

// 4. Xóa
function removeVariant(id) {
    variants = variants.filter(v => v.id !== id);
    renderTable();
}

// 5. Vẽ lại bảng (Hỗ trợ 2 chế độ: Hiển thị và Chỉnh sửa)
function renderTable() {
    const tbody = document.getElementById('variantList');
    tbody.innerHTML = variants.map(v => {
        if (v.isEditing) {
            // Chế độ đang SỬA: Hiện input ngay trong TD
            return `
                <tr class="border-b bg-blue-50" data-id="${v.id}">
                    <td class="py-2 px-2"> <input type="text" 
                        class="edit-size w-full bg-white rounded-xl py-1.5 px-3 text-xs border border-gray-200 focus:outline-none focus:ring-1 focus:ring-[#bc9c75]" 
                        value="${v.size}">
                    </td>
                    <td class="py-2 px-2"> <input type="text" 
                        class="edit-color w-full bg-white rounded-xl py-1.5 px-3 text-xs border border-gray-200 focus:outline-none focus:ring-1 focus:ring-[#bc9c75]" 
                        value="${v.color}">
                    </td>
                    <td class="py-2 px-2"> <input type="number" 
                        class="edit-qty w-full bg-white rounded-xl py-1.5 px-3 text-xs border border-gray-200 focus:outline-none focus:ring-1 focus:ring-[#bc9c75]" 
                        value="${v.qty}">
                    </td>
                    <td class="py-2 text-right space-x-2 px-2">
                        <button onclick="saveEdit(${v.id})" class="text-green-600 font-bold">Lưu</button>
                        <button onclick="removeVariant(${v.id})" class="text-red-500">Xóa</button>
                    </td>
                </tr>`;
        } else {
            // Chế độ HIỂN THỊ bình thường
            return `
                <tr class="border-b hover:bg-gray-50" data-id="${v.id}">
                    <td class="py-3">${v.size}</td>
                    <td class="py-3">${v.color}</td>
                    <td class="py-3">${v.qty}</td>
                    <td class="py-3 text-right space-x-3 px-2">
                        <button onclick="toggleEdit(${v.id})" class="text-blue-500 font-semibold">Sửa</button>
                        <button onclick="removeVariant(${v.id})" class="text-red-500 font-semibold">Xóa</button>
                    </td>
                </tr>`;
        }
    }).join('');
}