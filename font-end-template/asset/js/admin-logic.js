
document.addEventListener('DOMContentLoaded', () => {
    handleTabSwitch();
    renderProducts();
});

// 1. Xử lý chuyển đổi Tab
function handleTabSwitch() {
    const navItems = document.querySelectorAll('.nav-item');
    const contents = document.querySelectorAll('.tab-content');

    navItems.forEach(item => {
        item.addEventListener('click', () => {
            // Xóa active ở các nút cũ
            navItems.forEach(nav => nav.classList.remove('active'));
            item.classList.add('active');

            // Hiển thị nội dung tương ứng
            const target = item.getAttribute('data-target');
            contents.forEach(content => {
                content.classList.remove('active');
                if (content.id === target) {
                    content.classList.add('active');
                }
            });
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('profileToggle');
    const menu = document.getElementById('dropdownMenu');

    // Click vào profile để hiện menu
    toggle.addEventListener('click', (e) => {
        e.stopPropagation();
        menu.classList.toggle('show');
    });

    // Click ra ngoài để đóng menu
    document.addEventListener('click', () => {
        menu.classList.remove('show');
    });

    // Ngăn menu bị đóng khi click vào bên trong nó
    menu.addEventListener('click', (e) => {
        e.stopPropagation();
    });
});


document.addEventListener('DOMContentLoaded', function() {
    // Khai báo các vùng nội dung
    const dashboardView = document.getElementById('dashboard-view'); 
    const profileView = document.getElementById('profile-view');     
    const btnBack = document.getElementById('btnBackToAdmin');
    const navItems = document.querySelectorAll('.nav-item');

    // Hàm thực hiện quay lại
    btnBack.onclick = function() {

        profileView.style.display = 'none';
        dashboardView.style.display = 'block';

        navItems.forEach(item => item.classList.remove('active'));
        document.querySelector('[data-target="tong-quan"]').classList.add('active');
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };
});







// Sử lý menu
const menuBtn = document.getElementById('mobile-menu-btn');
const sidebar = document.querySelector('.sidebar');

if (menuBtn) {
    menuBtn.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });
}

// Bấm ra ngoài Sidebar thì tự đóng lại
document.addEventListener('click', (e) => {
    if (!sidebar.contains(e.target) && !menuBtn.contains(e.target)) {
        sidebar.classList.remove('active');
    }
});