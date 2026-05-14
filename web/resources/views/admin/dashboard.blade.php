@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/dashboard-style.css') }}">
@endpush

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
        </div>
        
        <div class="dashboard-popular-grid">

            @foreach($dashboardPopular as $item)

            <div class="dashboard-popular-card">

                <div class="dashboard-popular-rank">
                    #{{ $item['rank'] }} {{ $item['badge'] }}
                </div>

                <div class="dashboard-popular-excerpt">
                    {{ $item['title'] }}
                </div>

                <div class="dashboard-popular-content">

                    <div class="dashboard-popular-row">

                        <span class="dashboard-popular-badge">
                            {{ $item['badge'] }}
                        </span>

                        <h3 class="dashboard-popular-headline">
                            {{ $item['headline'] }}
                        </h3>

                    </div>

                    <p class="dashboard-popular-count">
                        <strong>{{ $item['count'] }}</strong>
                        orang mencari informasi serupa
                    </p>

                    <div class="dashboard-popular-footer">

                        <a href="{{ route('pencarian.populer') }}"
                        class="dashboard-popular-btn">

                            Detail Lengkap

                        </a>

                    </div>

                </div>

            </div>

            @endforeach

        </div>
    </div>

</div>

@endsection