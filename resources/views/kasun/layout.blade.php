<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Kasun Desa Sebalor</title>
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

        .dusun-badge {
            display: inline-block;
            background: #E3EF26;
            color: #0C342C;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            margin-top: 8px;
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
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
        }

        .alert i {
            font-size: 18px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
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
            .dusun-badge,
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
                <i class="fas fa-home"></i>
            </div>
            <h2 class="sidebar-title">Desa Sebalor</h2>
            <p class="sidebar-subtitle">Kepala Dusun</p>
            <span class="dusun-badge">{{ Auth::user()->dusun->nama ?? 'Dusun' }}</span>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-item">
                <a href="{{ route('kasun.dashboard') }}" class="menu-link {{ request()->routeIs('kasun.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('kasun.statistik') }}" class="menu-link {{ request()->routeIs('kasun.statistik') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i>
                    <span>Statistik Dusun</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('kasun.peta') }}" class="menu-link {{ request()->routeIs('kasun.peta') ? 'active' : '' }}">
                    <i class="fas fa-map"></i>
                    <span>Peta Dusun</span>
                </a>
            </li>
            
            <div class="menu-divider"></div>
            
            <li class="menu-item">
                <a href="{{ route('kasun.profile') }}" class="menu-link {{ request()->routeIs('kasun.profile') ? 'active' : '' }}">
                    <i class="fas fa-user"></i>
                    <span>Profil</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('logout') }}" class="menu-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <div class="navbar-left">
                <h1>@yield('page-title', 'Dashboard Dusun')</h1>
            </div>
            <div class="navbar-right">
                <div class="nav-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="user-profile">
                    <div class="user-avatar">KD</div>
                    <div class="user-info">
                        <span class="user-name">{{ Auth::user()->name ?? 'Kasun' }}</span>
                        <span class="user-role">Kepala Dusun</span>
                    </div>
                    <i class="fas fa-chevron-down" style="color: #6b7280; font-size: 12px;"></i>
                </div>
            </div>
        </nav>

        <!-- Content Area -->
        <div class="content-area">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>
