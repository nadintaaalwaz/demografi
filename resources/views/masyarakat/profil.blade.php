@extends('masyarakat.layout')

@section('title', 'Profil Desa')

@push('styles')
<style>
    .main-content {
        padding: 0;
        max-width: 100%;
    }

    /* Hero Section */
    .profile-hero {
        background: linear-gradient(135deg, rgba(12, 52, 44, 0.95), rgba(7, 102, 83, 0.95));
        padding: 50px 40px 40px;
        text-align: center;
    }

    .profile-hero h1 {
        font-size: 40px;
        font-weight: 800;
        color: #fff;
        margin-bottom: 12px;
    }

    .profile-hero p {
        font-size: 15px;
        color: rgba(255, 255, 255, 0.85);
        max-width: 600px;
        margin: 0 auto;
    }

    /* Stats Cards */
    .profile-stats {
        background: #076653;
        padding: 40px 40px;
        margin-top: 0;
        position: relative;
        z-index: 10;
    }

    .stats-grid {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .stat-card {
        background: rgba(12, 52, 44, 0.6);
        backdrop-filter: blur(10px);
        padding: 25px;
        border-radius: 12px;
        border: 1px solid rgba(227, 239, 38, 0.2);
        display: flex;
        align-items: center;
        gap: 20px;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        background: rgba(12, 52, 44, 0.8);
        border-color: #E3EF26;
    }

    .stat-card-icon {
        width: 60px;
        height: 60px;
        background: rgba(227, 239, 38, 0.15);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #E3EF26;
        font-size: 26px;
        flex-shrink: 0;
    }

    .stat-card-content {
        flex: 1;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 800;
        color: #fff;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.7);
        font-weight: 500;
    }

    /* Main Content */
    .profile-content {
        background: #f8f9fa;
        padding: 80px 40px;
    }

    .content-container {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 400px 1fr;
        gap: 40px;
    }

    /* Visi Box */
    .visi-box {
        background: linear-gradient(135deg, #076653, #0C342C);
        padding: 40px;
        border-radius: 16px;
        color: #fff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(7, 102, 83, 0.3);
    }

    .visi-box::before {
        content: '"';
        position: absolute;
        top: 20px;
        left: 30px;
        font-size: 120px;
        color: rgba(227, 239, 38, 0.1);
        font-family: Georgia, serif;
        line-height: 1;
    }

    .visi-badge {
        display: inline-block;
        background: rgba(227, 239, 38, 0.2);
        color: #E3EF26;
        padding: 8px 20px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 25px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .visi-text {
        font-size: 17px;
        line-height: 1.9;
        font-style: italic;
        position: relative;
        z-index: 1;
        color: #E3EF26;
        font-weight: 500;
    }

    /* Misi Section */
    .misi-section {
        background: #fff;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    }

    .misi-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f3f4f6;
    }

    .misi-icon {
        width: 50px;
        height: 50px;
        background: #076653;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 22px;
    }

    .misi-title {
        font-size: 24px;
        font-weight: 800;
        color: #0C342C;
    }

    .misi-list {
        list-style: none;
    }

    .misi-item {
        display: flex;
        align-items: start;
        gap: 15px;
        padding: 18px 20px;
        background: #f9fafb;
        border-radius: 10px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
    }

    .misi-item:hover {
        background: #ecfdf5;
        border-left-color: #10b981;
        transform: translateX(5px);
    }

    .misi-check {
        width: 28px;
        height: 28px;
        background: #10b981;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 14px;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .misi-text {
        flex: 1;
        font-size: 14px;
        color: #374151;
        line-height: 1.7;
    }

    /* Sejarah Section */
    .sejarah-section {
        background: #fff;
        padding: 80px 40px;
    }

    .sejarah-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .sejarah-header {
        text-align: center;
        margin-bottom: 50px;
    }

    .sejarah-badge {
        display: inline-block;
        background: rgba(7, 102, 83, 0.1);
        color: #076653;
        padding: 8px 20px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 15px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .sejarah-title {
        font-size: 36px;
        font-weight: 800;
        color: #0C342C;
        margin-bottom: 15px;
    }

    .sejarah-intro {
        font-size: 16px;
        color: #6b7280;
        max-width: 700px;
        margin: 0 auto;
        line-height: 1.8;
    }

    .timeline {
        position: relative;
        padding: 40px 0;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 50%;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(180deg, #076653, #E3EF26);
        transform: translateX(-50%);
    }

    .timeline-item {
        display: flex;
        gap: 40px;
        margin-bottom: 50px;
        position: relative;
    }

    .timeline-item:nth-child(even) {
        flex-direction: row-reverse;
    }

    .timeline-content {
        flex: 1;
        background: #f9fafb;
        padding: 30px;
        border-radius: 12px;
        border: 2px solid #e5e7eb;
        transition: all 0.3s ease;
        position: relative;
    }

    .timeline-content:hover {
        border-color: #076653;
        box-shadow: 0 5px 20px rgba(7, 102, 83, 0.15);
        transform: translateY(-3px);
    }

    .timeline-icon {
        width: 60px;
        height: 60px;
        background: #076653;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 24px;
        position: relative;
        z-index: 2;
        box-shadow: 0 0 0 8px #fff, 0 0 0 10px #076653;
        flex-shrink: 0;
    }

    .timeline-year {
        font-size: 14px;
        font-weight: 800;
        color: #076653;
        background: #E3EF26;
        padding: 6px 15px;
        border-radius: 20px;
        display: inline-block;
        margin-bottom: 12px;
    }

    .timeline-title {
        font-size: 18px;
        font-weight: 700;
        color: #0C342C;
        margin-bottom: 10px;
    }

    .timeline-text {
        font-size: 14px;
        color: #6b7280;
        line-height: 1.8;
    }

    @media (max-width: 968px) {
        .profile-hero h1 {
            font-size: 32px;
        }

        .content-container {
            grid-template-columns: 1fr;
            gap: 30px;
        }

        .timeline::before {
            left: 30px;
        }

        .timeline-item {
            flex-direction: column !important;
            padding-left: 80px;
        }

        .timeline-item:nth-child(even) {
            flex-direction: column !important;
        }

        .timeline-icon {
            position: absolute;
            left: 0;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .profile-hero {
            padding: 60px 20px;
        }

        .profile-content,
        .sejarah-section {
            padding: 50px 20px;
        }

        .profile-stats {
            padding: 40px 20px;
        }
    }
</style>
@endpush

@section('content')
<!-- Hero -->
<section class="profile-hero">
    <h1>Profil Desa Sebalor</h1>
    <p>Mengenal lebih dekat sejarah, visi, misi, dan potensi wilayah Desa Maju Jaya Kecamatan Sejahtera</p>
</section>

<!-- Stats -->
<section class="profile-stats">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-icon">
                <i class="fas fa-map"></i>
            </div>
            <div class="stat-card-content">
                <div class="stat-value">450.5 <small style="font-size: 16px;">ha</small></div>
                <div class="stat-label">Total Wilayah</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon">
                <i class="fas fa-home"></i>
            </div>
            <div class="stat-card-content">
                <div class="stat-value">5 <small style="font-size: 16px;">Dusun</small></div>
                <div class="stat-label">Jumlah Dusun</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon">
                <i class="fas fa-location-arrow"></i>
            </div>
            <div class="stat-card-content">
                <div class="stat-value">12 <small style="font-size: 16px;">RW</small></div>
                <div class="stat-label">Jumlah RW</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon">
                <i class="fas fa-building"></i>
            </div>
            <div class="stat-card-content">
                <div class="stat-value">36 <small style="font-size: 16px;">RT</small></div>
                <div class="stat-label">Jumlah RT</div>
            </div>
        </div>
    </div>
</section>

<!-- Content: Visi & Misi -->
<section class="profile-content">
    <div class="content-container">
        <!-- Visi -->
        <div class="visi-box">
            <span class="visi-badge">Visi Desa</span>
            <p class="visi-text">
                "Terwujudnya Desa Sebalor yang Mandiri, Sejahtera, dan Berbudaya 
                melalui Tata Kelola Pemerintahan yang Bersih dan Transparan."
            </p>
        </div>

        <!-- Misi -->
        <div class="misi-section">
            <div class="misi-header">
                <div class="misi-icon">
                    <i class="fas fa-bullseye"></i>
                </div>
                <h2 class="misi-title">Misi Desa</h2>
            </div>

            <ul class="misi-list">
                <li class="misi-item">
                    <div class="misi-check">
                        <i class="fas fa-check"></i>
                    </div>
                    <p class="misi-text">
                        Mewujudkan kualitas pelayanan publik yang cepat, tepat, dan transparan
                    </p>
                </li>

                <li class="misi-item">
                    <div class="misi-check">
                        <i class="fas fa-check"></i>
                    </div>
                    <p class="misi-text">
                        Meningkatkan potensi ekonomi lokal berbasis pertanian dan UMKM
                    </p>
                </li>

                <li class="misi-item">
                    <div class="misi-check">
                        <i class="fas fa-check"></i>
                    </div>
                    <p class="misi-text">
                        Mengembangkan sarana dan prasarana infrastruktur pendukung
                    </p>
                </li>

                <li class="misi-item">
                    <div class="misi-check">
                        <i class="fas fa-check"></i>
                    </div>
                    <p class="misi-text">
                        Melestarikan nilai-nilai budaya dan kearifan lokal masyarakat
                    </p>
                </li>

                <li class="misi-item">
                    <div class="misi-check">
                        <i class="fas fa-check"></i>
                    </div>
                    <p class="misi-text">
                        Mengoptimalkan infrastruktur desa yang membuka dan berkelanjutan
                    </p>
                </li>
            </ul>
        </div>
    </div>
</section>

<!-- Sejarah -->
<section class="sejarah-section">
    <div class="sejarah-container">
        <div class="sejarah-header">
            <span class="sejarah-badge">LATAR BELAKANG</span>
            <h2 class="sejarah-title">Sejarah Desa</h2>
            <p class="sejarah-intro">
                Desa Sebalor memiliki sejarah panjang yang dimulai sejak zaman kolonial 
                hingga menjadi desa mandiri yang terus berkembang hingga saat ini.
            </p>
        </div>

        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-content">
                    <span class="timeline-year">1960</span>
                    <h3 class="timeline-title">Awal Mula Desa</h3>
                    <p class="timeline-text">
                        Pada mulanya, Desa Sebalor merupakan wilayah hutan belantara yang 
                        dibuka oleh para perintis. Sejak tahun 1960 nama "Maju Jaya" diberikan 
                        sebagai harapan agar wilayah ini terus berkembang dan menyejahterakan 
                        masyarakatnya di masa mendatang.
                    </p>
                </div>
                <div class="timeline-icon">
                    <i class="fas fa-tree"></i>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-content">
                    <span class="timeline-year">1975</span>
                    <h3 class="timeline-title">Perkembangan Awal</h3>
                    <p class="timeline-text">
                        Seiring berjalannya waktu, desa ini mengalami perkembangan pesat. 
                        Berdiri fasilitas-fasilitas dasar dan sarana transportasi yang mendorong 
                        kemajuan ekonomi lokal. Generasi pertama mulai membangun pondasi 
                        kehidupan bersama dengan gotong royong.
                    </p>
                </div>
                <div class="timeline-icon">
                    <i class="fas fa-seedling"></i>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-content">
                    <span class="timeline-year">2000</span>
                    <h3 class="timeline-title">Era Modernisasi</h3>
                    <p class="timeline-text">
                        Hingga kini, Desa Sebalor telah bertransformasi menjadi desa modern 
                        dengan potensi alam yang melimpah. Komunitas petani dan UMKM terus 
                        berkembang, didukung oleh pemerintah desa yang transparan dan akuntabel 
                        untuk kebaikan bersama.
                    </p>
                </div>
                <div class="timeline-icon">
                    <i class="fas fa-city"></i>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
