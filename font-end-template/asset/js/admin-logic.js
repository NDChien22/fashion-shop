
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


document.addEventListener('DOMContentLoaded', function() {
    const navItems = document.querySelectorAll('.nav-item');
    const tabContents = document.querySelectorAll('.tab-content');
    const pageTitle = document.getElementById('page-title');
    const breadcrumbActive = document.getElementById('breadcrumb-active');
    
    // Sử dụng querySelector để tìm khối header
    const pageHeader = document.querySelector('.ff-page-header');

    navItems.forEach(item => {
        item.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const targetName = this.querySelector('span').innerText;

            // 1. Xử lý Active Sidebar
            navItems.forEach(nav => nav.classList.remove('active'));
            this.classList.add('active');

            // 2. Ẩn tất cả tab-content
            tabContents.forEach(content => {
                content.style.display = 'none';
            });

            // 3. Hiện tab-content được chọn
            const activeContent = document.getElementById(targetId);
            if (activeContent) {
                activeContent.style.display = 'block';
            }

            // 4. ĐIỀU KIỆN QUAN TRỌNG: Kiểm tra trang Tổng quan
            if (targetId === 'tong-quan') {
                // Nếu là Tổng quan thì ẩn hẳn header đi
                if (pageHeader) pageHeader.style.setProperty('display', 'none', 'important');
            } else {
                // Nếu KHÔNG PHẢI Tổng quan thì buộc phải hiện lại
                if (pageHeader) {
                    pageHeader.style.setProperty('display', 'block', 'important');
                    
                    // Cập nhật nội dung tiêu đề sau khi đã hiện
                    if (pageTitle) pageTitle.innerText = "Quản lý " + targetName.toLowerCase();
                    if (breadcrumbActive) breadcrumbActive.innerText = targetName;
                }
            }
        });
    });

    // Kiểm tra mặc định khi vừa load trang
    const currentActive = document.querySelector('.nav-item.active');
    if (currentActive && currentActive.getAttribute('data-target') === 'tong-quan') {
        if (pageHeader) pageHeader.style.display = 'none';
    }
});