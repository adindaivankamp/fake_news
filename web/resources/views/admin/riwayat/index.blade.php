@extends('layouts.admin')

@section('title', 'Riwayat Global')

@section('content')

<h2 class="judul-section">Riwayat Global</h2>

<div class="riwayat-container">

    @foreach($data as $item)
    <div class="riwayat-card">

        <div class="riwayat-header">
            ⚠️ <b>[KABAR PENTING]</b>
        </div>

        <p class="riwayat-text">
            {{ $item->judul }}
        </p>

        <hr>

        <!-- PROGRESS -->
        <div class="progress-box">
            <div class="progress-circle">
                70%
            </div>

            <div class="progress-text">
                <p><span class="dot red"></span> Data terdeteksi hoax sebesar</p>
                <p><span class="dot green"></span> Data terdeteksi benar sebesar</p>
            </div>
        </div>

        <!-- ACTION -->
        <div class="action-btn">
            <a href="/admin/riwayat/edit/{{ $item->id }}" class="btn-mini">Edit</a>

            <a href="/admin/riwayat/delete/{{ $item->id }}"
               onclick="return confirm('Yakin hapus data?')"
               class="btn-mini delete">
               Hapus
            </a>
        </div>

    </div>
    @endforeach

</div>

@endsection