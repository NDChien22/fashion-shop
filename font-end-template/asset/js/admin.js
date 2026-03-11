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

const employeeData = {

name: "TRẦN QUỐC BẢO",
birth: "1999-03-10",
gender: "Nam",
role: "Quản lý",
phone: "0988888888",
address: "Hà Nội"

};


function openEmployee(emp){

    document.getElementById("employeeModal").classList.remove("hidden");
    document.getElementById("empName").value = emp.name;
    document.getElementById("empBirth").value = emp.birth;
    document.getElementById("empGender").value = emp.gender;
    document.getElementById("empRole").value = emp.role;
    document.getElementById("empPhone").value = emp.phone;
    document.getElementById("empAddress").value = emp.address;

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

// xem chi tiết đơn hàng
function viewOrderDetail(orderId) {
    loadPage('order-detail');
}


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



function openProductModal(product = mockProduct) {
    const modal = document.getElementById('productDetailModal');
    
    // Đổ dữ liệu vào các trường
    document.getElementById('detail-name').innerText = product.name;
    document.getElementById('detail-sku').innerText = "Mã: " + product.sku;
    document.getElementById('detail-price').value = product.price;
    document.getElementById('detail-desc').value = product.desc;
    document.getElementById('detail-img').src = product.img;

    // Hiển thị modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeProductModal() {
    const modal = document.getElementById('productDetailModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
}






function handlePrint(orderId) {
    const order = ordersData.find(o => o.id === orderId);
    if (!order) return;

    // Đổ dữ liệu text
    document.querySelector('.print-order-id').innerText = `#${order.id}`;
    document.querySelector('.print-date').innerText = order.date;
    document.querySelector('.print-cust-name').innerText = order.customer.name;
    document.querySelector('.print-cust-phone').innerText = `SĐT: ${order.customer.phone}`;
    document.querySelector('.print-cust-address').innerText = order.customer.address;

    // Đổ danh sách sản phẩm
    const tbody = document.querySelector('.print-items');
    tbody.innerHTML = order.items.map((item, index) => `
        <tr>
            <td class="py-4">${String(index + 1).padStart(2, '0')}</td>
            <td class="py-4 font-bold text-gray-800">${item.name} <br><small class='font-normal text-gray-400'>${item.variant}</small></td>
            <td class="text-center">${item.qty}</td>
            <td class="text-right">${item.price.toLocaleString()}đ</td>
            <td class="text-right font-bold">${(item.qty * item.price).toLocaleString()}đ</td>
        </tr>
    `).join('');

    // Đổ số tiền
    document.querySelector('.print-subtotal').innerText = `${order.subtotal.toLocaleString()}đ`;
    document.querySelector('.print-discount').innerText = `-${order.discount.toLocaleString()}đ`;
    document.querySelector('.print-total').innerText = `${order.total.toLocaleString()}đ`;

    // Đợi 500ms để trình duyệt vẽ xong dữ liệu lên trang rồi mới gọi lệnh In
    setTimeout(() => {
        window.print();
    }, 500);
}





function openUserDetail(user) {
    // 1. Truy xuất template từ ID
    const template = document.getElementById('tpl-user-detail');
    if (!template) return;

    // 2. Tạo một bản sao (clone) của template
    const clone = template.content.cloneNode(true);

    // 3. Đổ dữ liệu vào các vị trí tương ứng trong bản sao
    clone.querySelector('.js-detail-img').src = user.img || '/asset/img/default-avatar.png';
    clone.querySelector('.js-detail-name').innerText = user.name;
    clone.querySelector('.js-detail-role').innerText = user.role || 'Thành viên';
    clone.querySelector('.js-detail-dob').innerText = user.dob || '--/--/----';
    clone.querySelector('.js-detail-gender').innerText = user.gender || 'Chưa xác định';
    clone.querySelector('.js-detail-phone').innerText = user.phone;
    clone.querySelector('.js-detail-email').innerText = user.email;
    clone.querySelector('.js-detail-address').innerText = user.address || 'Chưa cập nhật';
    clone.querySelector('.js-detail-join-date').innerText = user.joinDate || '--/--/----';

    // 4. Đưa bản sao đã có dữ liệu vào trang web
    document.body.appendChild(clone);
}



// Hàm đóng Modal
function closeModal() {
    const modal = document.querySelector('.fixed.inset-0'); // Tìm overlay
    if (modal) {
        modal.classList.add('animate-out', 'fade-out', 'zoom-out'); // Thêm hiệu ứng đóng (nếu có)
        setTimeout(() => modal.remove(), 200); // Xóa khỏi DOM
    }
}


function xemChiTietSanPham(data) {
    // 1. Lấy template thông tin (không phải template sửa)
    const temp = document.getElementById('tpl-productDetailModal');
    const clone = temp.content.cloneNode(true);
    
    // 2. Đổ dữ liệu vào các thẻ text (không dùng input)
    // clone.querySelector('#view-name').innerText = data.name; ...

    // 3. QUAN TRỌNG: Thêm trực tiếp vào body thay vì loadPage
    // Điều này giúp danh sách mẫu bên dưới không bị mất đi
    document.body.appendChild(clone);
    
    // 4. Thêm hiệu ứng khóa cuộn trang sau
    document.body.style.overflow = 'hidden';
}

function closeProductModal() {
    // Tìm và xóa lớp phủ ra khỏi màn hình
    const modal = document.querySelector('#product-modal-container');
    if (modal) modal.remove();
    document.body.style.overflow = 'auto';
}