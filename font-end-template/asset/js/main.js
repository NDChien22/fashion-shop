const products = [
  // --- NHÓM: ÁO NỮ ---
  { id: 1, name: "Áo khoác Blazer nữ Hàn Quốc", category: "Áo khoác", subCategory: "Áo Nữ", price: 705000, oldPrice: 850000, image: "https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=500", tag: "Bán chạy", sizes: ["S", "M", "L"] },
  { id: 2, name: "Áo Hoodie nỉ form rộng", category: "Áo hoodie", subCategory: "Áo Nữ", price: 350000, oldPrice: 450000, image: "https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=500", tag: "Mới", sizes: ["M", "L"] },
  { id: 3, name: "Áo len lưới croptop Vienne", category: "Áo len", subCategory: "Áo Nữ", price: 249000, oldPrice: 368000, image: "https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=500", tag: "Sale", sizes: ["S", "M"] },
  { id: 4, name: "Áo sơ mi lụa công sở", category: "Áo sơ mi", subCategory: "Áo Nữ", price: 250000, oldPrice: 320000, image: "https://images.unsplash.com/photo-1598554747436-c9293d6a588f?w=500", tag: "", sizes: ["S", "M", "L", "XL"] },
  { id: 5, name: "Áo thun Cotton basic", category: "Áo thun", subCategory: "Áo Nữ", price: 150000, oldPrice: null, image: "https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=500", tag: "Mới", sizes: ["S", "M", "L"] },

  // --- NHÓM: QUẦN NỮ ---
  { id: 6, name: "Quần Jean Baggy nữ", category: "Quần jeans", subCategory: "Quần Nữ", price: 390000, oldPrice: 500000, image: "https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=500", tag: "", sizes: ["S", "M", "L"] },
  { id: 7, name: "Quần Short thun năng động", category: "Quần short", subCategory: "Quần Nữ", price: 120000, oldPrice: 180000, image: "https://images.unsplash.com/photo-1591195853828-11db59a44f6b?w=500", tag: "Sale", sizes: ["S", "M"] },
  { id: 8, name: "Quần dài ống suông", category: "Quần dài", subCategory: "Quần Nữ", price: 280000, oldPrice: null, image: "https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=500", tag: "Mới", sizes: ["M", "L", "XL"] },

  // --- NHÓM: ÁO NAM ---
  { id: 9, name: "Áo sơ mi Oxford nam", category: "Áo sơ mi", subCategory: "Áo Nam", price: 380000, oldPrice: 450000, image: "https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=500", tag: "Mới", sizes: ["M", "L", "XL", "XXL"] },
  { id: 10, name: "Áo thun Polo phối cổ", category: "Áo polo", subCategory: "Áo Nam", price: 280000, oldPrice: null, image: "https://images.unsplash.com/photo-1624371414361-e6e0ed58d38c?w=500", tag: "Bán chạy", sizes: ["M", "L", "XL"] },
  { id: 11, name: "Áo Hoodie Streetwear", category: "Áo hoodie", subCategory: "Áo Nam", price: 450000, oldPrice: 550000, image: "https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=500", tag: "", sizes: ["L", "XL", "XXL"] },
  { id: 12, name: "Áo thun trơn nam", category: "Áo thun", subCategory: "Áo Nam", price: 190000, oldPrice: 250000, image: "https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=500", tag: "Sale", sizes: ["M", "L", "XL"] },

  // --- NHÓM: QUẦN NAM ---
  { id: 13, name: "Quần Jean Slim-fit", category: "Quần jeans", subCategory: "Quần Nam", price: 490000, oldPrice: 600000, image: "https://images.unsplash.com/photo-1542272604-787c3835535d?w=500", tag: "Bán chạy", sizes: ["29", "30", "31", "32"] },
  { id: 14, name: "Quần Kaki túi hộp Cargo", category: "Quần kaki", subCategory: "Quần Nam", price: 350000, oldPrice: null, image: "https://images.unsplash.com/photo-1617137968427-85924c800a22?w=500", tag: "Mới", sizes: ["M", "L", "XL"] },
  { id: 15, name: "Quần Tây Âu thanh lịch", category: "Quần âu", subCategory: "Quần Nam", price: 550000, oldPrice: 680000, image: "https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=500", tag: "", sizes: ["29", "30", "31", "32"] },

  // --- NHÓM: THỂ THAO ---
  { id: 16, name: "Bộ đồ thể thao nam", category: "Bộ đồ thể thao", subCategory: "Thể Thao nam", price: 650000, oldPrice: 800000, image: "https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=500", tag: "Sale", sizes: ["M", "L", "XL"] },
  { id: 17, name: "Áo tập gym nữ", category: "Áo thể thao", subCategory: "Thể Thao nữ", price: 220000, oldPrice: null, image: "https://images.unsplash.com/photo-1506629082955-511b1aa562c8?w=500", tag: "Mới", sizes: ["S", "M", "L"] },

  // --- PHỤ KIỆN ---
  { id: 18, name: "Thắt lưng da nam", category: "Thắt lưng", subCategory: "Phụ Kiện", price: 290000, oldPrice: null, image: "https://images.unsplash.com/photo-1624222247344-550fb60583dc?w=500", tag: "Mới", sizes: ["Freesize"] },
  { id: 19, name: "Túi xách nữ sang trọng", category: "Túi nam - nữ", subCategory: "Phụ Kiện", price: 1250000, oldPrice: 1500000, image: "https://images.unsplash.com/photo-1584917033904-493bb3c3cc08?w=500", tag: "Bán chạy", sizes: ["Freesize"] },
  { id: 20, name: "Mũ lưỡi trai phối lưới", category: "Mũ thể thao", subCategory: "Phụ Kiện", price: 150000, oldPrice: 190000, image: "https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=500", tag: "Sale", sizes: ["Freesize"] }
];




