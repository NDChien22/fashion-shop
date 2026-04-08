const products = [
  // THỜI TRANG NỮ - VÁY ĐẦM
  { id: 1, name: "Váy hoa nhí dáng dài Vintage", category: "Váy đầm", price: 350000, oldPrice: 450000, image: "https://picsum.photos/id/1/500/700", tag: "Mới" },
  { id: 2, name: "Đầm suông linen phối túi", category: "Váy đầm", price: 290000, oldPrice: null, image: "https://picsum.photos/id/2/500/700", tag: "" },
  { id: 3, name: "Chân váy xếp ly Tennis", category: "Váy đầm", price: 199000, oldPrice: 250000, image: "https://picsum.photos/id/3/500/700", tag: "Sale" },
  { id: 4, name: "Váy body ôm sát gợi cảm", category: "Váy đầm", price: 420000, oldPrice: 550000, image: "https://picsum.photos/id/4/500/700", tag: "Bán chạy" },
  { id: 5, name: "Đầm dự tiệc trễ vai", category: "Váy đầm", price: 890000, oldPrice: null, image: "https://picsum.photos/id/5/500/700", tag: "Mới" },

  // THỜI TRANG NỮ - ÁO
  { id: 6, name: "Áo sơ mi lụa công sở", category: "Áo sơ mi", price: 250000, oldPrice: 320000, image: "https://picsum.photos/id/10/500/700", tag: "" },
  { id: 7, name: "Áo thun Cotton basic", category: "Áo thun", price: 150000, oldPrice: null, image: "https://picsum.photos/id/11/500/700", tag: "Mới" },
  { id: 8, name: "Áo len lưới croptop Vienne", category: "Áo thun", price: 249000, oldPrice: 368000, image: "https://picsum.photos/id/12/500/700", tag: "Sale" },
  { id: 9, name: "Áo khoác Blazer nữ Hàn Quốc", category: "Áo khoác", price: 705000, oldPrice: 850000, image: "https://picsum.photos/id/13/500/700", tag: "Bán chạy" },
  { id: 10, name: "Áo hai dây lụa satin", category: "Áo thun", price: 120000, oldPrice: 180000, image: "https://picsum.photos/id/14/500/700", tag: "" },

  // THỜI TRANG NAM - ÁO
  { id: 11, name: "Áo sơ mi Oxford nam", category: "Áo nam", price: 380000, oldPrice: 450000, image: "https://picsum.photos/id/20/500/700", tag: "Mới" },
  { id: 12, name: "Áo thun Polo phối cổ", category: "Áo nam", price: 280000, oldPrice: null, image: "https://picsum.photos/id/21/500/700", tag: "" },
  { id: 13, name: "Áo Hoodie nỉ ngoại", category: "Áo nam", price: 450000, oldPrice: 520000, image: "https://picsum.photos/id/22/500/700", tag: "Bán chạy" },
  { id: 14, name: "Áo khoác Jean Denim Classic", category: "Áo nam", price: 650000, oldPrice: null, image: "https://picsum.photos/id/23/500/700", tag: "Mới" },
  { id: 15, name: "Áo Tanktop tập gym", category: "Áo nam", price: 160000, oldPrice: 210000, image: "https://picsum.photos/id/24/500/700", tag: "Sale" },

  // THỜI TRANG NAM - QUẦN
  { id: 16, name: "Quần Jean Slim-fit nam", category: "Quần nam", price: 490000, oldPrice: 600000, image: "https://picsum.photos/id/30/500/700", tag: "" },
  { id: 17, name: "Quần Kaki túi hộp Cargo", category: "Quần nam", price: 350000, oldPrice: null, image: "https://picsum.photos/id/31/500/700", tag: "Mới" },
  { id: 18, name: "Quần Short thun thể thao", category: "Quần nam", price: 180000, oldPrice: 250000, image: "https://picsum.photos/id/32/500/700", tag: "Sale" },
  { id: 19, name: "Quần Tây Âu công sở", category: "Quần nam", price: 550000, oldPrice: null, image: "https://picsum.photos/id/33/500/700", tag: "" },
  { id: 20, name: "Quần Jogger nỉ nam", category: "Quần nam", price: 320000, oldPrice: 380000, image: "https://picsum.photos/id/34/500/700", tag: "" },

  // PHỤ KIỆN
  { id: 21, name: "Túi xách da nữ cao cấp", category: "Phụ kiện", price: 1250000, oldPrice: 1500000, image: "https://picsum.photos/id/40/500/700", tag: "Bán chạy" },
  { id: 22, name: "Thắt lưng da nam khóa kim", category: "Phụ kiện", price: 290000, oldPrice: null, image: "https://picsum.photos/id/41/500/700", tag: "Mới" },
  { id: 23, name: "Mũ lưỡi trai phối lưới", category: "Phụ kiện", price: 150000, oldPrice: 190000, image: "https://picsum.photos/id/42/500/700", tag: "" },
  { id: 24, name: "Tất nam cổ cao basic (set 5)", category: "Phụ kiện", price: 99000, oldPrice: 150000, image: "https://picsum.photos/id/43/500/700", tag: "Sale" },
  { id: 25, name: "Kính mát phi công thời trang", category: "Phụ kiện", price: 450000, oldPrice: null, image: "https://picsum.photos/id/44/500/700", tag: "Mới" },

  // SẢN PHẨM BỔ SUNG ĐỂ ĐỦ 30
  { id: 26, name: "Áo sơ mi họa tiết biển", category: "Áo nam", price: 320000, oldPrice: null, image: "https://picsum.photos/id/50/500/700", tag: "" },
  { id: 27, name: "Váy len ôm body mùa đông", category: "Váy đầm", price: 580000, oldPrice: 650000, image: "https://picsum.photos/id/51/500/700", tag: "Bán chạy" },
  { id: 28, name: "Quần legging nữ tập yoga", category: "Quần nữ", price: 220000, oldPrice: null, image: "https://picsum.photos/id/52/500/700", tag: "Mới" },
  { id: 29, name: "Áo khoác phao siêu nhẹ", category: "Áo khoác", price: 950000, oldPrice: 1200000, image: "https://picsum.photos/id/53/500/700", tag: "Sale" },
  { id: 30, name: "Túi canvas đi học đơn giản", category: "Phụ kiện", price: 85000, oldPrice: null, image: "https://picsum.photos/id/54/500/700", tag: "" }
];




