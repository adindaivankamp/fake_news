@extends('layouts.admin')

@section('title', 'Umpan Balik')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/umpanbalik-style.css') }}">
@endpush

@section('content')

<!-- ===== HEADER TITLE ===== -->
<div class="feedback-header-top">
    <div class="feedback-title">
        <h1>Manajemen Umpan Balik</h1>
        <p>Pantau dan tanggapi masukan dari pengguna untuk meningkatkan akurasi sistem.</p>
    </div>

    <div class="search-wrapper">
        <input
            type="text"
            id="searchFeedback"
            class="search-input"
            placeholder="Search..."
        >

        <button class="search-btn">
            <i class="fa fa-search"></i>
        </button>
    </div>
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

    {{-- =========================
     CARD BELUM DIBACA
    ========================= --}}

    {{--
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
    --}}

</div>

<div class="umpanbalik-title">

    <h2>Umpan Balik Terbaru</h2>

    {{-- =========================
     FILTER
    ========================= --}}

    {{--
    <div class="umpanbalik-tools">
        <button class="btn-tool">
            <i class="fa fa-filter"></i> Filter
        </button>
    </div>
    --}}
</div>

<!-- ===== LIST ===== -->
<div class="umpanbalik-list" id="feedbackList">

    <div style="padding:20px">
        Memuat data...
    </div>

</div>

<!-- POPUP DETAIL -->
<div id="feedbackPopup" class="popup-overlay" style="display:none;">

    <div class="popup-box">

        <button id="closePopup"
        class="popup-close">

            ✕

        </button>


        <div class="popup-header">

            <h2>Detail Umpan Balik</h2>

            <p>
                Informasi lengkap masukan pengguna
            </p>

        </div>


        <div class="popup-info">

            <div class="info-card">

                <div class="info-title">
                    Nama Pengguna
                </div>

                <div
                class="info-value"
                id="popupUser">
                </div>

            </div>


            <div class="info-card">

                <div class="info-title">
                    Tanggal
                </div>

                <div
                class="info-value"
                id="popupDate">
                </div>

            </div>


            <div class="info-card">

                <div class="info-title">
                    ID Request
                </div>

                <div
                class="info-value"
                id="popupRequest">
                </div>

            </div>

        </div>


        <h3>Isi Feedback</h3>

        <div
        class="feedback-box"
        id="popupFeedback">

        </div>

    </div>

</div>

<script>

document.addEventListener('DOMContentLoaded',function(){

    const container=
    document.getElementById('feedbackList');

    const popup=
    document.getElementById('feedbackPopup');

    const closeBtn=
    document.getElementById('closePopup');

    fetch('/admin/umpanbalik-data')

    .then(response=>response.json())

    .then(result=>{

        container.innerHTML='';

        result.data.forEach(item=>{

            container.innerHTML += `
            <div class="umpanbalik-item new feedback-item"
                data-user="${item.username.toLowerCase()}"
                data-feedback="${item.feedback.toLowerCase()}"
                >

                <div class="umpanbalik-left">

                    <img src="https://i.pravatar.cc/40?u=${item.id}" class="avatar">

                    <div>

                        <h4>${item.username}</h4>

                        <span>${item.date}</span>

                        <p>${item.feedback}</p>

                        <div class="umpanbalik-actions">

                            <button
                            class="btn-outline btn-detail"
                            data-username="${item.username}"
                            data-date="${item.date}"
                            data-feedback="${item.feedback}"
                            data-request="${item.request_id ?? '-'}">

                            Detail

                            </button>

                        </div>

                    </div>

                </div>

                {{--
                <span class="badge new-badge">
                   Baru
                </span>
                    --}}
            </div>
            `;

        });

    })

    .catch(error=>{

        console.log(error);

        container.innerHTML=`
            <p style="color:red">
                Gagal memuat data
            </p>
        `;

    });

    /* =====================
    SEARCH FEEDBACK
    ===================== */

    document
    .getElementById('searchFeedback')
    .addEventListener('keyup',function(){

        const keyword=
        this.value.toLowerCase();

        const cards=
        document.querySelectorAll(
            '.feedback-item'
        );

        cards.forEach(card=>{

            const username=
            card.dataset.user;

            const feedback=
            card.dataset.feedback;

            const match=

            username.includes(keyword)

            ||

            feedback.includes(keyword);

            card.style.display=
            match ? 'flex' : 'none';

        });

    });

    document.addEventListener('click',function(e){

        if(e.target.classList.contains('btn-detail')){

            document.getElementById(
                'popupUser'
            ).innerText =
            e.target.dataset.username;

            document.getElementById(
                'popupDate'
            ).innerText =
            e.target.dataset.date;

            document.getElementById(
                'popupRequest'
            ).innerText =
            e.target.dataset.request;

            document.getElementById(
                'popupFeedback'
            ).innerText =
            e.target.dataset.feedback;

            popup.style.display='flex';

        }

    });


    closeBtn.addEventListener(
        'click',
        ()=> popup.style.display='none'
    );


    popup.addEventListener(
        'click',
        function(e){

            if(e.target===popup){

                popup.style.display='none';

            }

        }
    );

});

</script>

@endsection