
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