// Cấu hình phân trang toàn cục
let currentPage = 1;
const productsPerPage = 12;

function renderProducts(productList) {
    // 1. Kiểm tra dữ liệu đầu vào
    if (!productList || !Array.isArray(productList)) {
        console.error("Dữ liệu sản phẩm không hợp lệ");
        return;
    }

    const grid = document.getElementById('product-grid');
    const countLabel = document.getElementById('product-count');
    
    if (!grid) {
        console.warn("Không tìm thấy thẻ #product-grid.");
        return;
    }

    // --- LOGIC PHÂN TRANG ---
    // Xác định sản phẩm cần hiển thị cho trang hiện tại
    const startIndex = (currentPage - 1) * productsPerPage;
    const endIndex = startIndex + productsPerPage;
    const paginatedItems = productList.slice(startIndex, endIndex);

    // 2. Cập nhật số lượng hiển thị (Tổng số sản phẩm)
    if (countLabel) {
        countLabel.innerText = `Hiển thị ${paginatedItems.length} trên ${productList.length} sản phẩm`;
    }

    // 3. Render HTML cho danh sách sản phẩm đã cắt (paginatedItems)
    grid.innerHTML = paginatedItems.map(item => {
        const tagHTML = item.tag 
            ? `<div class="absolute top-3 left-3 z-10">
                 <span class="bg-orange-500 text-white text-[10px] px-2.5 py-1 rounded-full uppercase font-bold shadow">${item.tag}</span>
               </div>` 
            : '';
            
        const oldPriceHTML = item.oldPrice 
            ? `<p class="text-xs text-gray-400 line-through">${item.oldPrice.toLocaleString('vi-VN')}₫</p>` 
            : '';

        return `
            <div class="product-card group relative">
                <div class="overflow-hidden mb-4 relative rounded-xl aspect-3/4 bg-gray-100 shadow-sm">
                    <img src="${item.image}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="${item.name}">
                    ${tagHTML}
                    <div class="absolute inset-x-0 bottom-4 px-4 flex flex-col gap-3 translate-y-8 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300 z-10">
                        <div class="flex justify-center gap-2">
                            <button class="bg-white/95 text-gray-800 p-2 rounded-full shadow-md hover:bg-[#bc9c75] hover:text-white transition transform hover:scale-110">
                                <i class="ri-eye-line text-base"></i>
                            </button>
                            <button class="bg-white/95 text-gray-800 p-2 rounded-full shadow-md hover:bg-[#bc9c75] hover:text-white transition transform hover:scale-110">
                                <i class="ri-heart-line text-base"></i>
                            </button>
                        </div>
                        <button onclick="addToCart(${item.id})" class="w-full bg-white text-black py-2.5 text-[10px] font-bold uppercase hover:bg-[#bc9c75] hover:text-white rounded-lg shadow-md transition-colors">
                            Thêm vào giỏ
                        </button>
                    </div>
                </div>
                <h4 class="text-sm font-medium text-gray-800 mb-1.5 group-hover:text-[#bc9c75] transition-colors line-clamp-2">
                    ${item.name}
                </h4>
                <div class="flex items-center gap-3">
                    <p class="font-bold text-[#bc9c75] text-lg">${item.price.toLocaleString('vi-VN')}₫</p>
                    ${oldPriceHTML}
                </div>
            </div>
        `;
    }).join('');

    // 4. Tạo các nút phân trang
    renderPagination(productList);
}

