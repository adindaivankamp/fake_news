@extends('layouts.admin')

@section('title', 'Umpan Balik')

@section('content')

<!-- ===== HEADER TITLE ===== -->
<div class="feedback-title">
    <h1>Manajemen Umpan Balik</h1>
    <p>Pantau dan tanggapi masukan dari pengguna untuk meningkatkan akurasi sistem.</p>
</div>

<!-- ===== STATS ===== -->
<div class="stats-container">

    <!-- TOTAL FEEDBACK -->
    <div class="stats-card">
        <div class="stats-top">
            <span>TOTAL UMPAN BALIK</span>
            <div class="icon-box gray">
                <i class="fa fa-comments"></i>
            </div>
        </div>
        <h2>1,284</h2>
        <p class="positive">↑ +12% dari bulan lalu</p>
    </div>

    <!-- BELUM DIBACA -->
    <div class="stats-card active">
        <div class="stats-top">
            <span>BELUM DIBACA</span>
            <div class="icon-box red">
                <i class="fa fa-envelope"></i>
            </div>
        </div>
        <h2>42</h2>
        <p class="negative">Perlu perhatian segera!</p>
    </div>

    <!-- RATING -->
    <div class="stats-card">
        <div class="stats-top">
            <span>RATA-RATA RATING</span>
            <div class="icon-box yellow">
                <i class="fa fa-star"></i>
            </div>
        </div>
        <h2>4.8 <small>/ 5.0</small></h2>

        <div class="rating-stars">
            <span class="stars">★ ★ ★ ★ ☆</span>
            <span class="total"> dari 840 total ulasan</span>
        </div>
    </div>

</div>

<div class="umpanbalik-title">

    <h2>Umpan Balik Terbaru</h2>

    <div class="umpanbalik-tools">
        <button class="btn-tool">
            <i class="fa fa-filter"></i> Filter
        </button>
    </div>
</div>

<!-- ===== LIST ===== -->
<div class="umpanbalik-list">

    <!-- ITEM 1 (BARU) -->
    <div class="umpanbalik-item new">
        <div class="umpanbalik-left">
            <img src="https://i.pravatar.cc/40" class="avatar">

            <div>
                <h4>Andi Maulana</h4>
                <span>Kamis, 9 April 2027 • 14:20 WIB</span>

                <div class="rating">
                    ★ ★ ★ ★ ☆
                </div>

                <p>
                    Aplikasi sangat membantu untuk memverifikasi link yang beredar di grup WhatsApp keluarga.
                    Namun, proses loading saat deteksi link berita internasional terasa sedikit lambat dibanding link lokal.
                </p>

                <div class="umpanbalik-actions">
                    <button class="btn-primary">Balas</button>
                    <button class="btn-outline">Detail</button>
                </div>
            </div>
        </div>

        <span class="badge new-badge">Baru</span>
    </div>


    <!-- ITEM 2 (DIBACA) -->
    <div class="umpanbalik-item read">
        <div class="umpanbalik-left">
            <img src="https://i.pravatar.cc/41" class="avatar">

            <div>
                <h4>Siti Pertiwi</h4>
                <span>Rabu, 8 April 2027 • 09:15 WIB</span>

                <div class="rating">
                    ★ ★ ★ ★ ★
                </div>

                <p>
                    Suka dengan tampilan barunya! Sangat minimalis dan tidak membingungkan.
                </p>

                <div class="umpanbalik-actions">
                    <button class="btn-primary">Balas</button>
                    <button class="btn-outline">Detail</button>
                </div>
            </div>
        </div>

        <span class="badge read-badge">Dibaca</span>
    </div>


    <!-- ITEM 3 (DIBALAS) -->
    <div class="umpanbalik-item replied">
        <div class="umpanbalik-left">
            <img src="https://i.pravatar.cc/42" class="avatar">

            <div>
                <h4>Budi Wijaya</h4>
                <span>Selasa, 7 April 2027 • 16:45 WIB</span>

                <div class="rating">
                    ★ ★ ★ ★ ☆
                </div>

                <p>
                    Ada kesalahan deteksi pada link situs berita resmi "Warta Ekonomi".
                </p>

                <div class="reply-box">
                    <strong>Tanggapan Admin</strong>
                    <p>
                        Terima kasih Pak Budi, kami telah melakukan verifikasi domain tersebut.
                    </p>
                </div>

                <button class="btn-outline">Detail Percakapan</button>
            </div>
        </div>

        <span class="badge reply-badge">Dibalas</span>
    </div>

</div>

@endsection