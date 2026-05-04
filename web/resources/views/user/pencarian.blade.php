@extends('layouts.app')

@section('title', 'Pencarian')

@section('content')
<div class="lh-wrapper">

    @include('user.partials.navbar')

    @include('user.partials.hero-bg')

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
                    <!-- Image preview area -->
                    <div id="imagePreviewContainer" style="display: none; margin-top: 16px;">
                        <img id="imagePreview" src="" alt="Preview gambar" style="max-width: 100%; max-height: 300px; border-radius: 8px; display: block; margin-bottom: 8px;">
                        <p id="imagePreviewText" style="text-align: center; font-size: 12px; color: #666; margin: 0;">
                            ✓ Gambar siap untuk diteliti
                        </p>
                    </div>
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
<link rel="stylesheet" href="{{ asset('css/user/profile-popup.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/pencarian.js') }}"></script>
<script src="{{ asset('js/user/profile-popup.js') }}"></script>
@endpush
