

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

function openProductDetail(name, sku, price, img) {
    const modal = document.getElementById('productModal');
    document.getElementById('modalName').innerText = name;
    document.getElementById('modalSku').innerText = "Mã: " + sku;
    document.getElementById('modalPrice').innerText = price;
    document.getElementById('modalImg').src = img;

    modal.classList.remove('hidden');
    setTimeout(() => modal.style.opacity = "1", 10);
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    const modal = document.getElementById('productModal');
    modal.style.opacity = "0";
    setTimeout(() => modal.classList.add('hidden'), 300);
    document.body.style.overflow = 'auto';
}
