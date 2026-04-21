@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')

<div class="stats">

    <div class="card">
        <i class="fa fa-users icon-red"></i>
        <h3>Pengguna Terdaftar</h3>
        <p>7.543</p>
    </div>

    <div class="card">
        <i class="fa fa-file icon-red"></i>
        <h3>Berita Terdeteksi</h3>
        <p>2.306</p>
    </div>

    <div class="card">
        <i class="fa fa-comment icon-red"></i>
        <h3>Umpan Balik</h3>
        <p>8</p>
    </div>

</div>

<div class="info-update">
    Data terakhir diperbarui pukul 00.00, 20 April 2026
</div>

<div class="popular-section">

    <div class="popular-header">
        <i class="fa fa-chart-line"></i>
        <span>Pencarian Populer</span>
    </div>

    <div class="popular-container">

        <div class="popular-card">
            <div class="rank">1</div>
            <p>Contoh berita hoax tentang wisata</p>
        </div>

        <div class="popular-card">
            <div class="rank">2</div>
            <p>Kabar bantuan sosial Rp1,5 juta</p>
        </div>

        <div class="popular-card">
            <div class="rank">3</div>
            <p>Isu politik dan narasi viral</p>
        </div>

    </div>

</div>

@endsection