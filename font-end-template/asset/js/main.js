const PAGES = {
    'trang-chu': { title: 'Trang chủ', file: 'home.html' },
    'gioi-thieu': { title: 'Giới thiệu', file: 'introduce.html' },
    'san-pham': { title: 'Tất cả sản phẩm', file: 'product_list.html' },
    'Bo-suu-tap': { title: 'Bộ sưu tập', file: 'collection.html' },
    "Don-hang": { title: 'Đơn hàng', file: 'orders.html' },
    'Ho-tro': { title: 'Hỗ trợ', file: 'support.html' },
    'Lien-he': { title: 'Liên hệ', file: 'contact.html' },
};

let countdownInterval = null;

// --- 1. Hàm đếm ngược (Countdown) ---
function startCountdown() {
    if (countdownInterval) clearInterval(countdownInterval);
    const h = document.getElementById('hours'), m = document.getElementById('minutes'), s = document.getElementById('seconds');
    if (!h || !m || !s) return;

    let hh = 2, mm = 45, ss = 0;
    countdownInterval = setInterval(() => {
        if (ss > 0) ss--;
        else {
            if (mm > 0) { mm--; ss = 59; }
            else if (hh > 0) { hh--; mm = 59; ss = 59; }
        }
        h.innerText = String(hh).padStart(2, '0');
        m.innerText = String(mm).padStart(2, '0');
        s.innerText = String(ss).padStart(2, '0');
        if (hh === 0 && mm === 0 && ss === 0) clearInterval(countdownInterval);
    }, 1000);
}

// --- 2. Hàm tải trang chính (Load Page) ---
async function loadPage() {
    const content = document.getElementById("content-area");
    if (!content) return;

    let hash = window.location.hash || '#trang-chu';
    let pageId = hash.replace('#', '') || 'trang-chu';

    const config = PAGES[pageId];
    if (!config) {
        content.innerHTML = `<div class="p-20 text-center text-gray-500">Trang đang cập nhật...</div>`;
        return;
    }

    try {
        content.style.opacity = "0"; // Hiệu ứng mờ dần
        const res = await fetch(config.file);
        if (!res.ok) throw new Error("404");
        const html = await res.text();

        setTimeout(() => {
            content.innerHTML = html;
            content.style.opacity = "1";
            content.style.transition = "opacity 0.3s ease";

            updateUI(pageId, config.title);
            runPageScript(pageId);
            window.scrollTo({ top: 0, behavior: 'smooth' }); // Cuộn lên đầu trang khi đổi trang
        }, 150);

    } catch (err) {
        console.error("Lỗi fetch:", err);
        content.innerHTML = `<div class="p-20 text-center">Không thể tải nội dung. Vui lòng thử lại.</div>`;
    }
}

// --- 3. Hàm chạy Script riêng cho từng trang ---
function runPageScript(pageId) {
    if (pageId === 'trang-chu') {
        
    }
    if (pageId === 'gioi-thieu') {
        startCountdown();
    }
    if (pageId === 'Ho-tro') {
        console.log("Đã tải trang Hỗ trợ - Đang khởi tạo FAQ...");
        initFAQ(); // Gọi hàm khởi tạo
    }
}

// --- 4. Cập nhật Giao diện (Breadcrumb & Active Menu) ---
function updateUI(pageId, title) {
    const bcArea = document.getElementById("breadcrumb-area");
    const bcCurrent = document.getElementById("breadcrumb-current");
    const contentArea = document.getElementById("content-area"); // Cần lấy thêm element này

   
    if (bcArea && contentArea) {
        if (pageId === 'trang-chu') {
            bcArea.classList.add('hidden');
            
            
            contentArea.style.marginTop = "144px"; 
        } else {
            bcArea.classList.remove('hidden'); 
            
            contentArea.style.marginTop = "0px"; 

            if (bcCurrent) {
                bcCurrent.innerHTML = `
                    <span class="mx-2 text-gray-300">/</span>
                    <span class="text-[#bc9c75] font-bold">${title}</span>
                `;
            }
        }
    }

    document.querySelectorAll('.menu-link').forEach(link => {
        const href = link.getAttribute('href');
        const isActive = (href === `#${pageId}`);

        if (isActive) {
            link.classList.add('text-[#bc9c75]', 'border-b-2', 'border-[#bc9c75]');
            link.classList.remove('text-gray-800');
        } else {
            link.classList.remove('text-[#bc9c75]', 'border-b-2', 'border-[#bc9c75]');
            link.classList.add('text-gray-800');
        }
    });
}
// --- 5. Sự kiện khởi chạy ---
window.addEventListener('hashchange', loadPage);
document.addEventListener('DOMContentLoaded', loadPage);




function toggleAccordion(btn) {
  // Tìm tất cả các nội dung đang mở và đóng chúng lại
  document.querySelectorAll('.accordion-content').forEach(el => {
    if (el !== btn.nextElementSibling) {
      el.classList.add('hidden');
      el.previousElementSibling.querySelector('svg').classList.remove('rotate-180');
    }
  });

  // Mở mục hiện tại
  const content = btn.nextElementSibling;
  const icon = btn.querySelector('svg');
  content.classList.toggle('hidden');
  icon.classList.toggle('rotate-180');
}



function initFAQ() {
    // --- PHẦN 1: XỬ LÝ ĐÓNG/MỞ FAQ (CHO PHÉP MỞ NHIỀU MỤC) ---
    const buttons = document.querySelectorAll('.accordion-item button');

    buttons.forEach(btn => {
        // Xóa sự kiện cũ bằng cách clone để tránh trùng lặp
        btn.replaceWith(btn.cloneNode(true));
    });

    const newButtons = document.querySelectorAll('.accordion-item button');
    newButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const item = this.closest('.accordion-item');
            const content = item.querySelector('.accordion-content');
            const icon = this.querySelector('svg');
            
            // Kiểm tra trạng thái hiện tại của mục được nhấn
            const isOpen = item.classList.contains('active-faq');

            if (isOpen) {
                // Nếu đang mở thì ĐÓNG LẠI
                item.classList.remove('active-faq');
                content.style.maxHeight = null;
                if (icon) icon.classList.remove('rotate-180');
            } else {
                // Nếu đang đóng thì MỞ RA (Không can thiệp vào các mục khác)
                item.classList.add('active-faq');
                content.style.maxHeight = content.scrollHeight + "px";
                if (icon) icon.classList.add('rotate-180');
            }
        });
    });

    // --- PHẦN 2: XỬ LÝ CUỘN MƯỢT CHO NỘI DUNG CHÍNH ---
    const navLinks = document.querySelectorAll('a[href^="#"]'); 
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);

            if (targetElement) {
                e.preventDefault(); 

                const headerOffset = 150; 
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: "smooth"
                });
            }
        });
    });
}