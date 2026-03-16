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
    'role-management': { title: 'Thêm chức vụ', file: 'manager-employee.html', tpl: 'tpl-role-management' },
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

// --- LOGIC BIẾN THỂ (VARIANTS) ---

function renderTable() {
    const tbody = document.getElementById('variantList');
    if (!tbody) return; // Tránh lỗi khi đang ở trang không có bảng

    if (variants.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4" class="py-4 text-center text-gray-400 text-[10px]">Chưa có biến thể nào được thêm</td></tr>`;
        return;
    }

    tbody.innerHTML = variants.map(v => v.isEditing ? `
        <tr class="border-b bg-blue-50" data-id="${v.id}">
            <td class="py-2 px-2"><input type="text" class="edit-size w-full rounded-lg p-1.5 border text-xs" value="${v.size}"></td>
            <td class="py-2 px-2"><input type="text" class="edit-color w-full rounded-lg p-1.5 border text-xs" value="${v.color}"></td>
            <td class="py-2 px-2"><input type="number" class="edit-qty w-full rounded-lg p-1.5 border text-xs" value="${v.qty}"></td>
            <td class="py-2 text-right px-2 space-x-2">
                <button onclick="saveEdit(${v.id})" class="text-green-600 font-bold text-[10px]">LƯU</button>
                <button onclick="removeVariant(${v.id})" class="text-red-500 text-[10px]">XÓA</button>
            </td>
        </tr>` : `
        <tr class="border-b hover:bg-gray-50" data-id="${v.id}">
            <td class="py-3 px-2 text-gray-700">${v.size}</td>
            <td class="py-3 px-2 text-gray-700">${v.color}</td>
            <td class="py-3 px-2 text-gray-700">${v.qty}</td>
            <td class="py-3 text-right space-x-3 px-2">
                <button onclick="toggleEdit(${v.id})" class="text-blue-500 font-semibold text-[10px]">SỬA</button>
                <button onclick="removeVariant(${v.id})" class="text-red-500 font-semibold text-[10px]">XÓA</button>
            </td>
        </tr>`).join('');
}

function addVariant() {
    // Lấy chính xác các ID từ giao diện Input của bạn
    const sInput = document.getElementById('size');
    const cInput = document.getElementById('color');
    const qInput = document.getElementById('qty');

    if (!sInput || !cInput || !qInput) {
        alert("Lỗi hệ thống: Không tìm thấy các ô nhập dữ liệu!");
        return;
    }

    const size = sInput.value.trim();
    const color = cInput.value.trim();
    const qty = qInput.value;

    if (!size || !color || !qty) {
        alert("Vui lòng nhập đủ Size, Màu và Số lượng!");
        return;
    }

    const isDuplicate = variants.some(v => 
        v.size.toLowerCase() === size.toLowerCase() && 
        v.color.toLowerCase() === color.toLowerCase()
    );

    if (isDuplicate) {
        alert(`Biến thể [Size: ${size} - Màu: ${color}] đã có trong danh sách!`);
        return; // Dừng hàm, không thêm vào mảng
    }

    // Thêm vào mảng tạm
    variants.push({ 
        id: Date.now(), 
        size: size, 
        color: color, 
        qty: Number(qty), 
        isEditing: false 
    });

    renderTable(); 
    // Xóa trống để nhập tiếp
    sInput.value = '';
    cInput.value = '';
    qInput.value = '';
    sInput.focus();
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
    if(confirm("Xác nhận xóa biến thể này?")) {
        variants = variants.filter(v => v.id !== id);
        renderTable();
    }
}

// Xử lý đóng Modal khi click ra ngoài (Dùng Event Listener để tránh ghi đè)
window.addEventListener('click', function(event) {
    const albumModal = document.getElementById('albumModal');
    if (event.target === albumModal) {
        closeAlbumModal();
    }
});



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
            fillFn: (d) => fillProductFields(d), // Hàm xử lý khóa trường nằm ở đây
            clearFn: () => clearProductForm()    // Hàm xử lý mở khóa nằm ở đây
        }
    };

    const target = config[type];
    if (!target) return;

    loadPage(target.page);

    setTimeout(() => {
        const mainTitle = document.getElementById('page-title');
        const breadcrumb = document.getElementById('breadcrumb-current');
        const btnSubmit = document.getElementById(target.btnId);

        if (!mainTitle || !btnSubmit) return;

        if (data) {
            // CHẾ ĐỘ SỬA
            mainTitle.innerText = target.titleEdit;
            if (breadcrumb) breadcrumb.innerText = target.titleEdit;
            btnSubmit.innerText = "Lưu cập nhật";
            btnSubmit.onclick = () => target.saveFn(data.id);
            
            target.fillFn(data); // Đổ dữ liệu & Khóa trường
        } else {
            // CHẾ ĐỘ THÊM MỚI
            mainTitle.innerText = target.titleAdd;
            if (breadcrumb) breadcrumb.innerText = target.titleAdd.replace(' mới', '');
            btnSubmit.innerText = "Thêm sản phẩm";
            btnSubmit.onclick = () => target.saveFn(null);
            
            target.clearFn(); // Xóa form & Mở khóa trường
        }
    }, 150);
}

function fillProductFields(data) {
    // 1. Map dữ liệu vào Input
    const mapping = {
        'prod-id': data.id,
        'prod-name': data.name,
        'prod-desc': data.desc,
        'prod-price': data.price,
        'prod-cate': data.category_id,
        'prod-collection': data.collection_id
    };

    // 2. Duyệt qua mapping để điền giá trị và khóa trường
    for (const [id, value] of Object.entries(mapping)) {
        const el = document.getElementById(id);
        if (el) {
            el.value = value || '';
            
            // Nếu là Phân loại hoặc Bộ sưu tập thì KHÓA (không cho sửa)
            if (id === 'prod-cate' || id === 'prod-collection') {
                el.disabled = true;
                el.classList.add('bg-gray-100', 'cursor-not-allowed', 'opacity-70');
            }
        }
    }
    
    // 3. Đổ danh sách biến thể vào bảng
    if (data.variants) {
        variants = [...data.variants]; // Gán vào mảng toàn cục
        renderTable(); 
    }
}


function clearProductForm() {
    const fields = ['prod-id', 'prod-name', 'prod-desc', 'prod-price', 'prod-cate', 'prod-collection'];
    
    fields.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.value = '';
            // MỞ KHÓA lại khi thêm mới sản phẩm
            el.disabled = false;
            el.classList.remove('bg-gray-100', 'cursor-not-allowed', 'opacity-70');
        }
    });

    // Reset bảng biến thể về trạng thái trống
    variants = [];
    renderTable();
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

                <div class="w-12 h-12 rounded-xl bg-gray-50 overflow-hidden border border-gray-100 flex-shrink-0">
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

                <div class="text-right flex-shrink-0">
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