@extends('layouts.app')

@section('title', 'Pencarian Terpopuler')

@section('content')
<div class="lh-popular-page" data-search-url="{{ route('beranda') }}">

    @include('user.partials.navbar')

    @include('user.partials.hero-bg')

    <main class="lh-popular-main">
        <section class="lh-popular-hero">
            <p class="lh-popular-hero__eyebrow">Tren pencarian paling ramai hari ini</p>
            <h1 class="lh-popular-hero__title">
                Berita <button type="button" class="lh-filter-trigger" data-filter-trigger="category" aria-label="Ubah filter kategori">Hoax</button> Paling Banyak Dicari Bulan <button type="button" class="lh-filter-trigger" data-filter-trigger="period" aria-label="Ubah filter bulan dan tahun">Juni 2026</button>!
            </h1>
            <p class="lh-popular-hero__subtitle">
                Klik teks bergaris bawah berwarna kuning untuk memilih kategori dan periode pencarian.
            </p>
            <div class="lh-popular-hero__meta">
                <span class="lh-popular-hero__chip">Kategori aktif: <strong id="activeCategoryLabel">Hoax</strong></span>
                <span class="lh-popular-hero__chip">Periode aktif: <strong id="activePeriodLabel">Juni 2026</strong></span>
                <span class="lh-popular-hero__chip lh-popular-hero__chip--soft" id="popularCountLabel">3 hasil ditemukan</span>
            </div>
        </section>

        <section class="lh-popular-grid-wrap">
            <div class="lh-popular-grid" id="popularGrid" aria-live="polite"></div>
            <div class="lh-popular-empty" id="popularEmpty" hidden>
                <h2>Tidak ada hasil untuk filter ini</h2>
                <p>Coba ubah kategori atau pilih bulan dan tahun yang lain.</p>
            </div>
        </section>
    </main>

    <div class="lh-filter-modal" id="filterModal" hidden>
        <div class="lh-filter-modal__overlay" data-filter-close></div>
        <div class="lh-filter-modal__card" role="dialog" aria-modal="true" aria-labelledby="filterModalTitle">
            <div class="lh-filter-modal__header">
                <div>
                    <p class="lh-filter-modal__eyebrow" id="filterModalEyebrow">Filter</p>
                    <h2 class="lh-filter-modal__title" id="filterModalTitle">Pilih Filter</h2>
                </div>
                <button type="button" class="lh-filter-modal__close" aria-label="Tutup filter" data-filter-close>
                    <iconify-icon icon="mdi:close" width="22" height="22"></iconify-icon>
                </button>
            </div>
            <p class="lh-filter-modal__description" id="filterModalDescription">
                Pilih opsi yang ingin ditampilkan pada halaman pencarian terpopuler.
            </p>
            <div class="lh-filter-modal__options" id="filterModalOptions"></div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user/background.css') }}">
<link rel="stylesheet" href="{{ asset('css/user/navbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/user/profile-popup.css') }}">
<link rel="stylesheet" href="{{ asset('css/user/pencarian-terpopuler.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/pencarian-terpopuler.js') }}"></script>
<script src="{{ asset('js/user/profile-popup.js') }}"></script>
@endpush