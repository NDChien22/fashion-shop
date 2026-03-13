/** * 1. CẤU HÌNH & KHỞI TẠO 
 */
const PAGES = {
    'dashboard': { title: 'Tổng quan' },

    'product-list': { title: 'Danh sách mẫu', file: 'manager-products.html', tpl: 'tpl-product-list' },
    'add-product': { title: 'Thêm sản phẩm', file: 'manager-products.html', tpl: 'tpl-add-product' },
    'edit-product': { title: 'Sửa sản phẩm', file: 'manager-products.html', tpl: 'tpl-edit-product' },
    
    'collection': { title: 'Bộ sưu tập', file: 'manager-products.html', tpl: 'tpl-collection' },
    'collection-detail': { title: 'Các sản phẩm ', file: 'manager-products.html', tpl: 'tpl-collection-detail' },
    'add-collection': { title: 'Thêm bộ sưu tập', file: 'manager-products.html', tpl: 'tpl-add-collection' },

    'vouchers': { title: 'Voucher', file: 'voucher.html', tpl: 'tpl-vouchers' },
    'add-voucher': { title: 'Tạo voucher', file: 'voucher.html', tpl: 'tpl-add-voucher' },
    'edit-voucher': { title: 'Chỉnh sửa Voucher', file: 'voucher.html', tpl: 'tpl-edit-voucher' },
    'flash-sale': { title: 'Chương trình khuyến mãi', file: 'voucher.html', tpl: 'tpl-flash-sale' },

    'orders': { title: 'Đơn hàng', file: 'manager-orders.html', tpl: 'tpl-orders' },
    'order-detail': { title: 'Chi tiết đơn hàng', file: 'manager-orders.html', tpl: 'tpl-order-detail' },
    'revenue': { title: 'Doanh thu', file: 'manager-orders.html', tpl: 'tpl-revenue' },
    

    'customers': { title: 'Khách hàng', file: 'manager-customers.html', tpl: 'tpl-customers' },
    'support': { title: 'Hỗ trợ khách hàng', file: 'manager-customers.html', tpl: 'tpl-support' },

    'hr': { title: 'Quản lý nhân sự', file: 'manager-employee.html', tpl: 'tpl-hr' },
    'user-detail': { title: 'Chi tiết nhân viên', file: 'manager-employee.html', tpl: 'tpl-user-detail' },
    'add-employee': { title: 'Thêm nhân viên', file: 'manager-employee.html', tpl: 'tpl-add-employee' },
    'edit-employee': { title: 'Sửa nhân viên', file: 'manager-employee.html', tpl: 'tpl-edit-employee' },
    'role-management': { title: 'Thêm chức vụ', file: 'manager-employee.html', tpl: 'tpl-role-management' },
    
    'quan-ly': { title: 'Hồ sơ cá nhân', file: 'account-management.html', tpl: 'tpl-quan-ly' },
    'thong-tin': { title: 'Hồ sơ cá nhân', file: 'account-management.html', tpl: 'tpl-tn' },
    'account': { title: 'Cài đặt tài khoản', file: 'account-management.html', tpl: 'tpl-account' },
};





const fileCache = {};
let variants = [];

/** * 2. ĐIỀU HƯỚNG TRANG (SPA LOGIC) 
 */
async function loadPage(pageId) {
    const contentArea = document.getElementById("content-area");
    if (!contentArea) return;

    const config = PAGES[pageId] || PAGES['dashboard'];
    const activePageId = PAGES[pageId] ? pageId : 'dashboard';

    // Cập nhật trạng thái trình duyệt
    window.location.hash = activePageId;
    localStorage.setItem("currentPage", activePageId);

    contentArea.style.opacity = "0.6"; 
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
                if (!response.ok) throw new Error("Lỗi tải file");
                const htmlText = await response.text();
                doc = new DOMParser().parseFromString(htmlText, "text/html");
                fileCache[config.file] = doc;
            }
            template = doc.getElementById(config.tpl);
        }

        contentArea.innerHTML = "";
        if (template) {
            const node = (template.tagName === "TEMPLATE") ? template.content.cloneNode(true) : template.cloneNode(true);
            contentArea.appendChild(node);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            contentArea.innerHTML = `<div class="p-10 text-center text-gray-400">Trang đang được xây dựng...</div>`;
        }

        updateHeaderUI(config.title, activePageId);
        updateSidebarUI(activePageId);

    } catch (err) {
        console.error(err);
        contentArea.innerHTML = `<div class="p-10 text-center text-red-500">Lỗi nạp trang. Vui lòng thử lại.</div>`;
    } finally {
        contentArea.style.opacity = "1";
        contentArea.style.pointerEvents = "auto";
    }
}

function updateHeaderUI(title, pageId) {
    const pageTitle = document.getElementById("page-title");
    const breadcrumb = document.getElementById("breadcrumb-current");
    const pageHeader = document.querySelector(".ff-page-header");

    if (pageHeader) pageHeader.style.display = (pageId === "dashboard") ? "none" : "block";
    if (pageTitle) pageTitle.innerText = title;
    if (breadcrumb) breadcrumb.innerHTML = `<span class="text-[#bc9c75] font-medium">${title}</span>`;
}

