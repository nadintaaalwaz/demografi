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
    <p>Mengenal lebih dekat sejarah, visi, misi, dan potensi wilayah Desa Sebalor Kecamatan Bandung Kabupaten Tulungagung</p>
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
                "Terbangunnya Tata Kelola Pemerintahan yang Baik dan Bersih untuk Mewujudkan Desa Sebalor yang lebih maju"
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
                        Menyelenggarakan pemerintahan yang melayani dan mengayomi masyarakat;
                </li>

                <li class="misi-item">
                    <div class="misi-check">
                        <i class="fas fa-check"></i>
                    </div>
                    <p class="misi-text">
                        Menyelenggarakan pemerintahan yang amanah, bersih, terbebas dari korupsi, kolusi dan Nepotisme. 
                    </p>
                </li>

                <li class="misi-item">
                    <div class="misi-check">
                        <i class="fas fa-check"></i>
                    </div>
                    <p class="misi-text">
                        Meningkatkan perekonomian masyarakat melalui penciptaan lapangan kerja dengan berbasiskan pada potensi desa.
                    </p>
                </li>

                <li class="misi-item">
                    <div class="misi-check">
                        <i class="fas fa-check"></i>
                    </div>
                    <p class="misi-text">
                        Meningkatkan mutu kesejahteraan masyarakat untuk mencapai taraf hidup yang lebih baik dan layak. 
                    </p>
                </li>

                <li class="misi-item">
                    <div class="misi-check">
                        <i class="fas fa-check"></i>
                    </div>
                    <p class="misi-text">
                        Membangun mental spiritual bagi seluruh birokrasi dan masyarakat untuk mewujudkan desa Sebalor yang religius dan bermartabat melalui peningkatan mutu lembaga pendidikan dan keagamaan yang ada.
                    </p>
                </li>
                <li class="misi-item">
                    <div class="misi-check">
                        <i class="fas fa-check"></i>
                    </div>
                    <p class="misi-text">
                        Meningkatkan sarana dan prasarana umum guna mendukung kelancaran perekonomian masyarakat.
                    </p>
                </li>
                <li class="misi-item">
                    <div class="misi-check">
                        <i class="fas fa-check"></i>
                    </div>
                    <p class="misi-text">
                        Pemerataan pembangunan fisik dan non fisik, sehingga tidak akan tejadi kesenjangan sosial di seluruh masyarakat Desa Sebalor.
                </li>
                <li class="misi-item">
                    <div class="misi-check">
                        <i class="fas fa-check"></i>
                    </div>
                    <p class="misi-text">
                        Koordinasi dan bekerja sama dengan semua unsur kelembagaan desa, lembaga keagamaan dan lembaga sosial politik supaya dapat memberikan pelayanan yang terbaik kepada masyarakat yang meliputi bidang : ekonomi, sosial, politik, budaya, olahraga, ketertiban dan keamanan masyarakat.
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
                Sejarah Desa Sebalor memiliki latar belakang yang unik dan menarik sejak masa lampau hingga era kemerdekaan.
            </p>
        </div>

        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-content">
                    <span class="timeline-year">Masa Silam</span>
                    <h3 class="timeline-title">Asal Usul Desa Sebalor</h3>
                    <p class="timeline-text">
                        Sejarah Desa Sebalor yang secara pasti tidak dapat diketahui secara tepat. Sebab tidak terdapat tanda tanda tertulis yang berwujud prasasti dan lain sebagainya, jadi jelasnya riwayat tersebut hanya merupakan cerita atau dongengan secara turun temurun. Baru setelah menginjak jaman kemerdekaan, ada beberapa peninggalan pasti, berdasarkan narasumber yang sekarang masih hidup. Demikian pula bagi Desa Sebalor, pada masa masa silam mempunyai sejarah yang unik sehingga sejarah dapat kami bagi dua periode, yaitu yang pertama sejarah masa silam dan yang kedua sejarah masa perang kemerdekaan.
                    </p>
                </div>
                <div class="timeline-icon">
                    <i class="fas fa-book"></i>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-content">
                    <span class="timeline-year">1873</span>
                    <h3 class="timeline-title">Pembabatan Desa</h3>
                    <p class="timeline-text">
                        Berdasarkan cerita dari nara sumber yang dapat kami percaya dan sekarang masih hidup, maka desa yang sekarang di sebut Sebalor itu dibuka (dibabat) kurang lebih pada taun 1873, dan sebagai pembabatnya (pendirinya) ialah seorang yang bernama Mbah Guru Eyang Djalaludin Sihab. Kemudian Beliau mendirikan suatu perguruan dengan nama Putuk Pengajaran. Karena sang guru orang yang sakti dan mumpuni dalam ilmu lahir dan batin, maka banyaklah orang yang berguru kepadaNya.
                    </p>
                </div>
                <div class="timeline-icon">
                    <i class="fas fa-tree"></i>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-content">
                    <span class="timeline-year">Era Perguruan</span>
                    <h3 class="timeline-title">Asal Nama Sebalor</h3>
                    <p class="timeline-text">
                        Demikian banyaknya orang yang berguru maka diaturlah waktu menghadap sang guru, sehingga semua siswa dapat bertatap muka, guna mendapatkan wejangan ilmu seluruhnya. Dalam pengaturan Waktu tersebut siswa yang bersal dari utara perguruan harus menanti dalam bahasa jawa Sebo dulu sebelum mendapat panggilan. Sehingga tempat menantinya (Sebonya) para siswa dari bagian utara bahasa jawanya adalah Lor tersebut sampai sekarang terkenal dengan nama Sebalor, yang maknanya PASEBON BAGIAN UTARA (LOR), Berubah ucapan menjadi SEBANLOR dan akhirnya berubah ucapan lagi menjadi SEBALOR.
                    </p>
                </div>
                <div class="timeline-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-content">
                    <span class="timeline-year">1940</span>
                    <h3 class="timeline-title">Masa Perang Kemerdekaan</h3>
                    <p class="timeline-text">
                        Pada masa perang kemerdekaan tahun 1940, karena pada umumnya daerah kota (termasuk ibukota Kecamatan) sudah diduduki Belanda, maka para pejuang meneruskan perlawanan dengan taktik Gerilya. Guna Mengatur siasat dicarilah tempat yang sangat strategis yang dipusatkan dilereng gunung yang terletak di Dukuh Cunduk Desa Sebalor.
                    </p>
                </div>
                <div class="timeline-icon">
                    <i class="fas fa-flag"></i>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-content">
                    <span class="timeline-year">Era Perjuangan</span>
                    <h3 class="timeline-title">Pos Komando Batalyon 121 Mliwis</h3>
                    <p class="timeline-text">
                        Tempatnya di rumah Bapak Songgolo sebagai warga setempat. Rumah tersebut dijadikan Pos Komado dari Batalyon 121 Mliwis TNI AD dibawah pimpinan Mayor Sobiran. Adapun nama-nama pimpinan Batalyon tersebut adalah Mayor Sobiran Sebagai DAN YON, Kapten Witarmin sebagai DAN K I, Letda Sandiman Sebagai DAN KI II, Letda Hartono sebagai DAN KI III dan Kapten Muyadi Sebagai DAN KI IV.
                    </p>
                </div>
                <div class="timeline-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-content">
                    <span class="timeline-year">1985</span>
                    <h3 class="timeline-title">Monumen Peringatan</h3>
                    <p class="timeline-text">
                        Untuk mengenang sejarah tersebut, maka pada tahun 1985 rekan – rekan guru yang tergabung dalam Peserta Penataran Pendidikan Sejarah Perjuangan Bangsa (PSPB) telah mengadakan kerjasama dengan pemerintah setempat untuk mengadakan pelacakan kembali. Dan akhirnya dihalaman rumah Pak Songgolo tersebut di dirikan sebuah monument mekipun sangat sederhana.
                    </p>
                </div>
                <div class="timeline-icon">
                    <i class="fas fa-monument"></i>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
