const products = [
  // THỜI TRANG NỮ - VÁY ĐẦM
  { id: 1, name: "Váy hoa nhí dáng dài Vintage", category: "Váy đầm", price: 350000, oldPrice: 450000, image: "https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=500&q=80", tag: "Mới" },
  { id: 2, name: "Đầm suông linen phối túi", category: "Váy đầm", price: 290000, oldPrice: null, image: "https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=500&q=80", tag: "" },
  { id: 3, name: "Chân váy xếp ly Tennis", category: "Váy đầm", price: 199000, oldPrice: 250000, image: "https://images.unsplash.com/photo-1583337130417-3346a1be7dee?w=500&q=80", tag: "Sale" },
  { id: 4, name: "Váy body ôm sát gợi cảm", category: "Váy đầm", price: 420000, oldPrice: 550000, image: "https://images.unsplash.com/photo-1539008835657-9e8e82165a5c?w=500&q=80", tag: "Bán chạy" },
  { id: 5, name: "Đầm dự tiệc trễ vai", category: "Váy đầm", price: 890000, oldPrice: null, image: "https://images.unsplash.com/photo-1566174053879-31528523f8ae?w=500&q=80", tag: "Mới" },

  // THỜI TRANG NỮ - ÁO
  { id: 6, name: "Áo sơ mi lụa công sở", category: "Áo sơ mi", price: 250000, oldPrice: 320000, image: "https://images.unsplash.com/photo-1598554747436-c9293d6a588f?w=500&q=80", tag: "" },
  { id: 7, name: "Áo thun Cotton basic", category: "Áo thun", price: 150000, oldPrice: null, image: "https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=500&q=80", tag: "Mới" },
  { id: 8, name: "Áo len lưới croptop Vienne", category: "Áo thun", price: 249000, oldPrice: 368000, image: "https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=500&q=80", tag: "Sale" },
  { id: 9, name: "Áo khoác Blazer nữ Hàn Quốc", category: "Áo khoác", price: 705000, oldPrice: 850000, image: "https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=500&q=80", tag: "Bán chạy" },
  { id: 10, name: "Áo hai dây lụa satin", category: "Áo thun", price: 120000, oldPrice: 180000, image: "https://images.unsplash.com/photo-1604176354204-926873ff3da9?w=500&q=80", tag: "" },

  // THỜI TRANG NAM - ÁO
  { id: 11, name: "Áo sơ mi Oxford nam", category: "Áo nam", price: 380000, oldPrice: 450000, image: "https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=500&q=80", tag: "Mới" },
  { id: 12, name: "Áo thun Polo phối cổ", category: "Áo nam", price: 280000, oldPrice: null, image: "https://images.unsplash.com/photo-1624371414361-e6e0ed58d38c?w=500&q=80", tag: "" },
  { id: 13, name: "Áo Hoodie nỉ ngoại", category: "Áo nam", price: 450000, oldPrice: 520000, image: "https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=500&q=80", tag: "Bán chạy" },
  { id: 14, name: "Áo khoác Jean Denim Classic", category: "Áo nam", price: 650000, oldPrice: null, image: "https://images.unsplash.com/photo-1516257984877-a03a804f7a7a?w=500&q=80", tag: "Mới" },
  { id: 15, name: "Áo Tanktop tập gym", category: "Áo nam", price: 160000, oldPrice: 210000, image: "https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=500&q=80", tag: "Sale" },

  // THỜI TRANG NAM - QUẦN
  { id: 16, name: "Quần Jean Slim-fit nam", category: "Quần nam", price: 490000, oldPrice: 600000, image: "https://images.unsplash.com/photo-1542272604-787c3835535d?w=500&q=80", tag: "" },
  { id: 17, name: "Quần Kaki túi hộp Cargo", category: "Quần nam", price: 350000, oldPrice: null, image: "https://images.unsplash.com/photo-1617137968427-85924c800a22?w=500&q=80", tag: "Mới" },
  { id: 18, name: "Quần Short thun thể thao", category: "Quần nam", price: 180000, oldPrice: 250000, image: "https://images.unsplash.com/photo-1591195853828-11db59a44f6b?w=500&q=80", tag: "Sale" },
  { id: 19, name: "Quần Tây Âu công sở", category: "Quần nam", price: 550000, oldPrice: null, image: "https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=500&q=80", tag: "" },
  { id: 20, name: "Quần Jogger nỉ nam", category: "Quần nam", price: 320000, oldPrice: 380000, image: "https://images.unsplash.com/photo-1620799140408-edc6dcb6d633?w=500&q=80", tag: "" },

  // PHỤ KIỆN
  { id: 21, name: "Túi xách da nữ cao cấp", category: "Phụ kiện", price: 1250000, oldPrice: 1500000, image: "https://images.unsplash.com/photo-1584917033904-493bb3c3cc08?w=500&q=80", tag: "Bán chạy" },
  { id: 22, name: "Thắt lưng da nam khóa kim", category: "Phụ kiện", price: 290000, oldPrice: null, image: "https://images.unsplash.com/photo-1624222247344-550fb60583dc?w=500&q=80", tag: "Mới" },
  { id: 23, name: "Mũ lưỡi trai phối lưới", category: "Phụ kiện", price: 150000, oldPrice: 190000, image: "https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=500&q=80", tag: "" },
  { id: 24, name: "Tất nam cổ cao basic (set 5)", category: "Phụ kiện", price: 99000, oldPrice: 150000, image: "https://images.unsplash.com/photo-1582966772640-62507632669e?w=500&q=80", tag: "Sale" },
  { id: 25, name: "Kính mát phi công thời trang", category: "Phụ kiện", price: 450000, oldPrice: null, image: "https://images.unsplash.com/photo-1511499767350-a1511f02fbf3?w=500&q=80", tag: "Mới" },

  // SẢN PHẨM BỔ SUNG
  { id: 26, name: "Áo sơ mi họa tiết biển", category: "Áo nam", price: 320000, oldPrice: null, image: "https://images.unsplash.com/photo-1523381210434-271e8be1f52b?w=500&q=80", tag: "" },
  { id: 27, name: "Váy len ôm body mùa đông", category: "Váy đầm", price: 580000, oldPrice: 650000, image: "https://images.unsplash.com/photo-1612817288484-6f916006741a?w=500&q=80", tag: "Bán chạy" },
  { id: 28, name: "Quần legging nữ tập yoga", category: "Quần nữ", price: 220000, oldPrice: null, image: "https://images.unsplash.com/photo-1506629082955-511b1aa562c8?w=500&q=80", tag: "Mới" },
  { id: 29, name: "Áo khoác phao siêu nhẹ", category: "Áo khoác", price: 950000, oldPrice: 1200000, image: "https://images.unsplash.com/photo-1544022613-e87ca75a784a?w=500&q=80", tag: "Sale" },
  { id: 30, name: "Túi canvas đi học đơn giản", category: "Phụ kiện", price: 85000, oldPrice: null, image: "https://images.unsplash.com/photo-1544816155-12df9643f363?w=500&q=80", tag: "" }
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
        const wishlist = JSON.parse(localStorage.getItem('vienne_wishlist')) || [];
        const isWishlisted = wishlist.includes(item.id);
        const tagHTML = item.tag 
            ? `<div class="absolute top-3 left-3 z-10">
                 <span class="bg-orange-500 text-white text-[10px] px-2.5 py-1 rounded-full uppercase font-bold shadow">${item.tag}</span>
               </div>` 
            : '';
            
        const oldPriceHTML = item.oldPrice 
            ? `<p class="text-xs text-gray-400 line-through">${item.oldPrice.toLocaleString('vi-VN')}₫</p>` 
            : '';

        return `
            <div class="product-card group relative flex flex-col h-full">
                <div class="relative overflow-hidden mb-4 rounded-xl bg-gray-100 shadow-sm aspect-[3/4]">
                    <img 
                        src="${item.image}" 
                        
                        class="w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-105" 
                        alt="${item.name}"
                    >
                    
                    ${tagHTML}

                    <div class="absolute inset-x-0 bottom-4 px-4 flex flex-col gap-3 translate-y-8 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300 z-10">
                        <div class="flex justify-center gap-2">
                            <button class="bg-white/95 text-gray-800 p-2 rounded-full shadow-md hover:bg-[#bc9c75] hover:text-white transition transform hover:scale-110">
                                <i class="ri-eye-line text-base"></i>
                            </button>
                            <button onclick="toggleWishlist(${item.id})"  class="bg-white/95 p-2 rounded-full shadow-md transition transform hover:scale-110
                                ${isWishlisted ? 'text-red-500' : 'text-gray-800'} hover:bg-[#bc9c75] hover:text-white">
                                <i class="${isWishlisted ? 'ri-heart-fill' : 'ri-heart-line'} text-base"></i>

                            </button>
                        </div>
                        <button onclick="addToCart(${item.id})" class="w-full bg-white text-black py-2.5 text-[10px] font-bold uppercase hover:bg-[#bc9c75] hover:text-white rounded-lg shadow-md">
                            Thêm vào giỏ
                        </button>
                    </div>
                </div>

                <div class="flex flex-col grow">
                    <h4 class="text-sm font-medium text-gray-800 mb-1.5 line-clamp-2 min-h-10">
                        ${item.name}
                    </h4>
                    <div class="flex items-center gap-3 mt-auto">
                        <p class="font-bold text-[#bc9c75] text-lg">${item.price.toLocaleString('vi-VN')}₫</p>
                        ${oldPriceHTML}
                    </div>
                </div>
            </div>
        `;
    }).join('');

    // 4. Tạo các nút phân trang
    renderPagination(productList);
}



