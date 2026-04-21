@extends('layouts.app')

@section('title', 'Pencarian')

@section('content')
<div class="lh-wrapper">

    <!-- ========== NAVBAR ========== -->
    <header class="lh-navbar">
        <!-- Logo -->
        <a href="{{ route('beranda') }}" class="lh-logo">
            <img src="{{ asset('img/logo-lensa.png') }}" alt="Logo Lensa Hoax" class="lh-logo__img">
        </a>

        <!-- Nav Icons -->
        <nav class="lh-nav-icons">
            <a href="#" class="lh-nav-btn" aria-label="Tren" onclick="alert('Fitur Tren akan segera hadir')">
                <iconify-icon icon="iconamoon:trend-up-fill" width="26" height="26"></iconify-icon>
                <span class="lh-nav-tooltip" role="tooltip">
                    <iconify-icon icon="iconamoon:trend-up-fill" width="18" height="18"></iconify-icon>
                    <span>Pencarian Terpopuler</span>
                </span>
            </a>
            <a href="#" class="lh-nav-btn" aria-label="Riwayat" onclick="alert('Fitur Riwayat akan segera hadir')">
                <iconify-icon icon="fontisto:history" width="24" height="24"></iconify-icon>
                <span class="lh-nav-tooltip" role="tooltip">
                    <iconify-icon icon="fontisto:history" width="17" height="17"></iconify-icon>
                    <span>Riwayat Pencarian Anda</span>
                </span>
            </a>
            <a href="{{ route('whatsapp.page') }}" class="lh-nav-btn" aria-label="WhatsApp">
                <iconify-icon icon="garden:whatsapp-fill-16" width="24" height="24"></iconify-icon>
                <span class="lh-nav-tooltip" role="tooltip">
                    <iconify-icon icon="garden:whatsapp-fill-16" width="18" height="18"></iconify-icon>
                    <span>Dapatkan Melalui Whatsapp</span>
                </span>
            </a>
            <a href="#" class="lh-nav-btn lh-nav-btn--user" aria-label="Profil" onclick="alert('Fitur Profil akan segera hadir')">
                <iconify-icon icon="mdi:user" width="26" height="26"></iconify-icon>
                <span class="lh-nav-tooltip lh-nav-tooltip--left" role="tooltip">
                    <iconify-icon icon="mdi:user" width="18" height="18"></iconify-icon>
                    <span>Profil Pengguna</span>
                </span>
            </a>
        </nav>
    </header>

    <!-- ========== HERO BACKGROUND ========== -->
    <div class="lh-hero-bg">
        <!-- Network graph SVG background -->
        <svg class="lh-network-svg" viewBox="0 0 1920 900" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
            <!-- Nodes -->
            <circle cx="200" cy="120" r="22" fill="#C25A5A" opacity="0.85"/>
            <circle cx="340" cy="200" r="8"  fill="#C25A5A" opacity="0.65"/>
            <circle cx="520" cy="140" r="8"  fill="#C25A5A" opacity="0.65"/>
            <circle cx="130" cy="310" r="8"  fill="#C25A5A" opacity="0.55"/>
            <circle cx="1700" cy="160" r="8" fill="#C25A5A" opacity="0.55"/>
            <circle cx="1820" cy="80"  r="8" fill="#C25A5A" opacity="0.55"/>
            <circle cx="1600" cy="260" r="8" fill="#C25A5A" opacity="0.55"/>
            <circle cx="1850" cy="620" r="28" fill="#C25A5A" opacity="0.80"/>
            <circle cx="1750" cy="480" r="8" fill="#C25A5A" opacity="0.55"/>
            <circle cx="1870" cy="360" r="8" fill="#C25A5A" opacity="0.55"/>
            <circle cx="60"   cy="500" r="8" fill="#C25A5A" opacity="0.55"/>
            <circle cx="260"  cy="760" r="10" fill="#C25A5A" opacity="0.58"/>
            <circle cx="420"  cy="700" r="8"  fill="#C25A5A" opacity="0.55"/>
            <circle cx="610"  cy="800" r="8"  fill="#C25A5A" opacity="0.52"/>
            <circle cx="920"  cy="730" r="9"  fill="#C25A5A" opacity="0.56"/>
            <circle cx="1140" cy="820" r="8"  fill="#C25A5A" opacity="0.52"/>
            <circle cx="1410" cy="760" r="8"  fill="#C25A5A" opacity="0.54"/>
            <circle cx="1610" cy="860" r="10" fill="#C25A5A" opacity="0.58"/>
            <circle cx="1780" cy="790" r="8"  fill="#C25A5A" opacity="0.54"/>
            <!-- Lines -->
            <line x1="200" y1="120" x2="340" y2="200" stroke="#C25A5A" stroke-width="1.5" opacity="0.45"/>
            <line x1="340" y1="200" x2="520" y2="140" stroke="#C25A5A" stroke-width="1.5" opacity="0.45"/>
            <line x1="200" y1="120" x2="130" y2="310" stroke="#C25A5A" stroke-width="1.5" opacity="0.35"/>
            <line x1="340" y1="200" x2="130" y2="310" stroke="#C25A5A" stroke-width="1.5" opacity="0.35"/>
            <line x1="1700" y1="160" x2="1820" y2="80"  stroke="#C25A5A" stroke-width="1.5" opacity="0.45"/>
            <line x1="1700" y1="160" x2="1600" y2="260" stroke="#C25A5A" stroke-width="1.5" opacity="0.45"/>
            <line x1="1820" y1="80"  x2="1870" y2="360" stroke="#C25A5A" stroke-width="1.5" opacity="0.35"/>
            <line x1="1850" y1="620" x2="1750" y2="480" stroke="#C25A5A" stroke-width="1.5" opacity="0.45"/>
            <line x1="1850" y1="620" x2="1870" y2="360" stroke="#C25A5A" stroke-width="1.5" opacity="0.35"/>
            <line x1="60"   y1="500" x2="130" y2="310" stroke="#C25A5A" stroke-width="1.5" opacity="0.35"/>
            <line x1="60"   y1="500" x2="260" y2="760" stroke="#C25A5A" stroke-width="1.5" opacity="0.30"/>
            <line x1="260"  y1="760" x2="420" y2="700" stroke="#C25A5A" stroke-width="1.5" opacity="0.34"/>
            <line x1="420"  y1="700" x2="610" y2="800" stroke="#C25A5A" stroke-width="1.5" opacity="0.32"/>
            <line x1="610"  y1="800" x2="920" y2="730" stroke="#C25A5A" stroke-width="1.5" opacity="0.32"/>
            <line x1="920"  y1="730" x2="1140" y2="820" stroke="#C25A5A" stroke-width="1.5" opacity="0.30"/>
            <line x1="1140" y1="820" x2="1410" y2="760" stroke="#C25A5A" stroke-width="1.5" opacity="0.30"/>
            <line x1="1410" y1="760" x2="1610" y2="860" stroke="#C25A5A" stroke-width="1.5" opacity="0.32"/>
            <line x1="1610" y1="860" x2="1780" y2="790" stroke="#C25A5A" stroke-width="1.5" opacity="0.30"/>
            <line x1="1780" y1="790" x2="1850" y2="620" stroke="#C25A5A" stroke-width="1.5" opacity="0.30"/>
        </svg>
    </div>

    <!-- ========== MAIN CONTENT ========== -->
    <main class="lh-main">

        <!-- Heading -->
        <div class="lh-heading">
            <h1 class="lh-heading__title">Pastikan Fakta dengan Mudah</h1>
            <p class="lh-heading__subtitle">Verifikasi berbagai jenis informasi dengan mudah di Lensa Hoax</p>
        </div>

        <!-- Two-panel cards -->
        <div class="lh-panels">

            <!-- Panel Kiri: Input -->
            <div class="lh-card lh-card--input">
                <div class="lh-card__body">
                    <textarea
                        class="lh-textarea"
                        id="inputInformasi"
                        name="informasi"
                        placeholder="Ketik, Tempel, atau Unggah informasi ....."
                    ></textarea>
                </div>
                <div class="lh-card__footer">
                    <button class="lh-btn lh-btn--upload" id="btnUnggah" type="button">
                        <iconify-icon icon="ic:sharp-upload" width="22" height="22"></iconify-icon>
                        Unggah Gambar
                    </button>
                    <!-- Hidden file input -->
                    <input type="file" id="fileInput" accept="image/*" style="display:none;">

                    <button class="lh-btn lh-btn--search" id="btnTelusuri" type="button">
                        <iconify-icon icon="ic:outline-search" width="22" height="22"></iconify-icon>
                        Telusuri
                    </button>
                </div>
            </div>

            <!-- Panel Kanan: Hasil -->
            <div class="lh-card lh-card--result">
                <div class="lh-card__header">
                    <iconify-icon icon="ic:outline-search" class="lh-result-icon" width="32" height="32"></iconify-icon>
                    <span class="lh-result-title">Hasil Penelusuran</span>
                </div>
                <div class="lh-card__body lh-card__body--result" id="hasilPenelusuran">
                    <!-- Hasil akan muncul di sini -->
                </div>
            </div>

        </div>
    </main>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user/background.css') }}">
<link rel="stylesheet" href="{{ asset('css/user/navbar.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/pencarian.js') }}"></script>
@endpush
