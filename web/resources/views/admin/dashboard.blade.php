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

<h2 class="judul-section">Berita Teratas Hari ini</h2>

<div class="news-container">
    <div class="news-card"></div>
    <div class="news-card"></div>
</div>

@endsection