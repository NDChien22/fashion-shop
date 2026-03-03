<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fashion Shop Admin - FAST FASHION</title>
    <link rel="shortcut icon" href="/images/logo/logo.jpg" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../extra-assets/css/admin-manager.css">
    <link rel="stylesheet" href="../extra-assets/css/account-manager.css">
    @stack('styles')
</head>
<body>
    <div class="admin-layout">
        <header class="main-header">
                <div class="brand">
                    <img src="../images/logo/logo.jpg" alt="Logo" class="avatar-img">
                </div>
                <div class="header-spacer"></div>
                <div class="user-area mm">
                    <div class="notifications">
                        <i class="fa-regular fa-bell"></i>
                        <span class="dot"></span>
                    </div>

                    <div class="profile-container">
                        <div class="profile-link" id="profileToggle">
                            <div class="avatar" id="userAvatar">AC</div>
                            <div class="info">
                                <span class="name" id="userName">{{ Auth::user()->username }}</span>
                                <span class="role" id="userRole">Chủ cửa hàng</span>
                            </div>
                            <i class="fa-solid fa-chevron-down arrow-icon"></i>
                        </div>

                        <div class="dropdown-menu" id="dropdownMenu">
                            <div class="dropdown-user-info">
                                <div class="large-avatar">AC</div>
                                <div class="text-info">
                                    <span class="full-name">{{ Auth::user()->username }}</span>
                                    <span class="email">{{ Auth::user()->email }}</span>
                                </div>
                            </div>
                            <div class="menu-actions">
                                <a href="{{ route('admin.admin-profile') }}" class="btn-manage">Quản lý tài khoản của bạn</a>
                            </div>
                            <div class="menu-items">

                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="menu-item logout-link">
                                        <i class="fa-solid fa-right-from-bracket"></i> <span>Đăng xuất</span>
                                    </button>
                                </form>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <div class="admin-body-wrapper" style="display: flex;"> 
                <aside class="sidebar">
                    <nav class="nav-menu">
                        <div class="nav-item {{ Request::routeIs('admin.admin-profile') ? 'active' : '' }}" data-target="thong-tin">
                            <a href="{{ route('admin.admin-profile') }}"><i class="fa-solid fa-gauge-high"></i> <span>Thông tin hồ sơ</span></a>
                        </div>
                        <div class="nav-item {{ Request::routeIs('admin.account-manager') ? 'active' : '' }}" data-target="tai-khoan">
                            <a href="{{ route('admin.account-manager') }}"><i class="fa-solid fa-gears"></i> <span>Quản lý tài khoản</span></a>
                        </div>
                    </nav>
                </aside>

                <main class="main-content">
                    <div id="main-content-area" class="content-wrapper">

                    @yield('content')

                    </div>
                </main>
            </div>


        
    </div>

    <script src="../extra-assets/js/admin.js"></script>
</body>
</html>