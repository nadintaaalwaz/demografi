<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Sistem Demografi Desa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            color: #333;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 280px;
            background: linear-gradient(180deg, #0C342C 0%, #076653 100%);
            padding: 20px;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
        }

        .sidebar-header {
            padding: 20px 10px;
            margin-bottom: 30px;
            text-align: center;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-logo {
            width: 60px;
            height: 60px;
            background: #E3EF26;
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            color: #0C342C;
            font-weight: bold;
            box-shadow: 0 5px 15px rgba(227, 239, 38, 0.3);
        }

        .sidebar-title {
            color: #fff;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .sidebar-subtitle {
            color: rgba(255, 255, 255, 0.7);
            font-size: 12px;
        }

        .sidebar-menu {
            list-style: none;
        }

        .menu-item {
            margin-bottom: 5px;
        }

        .menu-link {
            display: flex;
            align-items: center;
            padding: 14px 18px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .menu-link i {
            width: 24px;
            margin-right: 12px;
            font-size: 18px;
        }

        .menu-link:hover {
            background: rgba(227, 239, 38, 0.15);
            color: #E3EF26;
            transform: translateX(5px);
        }

        .menu-link.active {
            background: #E3EF26;
            color: #0C342C;
            font-weight: 600;
        }

        .menu-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin: 20px 0;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
        }

        /* Top Navbar */
        .top-navbar {
            background: #fff;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .navbar-left h1 {
            font-size: 24px;
            color: #0C342C;
            font-weight: 700;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .nav-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f5f7fa;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #076653;
            position: relative;
        }

        .nav-icon:hover {
            background: #076653;
            color: #fff;
        }

        .nav-icon .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ef4444;
            color: #fff;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: 600;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 8px 15px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .user-profile:hover {
            background: #f5f7fa;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #076653, #0C342C);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            font-size: 16px;
        }

        .user-info {
            text-align: left;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: #0C342C;
            display: block;
        }

        .user-role {
            font-size: 12px;
            color: #6b7280;
        }

        /* Content Area */
        .content-area {
            padding: 30px 40px;
        }

        /* Alert Styles */
        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .alert-danger {
            background: #fee2e2;
            color: #7f1d1d;
            border-left: 4px solid #ef4444;
        }

        .alert i {
            font-size: 18px;
        }

        /* Alert Styles */
        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .alert-danger {
            background: #fee2e2;
            color: #7f1d1d;
            border-left: 4px solid #ef4444;
        }

        .alert i {
            font-size: 18px;
        }

        /* Logout Modal */
        .logout-modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            animation: fadeIn 0.3s ease-out;
        }

        .logout-modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .logout-modal-content {
            background: #fff;
            border-radius: 20px;
            padding: 35px;
            max-width: 450px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logout-modal-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .logout-modal-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 28px;
            box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
        }

        .logout-modal-header h3 {
            font-size: 22px;
            color: #0C342C;
            font-weight: 700;
            margin: 0;
        }

        .logout-modal-body {
            margin-bottom: 30px;
            color: #6b7280;
            font-size: 15px;
            line-height: 1.6;
        }

        .logout-modal-body p {
            margin-bottom: 10px;
        }

        .logout-modal-footer {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .logout-btn {
            padding: 12px 28px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .logout-btn-cancel {
            background: #f3f4f6;
            color: #374151;
        }

        .logout-btn-cancel:hover {
            background: #e5e7eb;
        }

        .logout-btn-confirm {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #fff;
        }

        .logout-btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.3);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
            }

            .sidebar-header {
                padding: 10px;
            }

            .sidebar-title,
            .sidebar-subtitle,
            .menu-link span {
                display: none;
            }

            .menu-link {
                justify-content: center;
                padding: 12px;
            }

            .menu-link i {
                margin-right: 0;
            }

            .main-content {
                margin-left: 80px;
            }

            .top-navbar {
                padding: 15px 20px;
            }

            .navbar-left h1 {
                font-size: 18px;
            }

            .user-info {
                display: none;
            }

            .content-area {
                padding: 20px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="fas fa-map-marked-alt"></i>
            </div>
            <h2 class="sidebar-title">Desa Sebalor</h2>
            <p class="sidebar-subtitle">Kasi Pemerintahan</p>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-item">
                <a href="{{ route('kasi.dashboard') }}" class="menu-link {{ request()->routeIs('kasi.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('kasi.penduduk.index') }}" class="menu-link {{ request()->routeIs('kasi.penduduk.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Data Penduduk</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('kasi.upload.form') }}" class="menu-link {{ request()->routeIs('kasi.upload.*') ? 'active' : '' }}">
                    <i class="fas fa-file-upload"></i>
                    <span>Upload Data</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('kasi.wilayah.index') }}" class="menu-link {{ request()->routeIs('kasi.wilayah.*') ? 'active' : '' }}">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Wilayah</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('kasi.dinamika') }}" class="menu-link {{ request()->routeIs('kasi.dinamika') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Dinamika Penduduk</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('kasi.laporan') }}" class="menu-link {{ request()->routeIs('kasi.laporan') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i>
                    <span>Pelaporan</span>
                </a>
            </li>
            
            <div class="menu-divider"></div>
            
            <li class="menu-item">
                <a href="{{ route('kasi.users.index') }}" class="menu-link {{ request()->routeIs('kasi.users.*') ? 'active' : '' }}">
                    <i class="fas fa-user-cog"></i>
                    <span>Manajemen User</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="#" class="menu-link" onclick="event.preventDefault(); showLogoutModal();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <div class="navbar-left">
                <h1>@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="navbar-right">
                <div class="nav-icon">
                    <i class="fas fa-bell"></i>
                    <span class="badge">3</span>
                </div>
                <div class="user-profile">
                    <div class="user-avatar">KP</div>
                    <div class="user-info">
                        <span class="user-name">{{ Auth::user()->nama ?? 'Admin' }}</span>
                        <span class="user-role">Kasi Pemerintahan</span>
                    </div>
                    <i class="fas fa-chevron-down" style="color: #6b7280; font-size: 12px;"></i>
                </div>
            </div>
        </nav>

        <!-- Content Area -->
        <div class="content-area">
            @if(session('login_success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('login_success') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="logout-modal">
        <div class="logout-modal-content">
            <div class="logout-modal-header">
                <div class="logout-modal-icon">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <h3>Konfirmasi Logout</h3>
            </div>
            <div class="logout-modal-body">
                <p><strong>{{ Auth::user()->nama ?? 'Admin' }}</strong>, apakah Anda yakin ingin keluar dari sistem?</p>
                <p>Anda harus login kembali untuk mengakses sistem.</p>
            </div>
            <div class="logout-modal-footer">
                <button type="button" class="logout-btn logout-btn-cancel" onclick="closeLogoutModal()">
                    <i class="fas fa-times"></i> Batal
                </button>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn logout-btn-confirm">
                        <i class="fas fa-sign-out-alt"></i> Ya, Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showLogoutModal() {
            document.getElementById('logoutModal').classList.add('active');
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').classList.remove('active');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('logoutModal');
            if (event.target === modal) {
                closeLogoutModal();
            }
        }
    </script>

    @stack('scripts')
</body>
</html>