function addToCart(productId) {
    // 1. Lấy giỏ hàng từ localStorage hoặc tạo mới
    let cart = JSON.parse(localStorage.getItem('vienne_cart')) || [];
    
    // 2. Tìm sản phẩm trong giỏ (giả sử bạn có mảng dữ liệu gốc là allProducts)
    // Trong thực tế SPA, bạn thường tìm sản phẩm theo ID từ dữ liệu đã fetch
    const existingProduct = cart.find(item => item.id === productId);

    if (existingProduct) {
        existingProduct.quantity += 1;
    } else {
        cart.push({ id: productId, quantity: 1 });
    }

    // 3. Lưu lại
    localStorage.setItem('vienne_cart', JSON.stringify(cart));

    // 4. Cập nhật con số trên icon giỏ hàng (Badge)
    if (typeof updateCartBadge === 'function') {
        updateCartBadge();
    }

    // 5. Hiển thị thông báo (Toast)
    alert("Đã thêm sản phẩm vào giỏ hàng!");
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






// --- XỬ LÝ TRANG GIỎ HÀNG (FULL PAGE) ---

// Biến lưu trữ ID các sản phẩm được chọn
let selectedItems = [];

function renderFullCartPage() {
    const container = document.getElementById('cart-content-page');
    const totalEl = document.getElementById('page-cart-total');
    if (!container) return;

    let cart = JSON.parse(localStorage.getItem('vienne_cart')) || [];
    
    if (cart.length === 0) {
        container.innerHTML = `<div class="text-center py-10">Giỏ hàng trống</div>`;
        totalEl.innerText = "0₫";
        return;
    }

    let totalPrice = 0;
    container.innerHTML = cart.map(item => {
        const product = products.find(p => p.id === item.id);
        if (!product) return '';
        
        // Kiểm tra xem sản phẩm này có đang được chọn không
        const isChecked = selectedItems.includes(product.id) ? 'checked' : '';
        
        // Chỉ tính tiền nếu sản phẩm được tích chọn
        if (selectedItems.includes(product.id)) {
            totalPrice += product.price * item.quantity;
        }

        return `
            <div class="flex items-center gap-4 bg-white p-4 rounded-xl border mb-4 shadow-sm">
                <input type="checkbox" ${isChecked} 
                    class="w-5 h-5 accent-[#bc9c75] cursor-pointer" 
                    onclick="toggleSelectItem(${product.id})">
                
                <img src="${product.image}" class="w-20 h-24 object-cover rounded-lg">
                
                <div class="flex-1">
                    <h4 class="font-bold text-gray-800">${product.name}</h4>
                    <p class="text-[#bc9c75] font-bold">${product.price.toLocaleString('vi-VN')}₫</p>
                </div>

                <div class="flex items-center border rounded-lg">
                    <button onclick="changePageCartQty(${product.id}, -1)" class="px-2 py-1">-</button>
                    <span class="px-3 font-bold text-sm">${item.quantity}</span>
                    <button onclick="changePageCartQty(${product.id}, 1)" class="px-2 py-1">+</button>
                </div>
                
                <button onclick="removePageCartItem(${product.id})" class="text-gray-300 hover:text-red-500 ml-2">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        `;
    }).join('');

    totalEl.innerText = totalPrice.toLocaleString('vi-VN') + "₫";
}

// Hàm xử lý khi bấm vào Checkbox
window.toggleSelectItem = function(id) {
    const index = selectedItems.indexOf(id);
    if (index > -1) {
        // Nếu đã có thì bỏ chọn
        selectedItems.splice(index, 1);
    } else {
        // Nếu chưa có thì thêm vào danh sách chọn
        selectedItems.push(id);
    }
    renderFullCartPage(); // Vẽ lại để cập nhật tổng tiền
}



// Hàm thay đổi số lượng trên trang
window.changePageCartQty = function(id, delta) {
    let cart = JSON.parse(localStorage.getItem('vienne_cart')) || [];
    const item = cart.find(i => i.id === id);
    
    if (item) {
        // KIỂM TRA: Nếu đang là 1 mà nhấn giảm (delta = -1) thì không làm gì cả
        if (item.quantity === 1 && delta === -1) {
            console.log("Số lượng tối thiểu là 1");
            return; 
        }
        // Ngược lại thì mới thực hiện cộng/trừ
        item.quantity += delta;
        
        // Lưu và vẽ lại giao diện
        localStorage.setItem('vienne_cart', JSON.stringify(cart));
        renderFullCartPage();
        
        if (typeof updateCartBadge === 'function') updateCartBadge();
    }
}

// Hàm xóa sản phẩm trên trang
window.removePageCartItem = function(id) {
    let cart = JSON.parse(localStorage.getItem('vienne_cart')) || [];
    cart = cart.filter(i => i.id !== id);
    localStorage.setItem('vienne_cart', JSON.stringify(cart));
    renderFullCartPage();
    if (typeof updateCartBadge === 'function') updateCartBadge();
}

function updateCartBadge() {
    // 1. Lấy dữ liệu giỏ hàng từ localStorage
    const cart = JSON.parse(localStorage.getItem('vienne_cart')) || [];
    
    // 2. Tìm phần tử badge hiển thị số lượng trên Header
    const badge = document.getElementById('cart-count'); 
    
    if (badge) {
        // 3. Tính tổng số lượng của tất cả mặt hàng (ví dụ: mua 2 áo + 1 quần = 3)
        // Nếu bạn chỉ muốn đếm số loại sản phẩm khác nhau, dùng: cart.length
        const totalQuantity = cart.reduce((total, item) => total + item.quantity, 0);
        
        // 4. Cập nhật số hiển thị
        badge.innerText = totalQuantity;
        
        // 5. Hiển thị badge nếu có hàng, ẩn nếu giỏ hàng trống (0)
        badge.style.display = totalQuantity > 0 ? 'flex' : 'none';
    }
}





// 1. Hàm Toggle (Thêm/Xóa)
window.toggleWishlist = function(id) {
    let wishlist = JSON.parse(localStorage.getItem('vienne_wishlist')) || [];
    const index = wishlist.indexOf(id);

    if (index === -1) {
        wishlist.push(id);
    } else {
        wishlist.splice(index, 1);
    }

    localStorage.setItem('vienne_wishlist', JSON.stringify(wishlist));

    if (document.getElementById('wishlist-grid')) {
        renderWishlistPage();
    }

    if (document.getElementById('product-grid')) {
        renderProducts(products);
    }

    updateWishlistBadge();
};

// 2. Hàm Render trang yêu thích
function renderWishlistPage() {
    const grid = document.getElementById('wishlist-grid');
    const emptyMsg = document.getElementById('wishlist-empty');
    if (!grid) return;

    let wishlist = JSON.parse(localStorage.getItem('vienne_wishlist')) || [];
    const favProducts = products.filter(p => wishlist.includes(p.id));

    if (favProducts.length === 0) {
        grid.innerHTML = '';
        emptyMsg.classList.remove('hidden');
        return;
    }

    emptyMsg.classList.add('hidden');
    // Tận dụng cấu trúc Card sản phẩm bạn đã có
    grid.innerHTML = favProducts.map(item => {
    // 1. Phải khai báo các biến bổ trợ này để không bị lỗi "undefined"
    const tagHTML = item.tag 
        ? `<div class="absolute top-3 left-3 z-10">
             <span class="bg-orange-500 text-white text-[10px] px-2.5 py-1 rounded-full uppercase font-bold shadow">${item.tag}</span>
           </div>` 
        : '';
        
    const oldPriceHTML = item.oldPrice 
        ? `<p class="text-xs text-gray-400 line-through">${item.oldPrice.toLocaleString('vi-VN')}₫</p>` 
        : '';

    // 2. Trả về cấu trúc HTML mới
    return `
        <div class="product-card group relative flex flex-col h-full">
            <div class="relative overflow-hidden mb-4 rounded-xl bg-gray-100 shadow-sm aspect-[3/4]">
                <img 
                    src="${item.image}" 
                    class="w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-105" 
                    alt="${item.name}"
                >
                ${tagHTML}

                <button onclick="toggleWishlist(${item.id})" class="absolute top-2 right-2 bg-white/90 p-2 rounded-full text-red-500 shadow-md hover:bg-red-50 transition-colors z-20">
                    <i class="ri-heart-fill"></i>
                </button>

                <div class="absolute inset-x-0 bottom-4 px-4 flex flex-col gap-3 translate-y-8 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300 z-10">
                    <button onclick="addToCart(${item.id})" class="w-full bg-white text-black py-2.5 text-[10px] font-bold uppercase hover:bg-[#bc9c75] hover:text-white rounded-lg shadow-md transition-colors">
                        Mua ngay
                    </button>
                </div>
            </div>

            <div class="flex flex-col grow px-1">
                <h4 class="text-sm font-medium text-gray-800 mb-1.5 line-clamp-2 min-h-10">
                    ${item.name}
                </h4>
                <div class="flex items-center gap-3 mt-auto">
                    <p class="font-bold text-[#bc9c75] text-lg">${item.price.toLocaleString('vi-VN')}₫</p>
                    ${oldPriceHTML}
                </div>
            </div>
        </div>
    `;
    }).join('');
}

function updateWishlistBadge() {
    const wishlist = JSON.parse(localStorage.getItem('vienne_wishlist')) || [];
    const badge = document.getElementById('wishlist-count');    
    if (badge) {
        badge.innerText = wishlist.length;
        badge.style.display = wishlist.length > 0 ? 'flex' : 'none';
    }
}

/**
 * Hàm đóng/mở Sidebar trên Mobile
 */
function toggleSidebar(isOpen) {
    const sidebar = document.getElementById('main-sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    
    if (isOpen) {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
    }
}


document.addEventListener('DOMContentLoaded', function() {
    const menuBtn = document.getElementById('menu-btn');
    const overlay = document.getElementById('sidebar-overlay');

    if (menuBtn) {
        menuBtn.onclick = function(e) {
            e.preventDefault();
            toggleSidebar(true);
        };
    }

    if (overlay) {
        overlay.onclick = function() {
            toggleSidebar(false);
        };
    }
});



// Sử dụng Event Delegation để xử lý cho SPA
document.addEventListener('click', function (e) {
    // Tìm phần tử .accordion-header gần nhất với vị trí click
    const header = e.target.closest('.accordion-header');
    
    // Nếu không nhấp vào header thì bỏ qua
    if (!header) return;

    // Tìm danh sách con (ul) và icon ngay trong header đó
    const content = header.nextElementSibling;
    const icon = header.querySelector('.accordion-icon');

    // Kiểm tra trạng thái đóng/mở
    const isOpen = content.style.maxHeight && content.style.maxHeight !== '0px';

    if (isOpen) {
        // ĐÓNG: Trả về 0
        content.style.maxHeight = '0px';
        if (icon) {
            icon.classList.replace('ri-subtract-line', 'ri-add-line');
        }
        header.classList.remove('text-[#bc9c75]');
    } else {
        // MỞ: Trước hết đóng tất cả các accordion khác (Tùy chọn)
        document.querySelectorAll('.accordion-content').forEach(el => {
            el.style.maxHeight = '0px';
            const otherHeader = el.previousElementSibling;
            if (otherHeader) {
                otherHeader.classList.remove('text-[#bc9c75]');
                const otherIcon = otherHeader.querySelector('.accordion-icon');
                if (otherIcon) otherIcon.classList.replace('ri-subtract-line', 'ri-add-line');
            }
        });

        // Mở cái hiện tại: Tính toán chiều cao thật bằng scrollHeight
        content.style.maxHeight = content.scrollHeight + 'px';
        if (icon) {
            icon.classList.replace('ri-add-line', 'ri-subtract-line');
        }
        header.classList.add('text-[#bc9c75]');
    }
});



document.addEventListener('DOMContentLoaded', () => {
    const chatBtn = document.getElementById('chat-toggle-btn');
    const supportBox = document.getElementById('support-box');
    const closeChatBtn = document.getElementById('close-chat-btn');
    const chatIcon = document.getElementById('chat-icon');

    // Hàm xử lý đóng mở
    function toggleChat() {
        // Kiểm tra xem hộp thoại có đang bị ẩn không
        const isHidden = supportBox.classList.contains('hidden');

        if (isHidden) {
            // HIỆN: Xóa class hidden và thêm hiệu ứng
            supportBox.classList.remove('hidden');
            // Đợi một chút để trình duyệt nhận diện rồi mới chạy hiệu ứng mượt
            setTimeout(() => {
                supportBox.classList.remove('opacity-0', 'translate-y-4');
                supportBox.classList.add('opacity-100', 'translate-y-0');
            }, 10);
            
            // Đổi icon sang dấu X (nếu có id chat-icon)
            if(chatIcon) chatIcon.classList.replace('ri-messenger-fill', 'ri-close-line');
        } else {
            // ẨN: Thêm lại các class ẩn
            supportBox.classList.add('opacity-0', 'translate-y-4');
            supportBox.classList.remove('opacity-100', 'translate-y-0');
            
            // Đợi hiệu ứng chạy xong rồi mới ẩn hẳn bằng hidden
            setTimeout(() => {
                supportBox.classList.add('hidden');
            }, 300);

            // Đổi icon lại thành Messenger
            if(chatIcon) chatIcon.classList.replace('ri-close-line', 'ri-messenger-fill');
        }
    }

    // Gán sự kiện click cho nút bong bóng
    if (chatBtn) {
        chatBtn.addEventListener('click', (e) => {
            e.stopPropagation(); // Ngăn sự kiện nổi bọt
            toggleChat();
        });
    }

    // Gán sự kiện cho nút đóng bên trong hộp thoại
    if (closeChatBtn) {
        closeChatBtn.addEventListener('click', toggleChat);
    }
});


document.addEventListener('DOMContentLoaded', () => {
    const backToTopBtn = document.getElementById('back-to-top');

    if (!backToTopBtn) return;

    // 1. Theo dõi sự kiện cuộn trang
    window.addEventListener('scroll', () => {
        // Nếu cuộn xuống quá 300px thì hiện nút
        if (window.scrollY > 300) {
            backToTopBtn.classList.remove('opacity-0', 'invisible', 'translate-y-10');
            backToTopBtn.classList.add('opacity-100', 'visible', 'translate-y-0');
        } else {
            // Ngược lại thì ẩn đi
            backToTopBtn.classList.remove('opacity-100', 'visible', 'translate-y-0');
            backToTopBtn.classList.add('opacity-0', 'invisible', 'translate-y-10');
        }
    });

    // 2. Xử lý khi click vào nút
    backToTopBtn.onclick = () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth' // Cuộn mượt mà lên đầu trang
        });
    };
});

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

document.addEventListener('DOMContentLoaded', () => {
    updateCartBadge();
    updateWishlistBadge();
    startCountdown();

    if (document.getElementById('product-grid')) {
        renderProducts(products);
    }

    if (document.getElementById('cart-content-page')) {
        renderFullCartPage();
    }

    if (document.getElementById('wishlist-grid')) {
        renderWishlistPage();
    }
});




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