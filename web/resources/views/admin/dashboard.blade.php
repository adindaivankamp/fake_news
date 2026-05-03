@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')

<div class="feedback-title">
    <h1>Dashboard Utama</h1>
    <p>Data terakhir diperbarui pukul 00.00, 20 April 2026</p>
</div>

<!-- STATS -->
<div class="stats-container">

    <!-- PENGGUNA -->
    <div class="stats-card active">
        <div class="stats-top">
            <span>PENGGUNA TERDAFTAR</span>
            <div class="icon-box red">
                <i class="fa fa-user"></i>
            </div>
        </div>
        <h2>7.543</h2>
        <p class="positive">↗ +12.5% dari bulan lalu</p>
    </div>

    <!-- BERITA -->
    <div class="stats-card active">
        <div class="stats-top">
            <span>BERITA TERDETEKSI</span>
            <div class="icon-box red">
                <i class="fa fa-newspaper"></i>
            </div>
        </div>
        <h2>2.306</h2>
        <p class="negative">⚠ +5.2% kasus hoax baru</p>
    </div>

    <!-- UMPAN BALIK -->
    <div class="stats-card active">
        <div class="stats-top">
            <span>UMPAN BALIK</span>
            <div class="icon-box red">
                <i class="fa fa-comment"></i>
            </div>
        </div>
        <h2>8</h2>
        <p class="neutral">— Stabil hari ini</p>
    </div>

</div>

<div class="dashboard-grid">

    <!-- KIRI -->
    <div class="left-content">

        <div class="section-header">
            <h2><i class="fa fa-chart-line"></i> Pencarian Populer</h2>
            <span class="badge-update">Terupdate Hari Ini</span>
        </div>

        <div class="main-news-card">
            <div class="rank-big">1</div>

            <img src="https://via.placeholder.com/300x200" class="news-img">

            <div class="news-content">
                <span class="label-hoax">HOAX</span>
                <p class="meta">Dicari oleh 12.8k pengguna</p>

                <h3>Pemerintah Umumkan Lockdown Nasional?</h3>

                <p class="desc">
                    Informasi beredar melalui WhatsApp...
                </p>

                <button class="btn-primary">Lihat Verifikasi →</button>
                <p class="risk">Skor Resiko: <b>98/100</b></p>
            </div>
        </div>

        <div class="news-list">

            <div class="news-card">
                <div class="rank">2</div>
                <span class="label-warning">DISINFORMASI</span>
                <h4>Video Viral Harta Karun</h4>
                <p>Video lama diedit ulang...</p>
                <small>8.4k Pencarian</small>
            </div>

            <div class="news-card">
                <div class="rank">3</div>
                <span class="label-success">FAKTA</span>
                <h4>Kenaikan BBM Dibatalkan</h4>
                <p>Konfirmasi resmi...</p>
                <small>5.2k Pencarian</small>
            </div>

        </div>

    </div>

    <!-- KANAN -->
    <div class="right-content">

        <div class="risk-card">

            <div class="risk-header">
                <h4>Status Resiko Nasional</h4>
                <i class="fa fa-exclamation-triangle"></i>
            </div>

            <div class="risk-gauge">
                <div class="gauge-fill"></div>
                <div class="gauge-text">
                    <h3>TINGGI</h3>
                    <span>LEVEL WASPADA</span>
                </div>
            </div>

            <div class="analysis-box">
                <strong>Analisis:</strong>
                Peningkatan volume misinformasi terkait isu ekonomi nasional sebesar 24% dalam 48 jam terakhir.
            </div>

        </div>

        <div class="platform-card">

            <small>STATISTIK PLATFORM</small>

            <h2>42.5k</h2>
            <p>Aduan diproses hari ini</p>

            <div class="progress-bar">
                <div class="progress"></div>
            </div>

            <div class="stats-row">
                <div>
                    <b>1.2k</b>
                    <span>ISU HOAX BARU</span>
                </div>
                <div>
                    <b>98.2%</b>
                    <span>AKURASI AI</span>
                </div>
            </div>

        </div>

    </div>

</div>

@endsection