// Cấu hình sản phẩm tổng quát
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

        // Tìm đến đoạn return trong grid.innerHTML và sửa như sau:
        return `
            <div class="product-card group relative flex flex-col h-full">
                <a href="product-detail.html?id=${item.id}" class="relative overflow-hidden mb-4 rounded-xl bg-gray-100 shadow-sm aspect-[3/4] block">
                    <img 
                        src="${item.image}" 
                        class="w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-105" 
                        alt="${item.name}"
                    >
                    
                    ${tagHTML}

                    <div class="absolute inset-x-0 bottom-4 px-4 flex flex-col gap-3 translate-y-8 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300 z-10" onclick="event.preventDefault();">
                        <div class="flex justify-center gap-2">
                            <button onclick="toggleWishlist(${item.id})" class="bg-white/95 p-2 rounded-full shadow-md transition transform hover:scale-110
                                ${isWishlisted ? 'text-red-500' : 'text-gray-800'} hover:bg-[#bc9c75] hover:text-white">
                                <i class="${isWishlisted ? 'ri-heart-fill' : 'ri-heart-line'} text-base"></i>
                            </button>
                        </div>
                        <button onclick="addToCart(${item.id})" class="w-full bg-white text-black py-2.5 text-[10px] font-bold uppercase hover:bg-[#bc9c75] hover:text-white rounded-lg shadow-md">
                            Thêm vào giỏ
                        </button>
                    </div>
                </a>

                <div class="flex flex-col grow">
                    <a href="product-detail.html?id=${item.id}">
                        <h4 class="text-sm font-medium text-gray-800 mb-1.5 line-clamp-2 min-h-10 hover:text-[#bc9c75] transition-colors">
                            ${item.name}
                        </h4>
                    </a>
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



// Hàm thêm sản phẩm vào giỏ hàng 
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






// XỬ LÝ TRANG GIỎ HÀNG (FULL PAGE) 
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


// hàm hiển thị số lượng sản phẩm trong giỏ hàng
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
    
    // Cập nhật lại giao diện tùy theo đang ở trang nào
    const currentPage = window.location.hash.replace('#', '');
    if (currentPage === 'yeu-thich') {
        renderWishlistPage();
    } else {
        renderProducts(products); // Cập nhật lại màu tim ở trang danh sách
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



// Hàm cập nhật số lượng sản phẩm trong mục yêu thích
function updateWishlistBadge() {
    const wishlist = JSON.parse(localStorage.getItem('vienne_wishlist')) || [];
    const badge = document.getElementById('wishlist-count');    
    if (badge) {
        badge.innerText = wishlist.length;
        badge.style.display = wishlist.length > 0 ? 'flex' : 'none';
    }
}


// hàm cập nhật giao diện Header dựa trên trạng thái đăng nhập của người dùng
function updateAuthUI() {
    const dropdownMenu = document.getElementById('user-dropdown-menu');
    if (!dropdownMenu) return;

    // Lấy thông tin tài khoản từ localStorage
    const currentUser = JSON.parse(localStorage.getItem('currentUser'));

    if (currentUser) {
        // TRƯỜNG HỢP 1: ĐÃ ĐĂNG NHẬP
        dropdownMenu.innerHTML = `
            <div class="py-2 text-sm">
                <div class="px-4 py-2 border-b border-gray-50 mb-1">
                    <p class="font-bold text-gray-800 truncate">${currentUser.fullname}</p>
                    <p class="text-[10px] text-gray-400">Đang hoạt động</p>
                </div>
                <a href="#profile" class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 hover:text-[#bc9c75] transition-colors">
                    <i class="ri-user-settings-line text-lg"></i>
                    <span>Tài khoản của tôi</span>
                </a>
                <hr class="my-1 border-gray-100">
                <button onclick="handleLogout()" class="w-full flex items-center gap-3 px-4 py-2.5 text-red-500 hover:bg-red-50 transition-colors">
                    <i class="ri-logout-box-r-line text-lg"></i>
                    <span>Đăng xuất</span>
                </button>
            </div>
        `;
    } else {
        // TRƯỜNG HỢP 2: CHƯA ĐĂNG NHẬP
        dropdownMenu.innerHTML = `
            <div class="py-2 text-sm">
                <a href="login.html" class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 hover:text-[#bc9c75] transition-colors">
                    <i class="ri-login-box-line text-lg"></i>
                    <span>Đăng nhập</span>
                </a>
                <a href="register.html" class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 hover:text-[#bc9c75] transition-colors">
                    <i class="ri-user-add-line text-lg"></i>
                    <span>Đăng ký</span>
                </a>

                <a href="#profile" class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 hover:text-[#bc9c75] transition-colors">
                    <i class="ri-user-settings-line text-lg"></i>
                    <span>Tài khoản của tôi</span>
                </a>

                <a href="#Don-hang" class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 hover:text-[#bc9c75] transition-colors">
                    <i class="ri-user-settings-line text-lg"></i>
                    <span>Đơn hàng</span>
                </a>
            </div>
        `;
    }
}




// Chạy hàm ngay khi trang web tải xong
document.addEventListener('DOMContentLoaded', updateAuthUI);



// Hàm đóng/mở Sidebar trên Mobile
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




let activeFilters = {
    category: null,
    prices: [],
    sizes: []
};

// 1. Lọc danh mục - Dùng selector [filter-category] để khớp HTML
document.querySelectorAll('[filter-category]').forEach(item => {
    item.addEventListener('click', () => {
        document.querySelectorAll('[filter-category]').forEach(el => 
            el.classList.remove('text-[#bc9c75]', 'font-bold')
        );
        item.classList.add('text-[#bc9c75]', 'font-bold');
        
        activeFilters.category = item.getAttribute('data-category');
        applyFilters();
    });
});

// 2. Cập nhật checkbox
function updateCheckboxes() {
    activeFilters.prices = Array.from(document.querySelectorAll('.price-checkbox:checked')).map(cb => cb.value);
    activeFilters.sizes = Array.from(document.querySelectorAll('.size-checkbox:checked')).map(cb => cb.value);
    applyFilters();
}

document.querySelectorAll('.price-checkbox, .size-checkbox').forEach(cb => {
    cb.addEventListener('change', updateCheckboxes);
});

// 3. Hàm lọc tổng thể
function applyFilters() {
    let result = products; // Đảm bảo biến products đã chứa data

    if (activeFilters.category) {
        result = result.filter(p => p.category === activeFilters.category);
    }

    if (activeFilters.prices.length > 0) {
        result = result.filter(p => {
            return activeFilters.prices.some(range => {
                if (range === "1000000+") return p.price >= 1000000;
                const [min, max] = range.split('-').map(Number);
                return p.price >= min && p.price <= max;
            });
        });
    }

    if (activeFilters.sizes.length > 0) {
        result = result.filter(p => 
            p.sizes.some(s => activeFilters.sizes.includes(s))
        );
    }

    renderProducts(result); 
}

// 4. Toggle Sidebar cho Mobile (Cần thiết vì bạn có nút "Bộ lọc" mobile)
function toggleSidebar(open) {
    const sidebar = document.getElementById('main-sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    if (open) {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    } else {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }
}


/**
 * Hàm xử lý khi nhấn vào menu cha "Tài khoản của tôi"
 */
function handleAccountClick() {
    const subMenu = document.getElementById('sub-menu-account');
    const isClosed = !subMenu.style.maxHeight || subMenu.style.maxHeight === '0px';

    if (isClosed) {
        // Mở menu và tự động chọn tab Hồ sơ ngay lập tức
        switchTab('profile'); 
    } else {
        // Nếu đang mở thì chỉ đóng lại
        closeAccountMenu();
    }
}

function openAccountMenu() {
    const subMenu = document.getElementById('sub-menu-account');
    const arrow = document.getElementById('arrow-icon');
    const parentBtn = document.getElementById('nav-account-parent');
    
    if (subMenu) {
        subMenu.style.maxHeight = subMenu.scrollHeight + 'px';
        // Dùng ?. để tránh lỗi nếu bạn lỡ tay xóa icon arrow trong HTML
        if (arrow) arrow.style.transform = 'rotate(180deg)';
        if (parentBtn) parentBtn.classList.add('text-[#bc9c75]', 'font-semibold');
    }
}

function closeAccountMenu() {
    const subMenu = document.getElementById('sub-menu-account');
    const arrow = document.getElementById('arrow-icon');
    const parentBtn = document.getElementById('nav-account-parent');
    
    if (subMenu) {
        subMenu.style.maxHeight = '0px';
        if (arrow) arrow.style.transform = 'rotate(0deg)';
        if (parentBtn) parentBtn.classList.remove('text-[#bc9c75]', 'font-semibold');
    }
}


function switchTab(tabName) {
    // 1. Ẩn tất cả nội dung tab
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // 2. Hiển thị nội dung của tab được chọn
    const targetContent = document.getElementById('content-' + tabName);
    if (targetContent) targetContent.classList.remove('hidden');

    // 3. RESET trạng thái màu sắc của toàn bộ Sidebar
    document.querySelectorAll('.tab-link').forEach(link => {
        link.classList.remove('bg-[#bc9c75]/10', 'text-[#bc9c75]', 'font-semibold');
        link.classList.add('text-gray-600');
    });
    document.querySelectorAll('.sub-link').forEach(sub => {
        sub.classList.remove('text-[#bc9c75]', 'font-bold');
        sub.classList.add('text-gray-500');
    });

    // 4. TÔ MÀU VÀ XỬ LÝ LOGIC RIÊNG
    const accountTabs = ['profile', 'address', 'change-password'];
    
    if (accountTabs.includes(tabName)) {
        // Nếu chọn mục thuộc nhóm Tài khoản
        openAccountMenu(); 
        
        // Tô màu mục con (Hồ sơ/Địa chỉ/...)
        const activeSub = document.getElementById('sub-' + tabName);
        if (activeSub) {
            activeSub.classList.add('text-[#bc9c75]', 'font-bold');
            activeSub.classList.remove('text-gray-500');
        }
    } else {
        // Nếu chọn mục ngoài (Đơn mua, Voucher...)
        closeAccountMenu();
        
        // Tô màu nền cho mục chính
        const activeNav = document.getElementById('nav-' + tabName);
        if (activeNav) {
            activeNav.classList.add('bg-[#bc9c75]/10', 'text-[#bc9c75]', 'font-semibold');
            activeNav.classList.remove('text-gray-600');
        }
    }
    
    // Lưu lại trạng thái để load trang không bị mất
    localStorage.setItem('activeTab', tabName);
}

// Khi vừa load trang, kiểm tra xem tab nào đang được chọn
document.addEventListener('DOMContentLoaded', () => {
    const savedTab = localStorage.getItem('activeTab') || 'profile';
    switchTab(savedTab);
});


//  hàm của phần đơn hàng
function filterOrders(status, element) {
    document.querySelectorAll('.order-tab').forEach(tab => {
        tab.classList.remove('font-bold', 'text-[#bc9c75]', 'border-[#bc9c75]', 'border-b-2');
        tab.classList.add('font-medium', 'text-gray-500');
    });

    element.classList.add('font-bold', 'text-[#bc9c75]', 'border-[#bc9c75]', 'border-b-2');
    element.classList.remove('font-medium', 'text-gray-500');

    const container = document.getElementById('order-list-container');
    if (container) {
        container.innerHTML = `
            <div class="text-center animate-in fade-in duration-500 p-20">
                <i class="ri-bill-line text-6xl text-gray-100 mb-4 block"></i>
                <p class="text-gray-400 font-medium">Bạn chưa có đơn hàng nào trong mục này</p>
            </div>
        `;
    }
    console.log("Đang lọc đơn hàng theo trạng thái: " + status);
}



// Hàm sử lý phần địa chỉ
function toggleAddressModal(show) {
    const modal = document.getElementById('address-modal');
    if (show) {
        modal.classList.remove('hidden');
    } else {
        modal.classList.add('hidden');
    }
}

window.onclick = function(event) {
    const modal = document.getElementById('address-modal');
    if (event.target == modal) {
        toggleAddressModal(false);
    }
}



const PAGES = {
    'trang-chu': { title: 'Trang chủ', file: 'home.html' },
    'gioi-thieu': { title: 'Giới thiệu', file: 'introduce.html' },
    'san-pham': { title: 'Tất cả sản phẩm', file: 'product.html' },
    'Bo-suu-tap': { title: 'Bộ sưu tập', file: 'collection.html' },
    "Don-hang": { title: 'Đơn hàng', file: 'orders.html' },
    'Ho-tro': { title: 'Hỗ trợ', file: 'support.html' },
    'Lien-he': { title: 'Liên hệ', file: 'contact.html' },
    'gio-hang': { title: 'Giỏ hàng', file: 'cart.html' },
    'yeu-thich': { title: 'Sản phẩm yêu thích', file: 'wishlist.html' },
    'profile': { title: 'Hồ sơ cá nhân', file: 'profile.html' }
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
    if (pageId === 'gio-hang') {
        renderFullCartPage();
    }
    if (pageId === 'yeu-thich') {
        renderWishlistPage();
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