//  3. GIAO DIỆN SIDEBAR & MENU 

function updateSidebarUI(pageId){
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.toggle("active", item.dataset.page === pageId);
    });
}

function toggleSidebar(){
    document.getElementById("sidebar").classList.toggle("-translate-x-full");
}

function toggleUserMenu(){
    document.getElementById("userMenu").classList.toggle("hidden");
}

// Đóng sidebar/menu khi click ra ngoài
document.addEventListener("click", function(e){
    const sidebar = document.getElementById("sidebar");
    const menu = document.getElementById("userMenu");
    const userArea = e.target.closest(".relative");
    const toggleBtn = e.target.closest("button[onclick='toggleSidebar()']");

    // Đóng User Menu
    if(!userArea && menu) menu.classList.add("hidden");

    // Đóng Sidebar Mobile
    if(sidebar && !sidebar.contains(e.target) && !toggleBtn && window.innerWidth < 1024){
        sidebar.classList.add("-translate-x-full");
    }
});


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




// 4. QUẢN LÝ BIẾN THỂ (VARIANTS) 
 
function renderTable() {
    const tbody = document.getElementById('variantList');
    if (!tbody) return;
    tbody.innerHTML = variants.map(v => v.isEditing ? `
        <tr class="border-b bg-blue-50" data-id="${v.id}">
            <td class="py-2 px-2"><input type="text" class="edit-size w-full rounded-xl p-1.5 border" value="${v.size}"></td>
            <td class="py-2 px-2"><input type="text" class="edit-color w-full rounded-xl p-1.5 border" value="${v.color}"></td>
            <td class="py-2 px-2"><input type="number" class="edit-qty w-full rounded-xl p-1.5 border" value="${v.qty}"></td>
            <td class="py-2 text-right px-2">
                <button onclick="saveEdit(${v.id})" class="text-green-600 font-bold">Lưu</button>
                <button onclick="removeVariant(${v.id})" class="text-red-500">Xóa</button>
            </td>
        </tr>` : `
        <tr class="border-b hover:bg-gray-50" data-id="${v.id}">
            <td class="py-3">${v.size}</td>
            <td class="py-3">${v.color}</td>
            <td class="py-3">${v.qty}</td>
            <td class="py-3 text-right space-x-3 px-2">
                <button onclick="toggleEdit(${v.id})" class="text-blue-500 font-semibold">Sửa</button>
                <button onclick="removeVariant(${v.id})" class="text-red-500 font-semibold">Xóa</button>
            </td>
        </tr>`).join('');
}

function addVariant() {
    const size = document.getElementById('size').value.trim();
    const color = document.getElementById('color').value.trim();
    const qty = document.getElementById('qty').value;

    if (!size || !color || !qty) return alert("Vui lòng nhập đầy đủ thông tin!");

    variants.push({ id: Date.now(), size, color, qty: Number(qty), isEditing: false });
    renderTable();
    ['size', 'color', 'qty'].forEach(id => document.getElementById(id).value = '');
}

function toggleEdit(id) {
    const v = variants.find(v => v.id === id);
    if (v) { v.isEditing = true; renderTable(); }
}

function saveEdit(id) {
    const v = variants.find(v => v.id === id);
    if (v) {
        const row = document.querySelector(`tr[data-id="${id}"]`);
        v.size = row.querySelector('.edit-size').value;
        v.color = row.querySelector('.edit-color').value;
        v.qty = row.querySelector('.edit-qty').value;
        v.isEditing = false;
        renderTable();
    }
}

function removeVariant(id) {
    if(confirm("Xóa biến thể này?")) {
        variants = variants.filter(v => v.id !== id);
        renderTable();
    }
}

//  5. MODALS & KHÁC 

/**
 * Hàm mở Modal dùng chung
 * @param {string} modalId - ID của thẻ div Modal cần mở
 */
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    
    if (!modal) {
        console.error(`LỖI: Không tìm thấy Modal có ID là "${modalId}"`);
        return;
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Chặn cuộn trang nếu modal là 'fixed' (phủ toàn màn hình)
    if (modal.classList.contains('fixed')) {
        document.body.style.overflow = 'hidden';
    }
}

/**
 * Hàm đóng Modal dùng chung
 * @param {string} modalId - ID của thẻ div Modal cần đóng
 */
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }
}

// Lắng nghe phím Esc để đóng Modal đang mở
window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        // Tìm tất cả các modal đang không bị ẩn và đóng chúng
        const openModals = document.querySelectorAll('.flex[id$="Modal"]'); 
        openModals.forEach(modal => closeModal(modal.id));
    }
});


//  6. KHỞI CHẠY 

window.addEventListener("DOMContentLoaded", () => {
    const hash = window.location.hash.replace("#", "");
    const savedPage = localStorage.getItem("currentPage");
    loadPage(hash || savedPage || "dashboard");
});

window.addEventListener("hashchange", () => {
    const hash = window.location.hash.replace("#", "");
    if (hash) loadPage(hash);
});