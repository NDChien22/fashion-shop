/** * 1. CẤU HÌNH & KHỞI TẠO 
 */
const PAGES = {
    'dashboard': { title: 'Tổng quan' },

    'product-list': { title: 'Danh sách mẫu', file: 'manager-products.html', tpl: 'tpl-product-list' },
    'product-form': { title: 'Thêm sản phẩm', file: 'manager-products.html', tpl: 'tpl-product-form' },
    
    'collection': { title: 'Bộ sưu tập', file: 'manager-products.html', tpl: 'tpl-collection' },
    'collection-detail': { title: 'Các sản phẩm ', file: 'manager-products.html', tpl: 'tpl-collection-detail' },
    'add-collection': { title: 'Thêm bộ sưu tập', file: 'manager-products.html', tpl: 'tpl-add-collection' },

    'vouchers': { title: 'Voucher', file: 'voucher.html', tpl: 'tpl-vouchers' },
    'voucher-form': { title: 'Tạo voucher', file: 'voucher.html', tpl: 'tpl-voucher-form' },
    'flash-sale': { title: 'Chương trình khuyến mãi', file: 'voucher.html', tpl: 'tpl-flash-sale' },
    'promotion-form': { title: 'Chương trình khuyến mãi', file: 'voucher.html', tpl: 'tpl-promotion-form' },

    'orders': { title: 'Đơn hàng', file: 'manager-orders.html', tpl: 'tpl-orders' },
    'order-detail': { title: 'Chi tiết đơn hàng', file: 'manager-orders.html', tpl: 'tpl-order-detail' },
    'revenue': { title: 'Doanh thu', file: 'manager-orders.html', tpl: 'tpl-revenue' },
    

    'customers': { title: 'Khách hàng', file: 'manager-customers.html', tpl: 'tpl-customers' },
    'support': { title: 'Hỗ trợ khách hàng', file: 'manager-customers.html', tpl: 'tpl-support' },

    'hr': { title: 'Quản lý nhân sự', file: 'manager-employee.html', tpl: 'tpl-hr' },
    'user-detail': { title: 'Chi tiết nhân viên', file: 'manager-employee.html', tpl: 'tpl-user-detail' },
    'employee-form': { title: 'Thông tin nhân viên', file: 'manager-employee.html', tpl: 'tpl-employee-form' },
    
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
// --- LOGIC ALBUM ---
function openAlbumModal() {
    const modal = document.getElementById('albumModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    } else {
        console.error("Lỗi: Không tìm thấy phần tử có ID 'albumModal'. Hãy kiểm tra lại file manager-products.html.");
    }
}

function closeAlbumModal() {
    const modal = document.getElementById('albumModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }
}



 
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










function openForm(type, data = null) {
    const config = {
        employee: {
            page: 'employee-form',
            btnId: 'btn-submit-employee',
            titleAdd: 'Thêm nhân viên mới',
            titleEdit: 'Chỉnh sửa nhân viên',
            saveFn: (id) => saveEmployeeData(id),
            fillFn: (d) => fillEmployeeFields(d),
            clearFn: () => clearEmployeeFields()
        },
        product: {
            page: 'product-form',
            btnId: 'btn-save-product',
            titleAdd: 'Thêm sản phẩm mới',
            titleEdit: 'Chỉnh sửa sản phẩm',
            saveFn: (id) => saveProductData(id),
            fillFn: (d) => fillProductFields(d),
            clearFn: () => clearProductForm()
        },
        voucher: {
            page: 'voucher-form',
            btnId: 'btn-save-voucher',
            titleAdd: 'Tạo mã giảm giá mới',
            titleEdit: 'Cập nhật Voucher',
            saveFn: (id) => saveVoucherData(id),
            fillFn: (d) => fillVoucherFields(d),
            clearFn: () => clearVoucherForm()
        },
        promotion: {
            page: 'promotion-form', 
            btnId: 'btn-save-promotion',
            titleAdd: 'Thiết lập Flash Sale mới',
            titleEdit: 'Chỉnh sửa chương trình',
            saveFn: (id) => savePromotionData(id),
            fillFn: (d) => fillPromotionFields(d),
            clearFn: () => clearPromotionForm()
        }
    };

    const target = config[type];
    if (!target) return;

    // 1. Chuyển trang/Nạp nội dung form
    loadPage(target.page);

    // 2. Xử lý logic sau khi DOM đã sẵn sàng
    setTimeout(() => {
        const mainTitle = document.getElementById('page-title');
        const breadcrumb = document.getElementById('breadcrumb-current');
        const btnSubmit = document.getElementById(target.btnId);

        if (!mainTitle || !btnSubmit) return;

        if (data) {
            // CHẾ ĐỘ SỬA
            mainTitle.innerText = target.titleEdit;
            if (breadcrumb) breadcrumb.innerText = target.titleEdit;
            btnSubmit.innerText = "Lưu thay đổi";
            btnSubmit.onclick = () => target.saveFn(data.id);
            
            // Điền dữ liệu vào form
            target.fillFn(data);
        } else {
            // CHẾ ĐỘ THÊM MỚI
            mainTitle.innerText = target.titleAdd;
            if (breadcrumb) breadcrumb.innerText = target.titleAdd.replace(' mới', '');
            btnSubmit.innerText = "Xác nhận thêm";
            btnSubmit.onclick = () => target.saveFn(null);
            
            // Làm sạch form
            target.clearFn();
        }
    }, 150); // Đợi 150ms để trang kịp load
}


function fillProductFields(data) {
    const mapping = {
        'prod-id': data.id,          // ID sản phẩm (ẩn hoặc dùng để hiển thị)
        'prod-name': data.name,      // Tên sản phẩm
        'prod-price': data.price,    // Giá bán
        'prod-cate': data.category,  // Danh mục (Sơ mi, Quần jean...)
        'prod-desc': data.desc,      // Mô tả chi tiết sản phẩm
        'prod-stock': data.stock     // Số lượng tồn kho
    };

    for (const [id, value] of Object.entries(mapping)) {
        const el = document.getElementById(id);
        if (el) el.value = value || '';
    }
}


function fillEmployeeFields(data) {
    const mapping = {
        'emp-name': data.name,       // Họ và tên
        'emp-email': data.email,     // Email liên lạc
        'emp-phone': data.phone,     // Số điện thoại
        'emp-role': data.role,       // Chức vụ (Quản lý, Bán hàng)
        'emp-dob': data.dob,         // Ngày sinh
        'emp-address': data.address  // Địa chỉ thường trú
    };

    for (const [id, value] of Object.entries(mapping)) {
        const el = document.getElementById(id);
        if (el) el.value = value || '';
    }
}



// Cho Voucher
function fillVoucherFields(data) {
    const mapping = {
        'v-code': data.code,
        'v-discount': data.discount,
        'v-type': data.type,
        'v-start': data.startDate,
        'v-end': data.endDate
    };
    for (const [id, val] of Object.entries(mapping)) {
        const el = document.getElementById(id);
        if (el) el.value = val || '';
    }
}

// Cho Flash Sale (Promotion)
function fillPromotionFields(data) {
    const mapping = {
        'promo-name': data.name,
        'promo-code': data.code,
        'promo-value': data.value,
        'promo-start': data.startDate,
        'promo-end': data.endDate
    };
    for (const [id, val] of Object.entries(mapping)) {
        const el = document.getElementById(id);
        if (el) el.value = val || '';
    }
}


function clearProductForm() {
    const ids = ['prod-id', 'prod-name', 'prod-price', 'prod-cate', 'prod-desc'];
    ids.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
}


function clearEmployeeFields() {
    const ids = ['emp-name', 'emp-email', 'emp-phone', 'emp-role', 'emp-dob', 'emp-address'];
    ids.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
}


function clearVoucherFields() {
    const ids = ['v-code', 'v-name', 'v-type', 'v-value', 'v-min-order', 'v-start', 'v-end'];
    ids.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.tagName === 'SELECT' ? el.selectedIndex = 0 : el.value = '';
        }
    });
}

