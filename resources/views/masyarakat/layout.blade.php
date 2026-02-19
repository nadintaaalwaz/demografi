<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Beranda') - SIDESA Desa Sebalor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0C342C;
            color: #333;
        }

        /* Header/Navbar */
        .main-header {
            background: rgba(12, 52, 44, 0.95);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(227, 239, 38, 0.1);
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 18px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo {
            width: 45px;
            height: 45px;
            background: #E3EF26;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #0C342C;
            font-weight: 800;
        }

        .site-title {
            color: #fff;
        }

        .site-title h1 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 2px;
            letter-spacing: 0.5px;
        }

        .site-title p {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 400;
        }

        .nav-wrapper {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .main-nav ul {
            display: flex;
            list-style: none;
            gap: 8px;
            align-items: center;
        }

        .nav-link {
            padding: 10px 20px;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 14px;
        }

        .nav-link:hover {
            background: rgba(227, 239, 38, 0.1);
            color: #E3EF26;
        }

        .nav-link.active {
            color: #E3EF26;
            font-weight: 600;
        }

        .btn-login {
            padding: 10px 24px;
            background: #E3EF26;
            color: #0C342C;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-login:hover {
            background: #d4e017;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(227, 239, 38, 0.3);
        }

        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: #fff;
            font-size: 24px;
            cursor: pointer;
        }

        /* Main Content */
        .main-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px;
        }

        /* Footer */
        .main-footer {
            background: #0C342C;
            color: rgba(255, 255, 255, 0.8);
            padding: 40px;
            margin-top: 60px;
        }

        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
        }

        .footer-section h3 {
            color: #E3EF26;
            font-size: 18px;
            margin-bottom: 15px;
        }

        .footer-section p,
        .footer-section a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            line-height: 1.8;
            font-size: 14px;
        }

        .footer-section a:hover {
            color: #E3EF26;
        }

        .footer-contact-item {
            display: flex;
            align-items: start;
            gap: 12px;
            margin-bottom: 12px;
        }

        .footer-contact-item i {
            color: #E3EF26;
            width: 20px;
            margin-top: 3px;
        }

        .footer-bottom {
            text-align: center;
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 30px;
            color: rgba(255, 255, 255, 0.6);
            font-size: 13px;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }

        .social-link {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(227, 239, 38, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #E3EF26;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background: #E3EF26;
            color: #0C342C;
            transform: translateY(-3px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                padding: 15px 20px;
            }

            .nav-wrapper {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: #0C342C;
                padding: 20px;
                flex-direction: column;
                gap: 15px;
            }

            .nav-wrapper.active {
                display: flex;
            }

            .main-nav {
                width: 100%;
            }

            .main-nav ul {
                flex-direction: column;
                gap: 10px;
            }

            .btn-login {
                width: 100%;
                text-align: center;
                justify-content: center;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .main-content {
                padding: 20px;
            }

            .footer-content {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .logo-section {
                gap: 10px;
            }

            .site-title h1 {
                font-size: 16px;
            }

            .site-title p {
                font-size: 11px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="header-content">
            <div class="logo-section">
                <div class="logo">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <div class="site-title">
                    <h1>Desa Sebalor</h1>
                    <p>Sistem Informasi Demografi</p>
                </div>
            </div>

            <div class="nav-wrapper">
                <nav class="main-nav" id="mainNav">
                    <ul>
                        <li><a href="{{ route('public.home') }}" class="nav-link {{ request()->routeIs('public.home') ? 'active' : '' }}">Beranda</a></li>
                        <li><a href="{{ route('public.profil') }}" class="nav-link {{ request()->routeIs('public.profil') ? 'active' : '' }}">Profil Desa</a></li>
                        <li><a href="{{ route('public.statistik') }}" class="nav-link {{ request()->routeIs('public.statistik') ? 'active' : '' }}">Statistik</a></li>
                        <li><a href="{{ route('public.peta') }}" class="nav-link {{ request()->routeIs('public.peta') ? 'active' : '' }}">Peta Wilayah</a></li>
                    </ul>
                </nav>

                <a href="{{ route('login') }}" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            </div>

            <button class="mobile-menu-toggle" id="mobileMenuToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Tentang Desa Sebalor</h3>
                <p>Sistem Informasi Demografi Desa Sebalor menyediakan data kependudukan yang akurat dan transparan untuk masyarakat.</p>
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <div class="footer-section">
                <h3>Tautan Cepat</h3>
                <p><a href="{{ route('public.home') }}">Beranda</a></p>
                <p><a href="{{ route('public.profil') }}">Profil Desa</a></p>
                <p><a href="{{ route('public.statistik') }}">Statistik</a></p>
                <p><a href="{{ route('public.peta') }}">Peta Wilayah</a></p>
            </div>

            <div class="footer-section">
                <h3>Kontak Kami</h3>
                <div class="footer-contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <p>Jl. Desa Sebalor No. 123<br>Kecamatan, Kabupaten, Provinsi</p>
                </div>
                <div class="footer-contact-item">
                    <i class="fas fa-phone"></i>
                    <p>(0123) 456-7890</p>
                </div>
                <div class="footer-contact-item">
                    <i class="fas fa-envelope"></i>
                    <p>info@desasebalor.id</p>
                </div>
            </div>

            <div class="footer-section">
                <h3>Jam Operasional</h3>
                <p><strong>Senin - Kamis</strong></p>
                <p>08:00 - 15:00 WIB</p>
                <p style="margin-top: 10px;"><strong>Jumat</strong></p>
                <p>08:00 - 11:30 WIB</p>
                <p style="margin-top: 10px;"><strong>Sabtu - Minggu</strong></p>
                <p>Tutup</p>
            </div>
        </div>

        <div class="footer-bottom">
            © 2026 Pemerintah Desa Sebalor. Semua Hak Dilindungi.
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const navWrapper = document.querySelector('.nav-wrapper');

        mobileMenuToggle.addEventListener('click', function() {
            navWrapper.classList.toggle('active');
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-bars');
            icon.classList.toggle('fa-times');
        });
    </script>

    @stack('scripts')
</body>
</html>
