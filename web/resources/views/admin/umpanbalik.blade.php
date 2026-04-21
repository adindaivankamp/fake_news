@extends('layouts.admin')

@section('title', 'Umpan Balik')

@section('content')

<div class="umpanbalik-container">

    <!-- CARD -->
    <div class="umpanbalik-card">
        <div class="umpanbalik-header">
            <h3>Budi Pratomo</h3>
            <span>Kamis, 9 April 2027</span>
        </div>

        <p>
            Respon lambat dan jawabannya tidak sesuai. Ini membuat saya rugi waktu.
            Saya minta eskalasi ke supervisor Anda.
        </p>

        <div class="umpanbalik-action">
            <button>Detail Informasi</button>
        </div>
    </div>

    <div class="umpanbalik-card">
        <div class="umpanbalik-header">
            <h3>Juliana</h3>
            <span>Kamis, 9 April 2027</span>
        </div>

        <p>
            Terima kasih atas jawaban yang panjang dan detailnya. Namun, mohon maaf,
            respon tersebut belum menjawab inti pertanyaan saya...
        </p>

        <div class="umpanbalik-action">
            <button>Detail Informasi</button>
        </div>
    </div>

</div>

@endsection