function clearPromotionFields() {
    const ids = ['promo-name', 'promo-code', 'promo-discount', 'promo-status', 'promo-start', 'promo-end'];
    ids.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
}



















































// 1. Khai báo data mẫu (Bắt buộc phải có đoạn này)
let allProducts = [
    { id: 1, name: "Sản phẩm A", price: 100000, collection_id: null, image_url: "" },
    { id: 2, name: "Sản phẩm B", price: 200000, collection_id: 1, image_url: "" },
    { id: 3, name: "Sản phẩm C", price: 150000, collection_id: null, image_url: "" }
];

let selectedProductIds = [];

// 2. Hàm mở Modal
function openProductSelectionModal() {
    console.log("Đang mở modal..."); // Dùng để kiểm tra trong Console xem nút có ăn lệnh không
    const modal = document.getElementById('productSelectionModal');
    const list = document.getElementById('unassignedProductList');

    if (!modal || !list) {
        alert("Lỗi: Không tìm thấy Modal trong HTML!");
        return;
    }
    // Lọc sản phẩm chưa có BST (collection_id là null hoặc trống)
    const available = allProducts.filter(p => !p.collection_id);
    // Hiển thị modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    // Vẽ danh sách
    if (available.length === 0) {
        list.innerHTML = `<p class="text-center text-gray-400 py-5">Hết sản phẩm để thêm!</p>`;
    } else {
        list.innerHTML = available.map(p => `
            <label class="flex items-center gap-4 p-3 mb-2 rounded-2xl hover:bg-gray-50 cursor-pointer transition-all group border border-transparent hover:border-gray-100">
                <div class="relative flex items-center">
                    <input type="checkbox" 
                        onchange="toggleSelect(${p.id})" 
                        class="w-5 h-5 rounded-md border-gray-300 text-[#bc9c75] focus:ring-[#bc9c75] cursor-pointer accent-[#bc9c75]">
                </div>

                <div class="w-12 h-12 rounded-xl bg-gray-50 overflow-hidden border border-gray-100 shrink-0">
                    <img src="${p.image_url || ''}" 
                        onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(p.name)}&background=f3f4f6&color=bc9c75'" 
                        class="w-full h-full object-cover">
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-700 group-hover:text-[#bc9c75] truncate transition-colors">
                        ${p.name}
                    </p>
                    <p class="text-[10px] text-gray-400 font-medium tracking-tight">
                        Mã sản phẩm: #SP-${p.id}
                    </p>
                </div>

                <div class="text-right shrink-0">
                    <p class="text-xs font-bold text-gray-900">
                        ${p.price ? p.price.toLocaleString() + 'đ' : '---'}
                    </p>
                </div>
            </label>
        `).join('');
    }
}