// Hàm tạo nút số trang
function renderPagination(productList) {
    const paginationContainer = document.getElementById('pagination-container');
    if (!paginationContainer) return;

    const totalPages = Math.ceil(productList.length / productsPerPage);
    let html = '';

    if (currentPage > 1) {
        html += `
            <button onclick="goToPage(${currentPage - 1})" 
                class="w-10 h-10 flex items-center justify-center border-2 border-[#bc9c75] text-[#bc9c75] rounded-lg font-bold hover:bg-[#bc9c75] hover:text-white transition-all">
                «
            </button>
        `;
    }

    for (let i = 1; i <= totalPages; i++) {
        const activeClass = i === currentPage 
            ? 'bg-[#bc9c75] text-white border-[#bc9c75]' 
            : 'bg-white text-[#bc9c75] border-[#bc9c75] hover:bg-[#bc9c75] hover:text-white';

        html += `
            <button onclick="goToPage(${i})" class="w-10 h-10 flex items-center justify-center border-2 rounded-lg font-bold transition-all ${activeClass}">
                ${i}
            </button>
        `;
    }

    // Thêm nút "Tiếp theo" nếu chưa ở trang cuối
    if (currentPage < totalPages) {
        html += `
            <button onclick="goToPage(${currentPage + 1})" class="w-10 h-10 flex items-center justify-center border-2 border-[#bc9c75] text-[#bc9c75] rounded-lg font-bold hover:bg-[#bc9c75] hover:text-white transition-all">
                »
            </button>
        `;
    }

    paginationContainer.innerHTML = html;
}

// Hàm chuyển trang
function goToPage(pageNumber) {
    currentPage = pageNumber; // Phải có dòng này để cập nhật số trang hiện tại
    renderProducts(products); // Sau đó gọi lại hàm render với dữ liệu gốc
}






const PAGES = {
    'trang-chu': { title: 'Trang chủ', file: 'home.html' },
    'gioi-thieu': { title: 'Giới thiệu', file: 'introduce.html' },
    'san-pham': { title: 'Tất cả sản phẩm', file: 'product.html' },
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

            if (pageId === 'san-pham' || config.file.includes('product.html')) {
                // Gọi hàm render từ dữ liệu mảng products đã có
                if (typeof renderProducts === "function") {
                    renderProducts(products); 
                }
            }

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