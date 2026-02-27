

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

