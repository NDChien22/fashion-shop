
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



const input = document.getElementById('dobInput');

input.addEventListener('input', function(e) {
    let cursorPosition = e.target.selectionStart;
    let value = e.target.value.replace(/\D/g, '');
    let formattedValue = "";

    if (value.length > 0) {
        // 1. Xử lý NGÀY: Nếu gõ số 4-9 thì tự biến thành 04-09
        let day = value.substring(0, 2);
        if (day.length === 1 && parseInt(day) > 3) {
            day = '0' + day;
            value = day + value.substring(1);
        }
        formattedValue = day;

        // 2. Xử lý THÁNG: Nếu gõ số 2-9 cho tháng thì tự biến thành 02-09
        if (value.length > 2) {
            let month = value.substring(2, 4);
            if (month.length === 1 && parseInt(month) > 1) {
                month = '0' + month;
                value = value.substring(0, 2) + month + value.substring(3);
            }
            formattedValue += '/' + month;
        }

        if (value.length > 4) {
            formattedValue += '/' + value.substring(4, 8);
        }
    }

    e.target.value = formattedValue;

    if (e.inputType === 'deleteContentBackward') {
        e.target.setSelectionRange(cursorPosition, cursorPosition);
    }
});