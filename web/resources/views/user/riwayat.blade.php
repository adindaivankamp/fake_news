@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user/background.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user/riwayat.css') }}?v=3">
    <link rel="stylesheet" href="{{ asset('css/user/profile-popup.css') }}">
@endpush

@section('content')
    <div class="riwayat-page">
        @include('user.partials.hero-bg')
        @include('user.partials.navbar', ['activeRiwayat' => true])

        <main class="lh-main riwayat-content">
            <div class="riwayat-container">
                <div class="riwayat-header">
                    <h1>Riwayat Pencarian Anda</h1>
                    <button class="btn-hapus-semua" id="hapusSemuaBtn">
                        <iconify-icon icon="mdi:trash" width="16" height="16"></iconify-icon>
                        Hapus Semua Riwayat
                    </button>
                </div>

                <div class="riwayat-list" id="riwayatContainer">
                </div>

                <div class="riwayat-empty" id="emptyState" style="display: none;">
                    <iconify-icon icon="mdi:history" width="80" height="80"></iconify-icon>
                    <h2>Tidak Ada Riwayat</h2>
                    <p>Mulai pencarian untuk melihat riwayat Anda di sini</p>
                    <a href="{{ route('beranda') }}" class="btn-mulai-pencarian">
                        <iconify-icon icon="mdi:magnify" width="18" height="18"></iconify-icon>
                        Mulai Pencarian
                    </a>
                </div>
            </div>
        </main>

        @auth
            @include('user.partials.profile-popup', ['popupId' => 'user-profile-popup'])
        @endauth
    </div>
@endsection

@push('scripts')
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <script src="{{ asset('js/user/riwayat.js') }}?v=2"></script>
    <script src="{{ asset('js/user/profile-popup.js') }}"></script>
@endpush