function toggleSelect(id) {
    const idx = selectedProductIds.indexOf(id);
    if (idx > -1) selectedProductIds.splice(idx, 1);
    else selectedProductIds.push(id);
}

function closeProductSelectionModal() {
    const modal = document.getElementById('productSelectionModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}




function openOrderHistory() {
    document.getElementById('customerDetailModal').classList.add('hidden');
    document.getElementById('orderHistoryModal').classList.remove('hidden');
}

function closeOrderHistory() {
    document.getElementById('orderHistoryModal').classList.add('hidden');
    document.getElementById('customerDetailModal').classList.remove('hidden');
}

function openOrderDetail(orderId) {
    // 1. Tìm Modal Lịch sử đơn hàng bằng ID và ẩn nó đi
    const historyModal = document.getElementById('orderHistoryModal'); 
    if (historyModal) {
        historyModal.classList.add('hidden'); // Nếu dùng Tailwind
        // Hoặc: historyModal.style.display = 'none';
    }

    // 2. Logic hiển thị trang Chi tiết đơn hàng của bạn
    // Ví dụ: Hiển thị div chi tiết hoặc chuyển trang
    const detailPage = document.getElementById('orderDetailPage');
    if (detailPage) {
        detailPage.classList.remove('hidden');
    }
    
    console.log("Đang mở chi tiết đơn hàng: " + orderId);
}




