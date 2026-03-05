<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fashion Shop Admin - FAST FASHION</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../extra-assets/css/account-manager.css">
    <link rel="stylesheet" href="../extra-assets/css/admin-manager.css">
    {{-- <link rel="stylesheet" href="../extra-assets/css/product-manager.css"> --}}
</head>

<body>
    <div class="admin-layout">
        <header class="main-header">
            <div class="brand">
                <img src="../images/logo/logo.jpg" alt="Logo"
                    class="avatar-img">
            </div>
            <div class="search-area">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Tìm kiếm đơn hàng, khách hàng...">
            </div>
            <div class="user-area">
                <div class="notifications">
                    <i class="fa-regular fa-bell"></i>
                    <span class="dot"></span>
                </div>

                <div class="profile-container">
                    <div class="profile-link" id="profileToggle">
                        <div class="avatar" id="userAvatar">AC</div>
                        <div class="info">
                            <span class="name" id="userName">Admin</span>
                            <span class="role" id="userRole">Chủ cửa hàng</span>
                        </div>
                        <i class="fa-solid fa-chevron-down arrow-icon"></i>
                    </div>

                    <div class="dropdown-menu" id="dropdownMenu">
                        <div class="dropdown-user-info">
                            <div class="large-avatar" id="menuAvatar">AC</div>
                            <div class="text-info">
                                <span class="full-name" id="menuFullName">Nguyễn Văn Admin</span>
                                <span class="email" id="userEmail">admin@fastfashion.com</span>
                            </div>
                        </div>

                        <div class="menu-actions">
                            <a href="/admin/account-management.html" class="btn-manage">Quản lý tài khoản của bạn</a>
                        </div>

                        <div class="menu-items">
                            <a href="#" class="menu-item logout-link">
                                <i class="fa-solid fa-right-from-bracket"></i> <span>Đăng xuất</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="admin-body-wrapper" style="display: flex;">

            <aside class="sidebar">

                <nav class="nav-menu">
                    <p class="nav-title">CHÍNH</p>
                    <div class="nav-item active" data-target="tong-quan">
                        <a href=""><i class="fa-solid fa-gauge-high"></i> <span>Tổng quan</span></a>
                    </div>

                    <p class="nav-title">SẢN PHẨM</p>
                    <div class="nav-item" data-target="danh-sach-mau">
                        <a href=""><i class="fa-solid fa-list"></i> <span>Danh sách mẫu</span></a>
                    </div>
                    <div class="nav-item" data-target="them-san-pham">
                        <a href=""><i class="fa-solid fa-plus"></i> <span>Thêm sản phẩm</span></a>
                    </div>
                    <div class="nav-item" data-target="phan-loai">
                        <a href=""><i class="fa-solid fa-layer-group"></i> <span>Phân loại</span></a>
                    </div>

                    <p class="nav-title">KINH DOANH</p>
                    <div class="nav-item" data-target="don-hang">
                        <a href=""><i class="fa-solid fa-cart-shopping"></i> <span>Đơn hàng</span>
                        <span class="nav-badge">5</span></a>
                    </div>
                    <div class="nav-item" data-target="khach-hang">
                        <a href=""><i class="fa-solid fa-users"></i> <span>Khách hàng</span></a>
                    </div>
                    <div class="nav-item" data-target="doanh-thu">
                        <a href=""><i class="fa-solid fa-chart-line"></i> <span>Doanh thu</span></a>
                    </div>
                    <div class="nav-item" data-target="ho-tro-khach-hang">
                        <a href=""><i class="fa-solid fa-comment-dots"></i> <span>Hỗ trợ khách hàng</span></a>
                    </div>
                    <div class="nav-item" data-target="quan-ly-nhan-su">
                        <a href=""><i class="fa-solid fa-users-gear"></i> <span>Quản lý nhân sự</span></a>
                    </div>
                </nav>
            </aside>

            <main class="main-content">
                <div class="dashboard-body">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script src="../extra-assets/js/admin.js"></script>

</body>

</html>
