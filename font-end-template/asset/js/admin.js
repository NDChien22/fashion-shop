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

    'product-list': { title: 'Danh sách mẫu', file: 'manage-products.html', tpl: 'tpl-product-list' },
    'add-product': { title: 'Thêm sản phẩm', file: 'manage-products.html', tpl: 'tpl-add-product' },
    'edit-product': { title: 'Sửa sản phẩm', file: 'manage-products.html', tpl: 'tpl-edit-product' },
    'vouchers': { title: 'Voucher', file: 'manage-products.html', tpl: 'tpl-vouchers' },
    'add-voucher': { title: 'Tạo voucher', file: 'manage-products.html', tpl: 'tpl-add-voucher' },
    'edit-voucher': { title: 'Chỉnh sửa Voucher', file: 'manage-products.html', tpl: 'tpl-edit-voucher' },

    'orders': { title: 'Đơn hàng', file: 'order.html', tpl: 'tpl-orders' },
    'order-detail': { title: 'Chi tiết đơn hàng', file: 'order.html', tpl: 'tpl-order-detail' },
    'revenue': { title: 'Doanh thu', file: 'order.html', tpl: 'tpl-revenue' },

    'customers': { title: 'Khách hàng', file: 'customers.html', tpl: 'tpl-customers' },
    'support': { title: 'Hỗ trợ khách hàng', file: 'customers.html', tpl: 'tpl-support' },
    'hr': { title: 'Quản lý nhân sự', file: 'customers.html', tpl: 'tpl-hr' },

    'quan-ly': { title: 'Hồ sơ cá nhân', file: 'account-management.html', tpl: 'tpl-quan-ly' },
    'thong-tin': { title: 'Hồ sơ cá nhân', file: 'account-management.html', tpl: 'tpl-tn' },
    'account': { title: 'Cài đặt tài khoản', file: 'account-management.html', tpl: 'tpl-account' },

};


const fileCache = {}; 

// 3. Hàm tải trang chính (Sửa lỗi logic)
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
        contentArea.innerHTML = `<div class="p-10 text-center text-red-400">Lỗi kết nối máy chủ</div>`;
    } finally {
        contentArea.style.opacity = "1";
        contentArea.style.pointerEvents = "auto";
    }
}



// 4. Cập nhật Sidebar
function updateSidebarUI(pageId){

    document.querySelectorAll('.nav-item').forEach(item => {

        const isMatch =
            item.dataset.page === pageId ||
            (item.getAttribute('onclick') &&
             item.getAttribute('onclick').includes(`'${pageId}'`));

        if(isMatch){
            item.classList.add('active');
        }else{
            item.classList.remove('active');
        }

    });

}



window.addEventListener("DOMContentLoaded", () => {

    const hash = window.location.hash.replace("#", "#");
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

// xem chi tiết đơn hàng
function viewOrderDetail(orderId) {
    loadPage('order-detail');
}







