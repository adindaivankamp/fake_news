@extends('layouts.admin')

@section('title', 'Umpan Balik')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/umpanbalik-style.css') }}">
@endpush

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
        <h2>{{ number_format($totalFeedback) }}</h2>
        <p class="positive">↑ Data dari database</p>
    </div>

    <!-- BELUM DIBACA -->
    <div class="stats-card active">
        <div class="stats-top">
            <span>BELUM DIBACA</span>
            <div class="icon-box red">
                <i class="fa fa-envelope"></i>
            </div>
        </div>
        <h2>{{ $belumDibaca }}</h2>
        <p class="negative">Perlu perhatian segera!</p>
    </div>

    <!-- RATING -->
     {{--
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
    --}}

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

    <div class="umpanbalik-list" id="feedbackList">

    <div style="padding:20px">
        Memuat data...
    </div>

</div>


    <!-- ITEM 2 (DIBACA) -->
    <div class="umpanbalik-item read">
        <div class="umpanbalik-left">
            <img src="https://i.pravatar.cc/41" class="avatar">

            <div>
                <h4>Siti Pertiwi</h4>
                <span>Rabu, 8 April 2027 • 09:15 WIB</span>

                <p>
                    Suka dengan tampilan barunya! Sangat minimalis dan tidak membingungkan.
                </p>

                <div class="umpanbalik-actions">
                    <button class="btn-outline">Detail</button>
                </div>
            </div>
        </div>

        <span class="badge read-badge">Dibaca</span>
    </div>


    <!-- ITEM 3 (DIBALAS) -->
    <div class="umpanbalik-item read">
        <div class="umpanbalik-left">
            <img src="https://i.pravatar.cc/42" class="avatar">

            <div>
                <h4>Budi Wijaya</h4>
                <span>Selasa, 7 April 2027 • 16:45 WIB</span>

                <p>
                    Ada kesalahan deteksi pada link situs berita resmi "Warta Ekonomi".
                </p>

                <div class="umpanbalik-actions">
                    <button class="btn-outline">Detail</button>
                </div>
            </div>
        </div>
        <span class="badge read-badge">Dibaca</span>
    </div>

</div>

<script>

document.addEventListener('DOMContentLoaded', function(){

    const container = document.querySelector('.umpanbalik-list');

    fetch('/umpanbalik-data')

    .then(response => response.json())

    .then(result => {

        container.innerHTML = '';

        result.data.forEach(item => {

            container.innerHTML += `

            <div class="umpanbalik-item new">

                <div class="umpanbalik-left">

                    <img src="https://i.pravatar.cc/40?u=${item.id}" class="avatar">

                    <div>

                        <h4>${item.username}</h4>

                        <span>${item.date}</span>

                        <p>
                            ${item.feedback}
                        </p>

                        <div class="umpanbalik-actions">
                            <button class="btn-outline">
                                Detail
                            </button>
                        </div>

                    </div>

                </div>

                <span class="badge new-badge">
                    Baru
                </span>

            </div>

            `;

        });

    })

    .catch(error => {

        console.log(error);

        container.innerHTML = `
            <p style="color:red">
                Gagal memuat data
            </p>
        `;

    });

});

</script>

@endsection