<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi OTP - Lensa Hoax</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/login-token.css') }}">
</head>
<body>

    <header class="lh-navbar">
        <a href="{{ route('landing') }}" class="lh-logo" aria-label="Lensa Hoax">
            <img src="{{ asset('img/logo-lensa.png') }}" alt="Logo Lensa Hoax" class="lh-logo__img">
        </a>

        <nav class="lh-nav-icons" aria-label="Aksi pengguna">
            <a href="#" class="lh-nav-btn" aria-label="Tren" title="Trending">
                <iconify-icon icon="iconamoon:trend-up-fill" width="26" height="26"></iconify-icon>
                <span class="lh-nav-tooltip" role="tooltip">
                    <iconify-icon icon="iconamoon:trend-up-fill" width="18" height="18"></iconify-icon>
                    <span>Pencarian Terpopuler</span>
                </span>
            </a>

            <a href="{{ route('whatsapp.page') }}" class="lh-nav-btn" aria-label="WhatsApp" title="WhatsApp">
                <iconify-icon icon="garden:whatsapp-fill-16" width="24" height="24"></iconify-icon>
                <span class="lh-nav-tooltip" role="tooltip">
                    <iconify-icon icon="garden:whatsapp-fill-16" width="18" height="18"></iconify-icon>
                    <span>Hubungkan via WhatsApp</span>
                </span>
            </a>
        </nav>
    </header>

    <main class="otp-page">
        <div class="otp-page__hero" aria-hidden="true">
            <img src="{{ asset('img/login.png') }}" alt="Ilustrasi verifikasi akun" class="hero-image">
            <div class="hero-fade"></div>
        </div>

        <section class="otp-panel">
            <div class="otp-panel__inner">
                <h1 class="otp-panel__title">Verifikasi WhatsApp</h1>

                @if(session('phone_number'))
                    <p class="otp-panel__subtitle">
                        Masukkan kode OTP 6 digit yang sudah kami kirim ke nomor WhatsApp Anda.
                    </p>
                @else
                    <p class="otp-panel__subtitle">
                        Masukkan nomor WhatsApp Anda untuk menerima kode OTP.
                    </p>
                @endif

                @if(session('success'))
                    <div class="alert alert--success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert--error">{{ session('error') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert--error">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if(session('phone_number'))
                    <form method="POST" action="{{ url('/login-wa/verify') }}" class="otp-form">
                        @csrf

                        <label for="token" class="form-label">Kode OTP</label>
                        <input
                            id="token"
                            type="text"
                            name="token"
                            class="form-input form-input--otp"
                            placeholder="Contoh: 123456"
                            maxlength="6"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            autocomplete="one-time-code"
                            required
                        >

                        <button type="submit" class="btn-primary">
                            Verifikasi dan Masuk
                        </button>
                    </form>

                    <form method="POST" action="{{ url('/login-wa/request') }}" class="otp-form otp-form--secondary">
                        @csrf
                        <input type="hidden" name="phone_number" value="{{ session('phone_number') }}">
                        <button type="submit" class="btn-secondary">
                            Kirim Ulang OTP
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ url('/login-wa/request') }}" class="otp-form">
                        @csrf

                        <label for="phone_number" class="form-label">Nomor WhatsApp</label>
                        <input
                            id="phone_number"
                            type="text"
                            name="phone_number"
                            class="form-input"
                            placeholder="Contoh: 08123456789"
                            value="{{ old('phone_number') }}"
                            autocomplete="tel"
                            required
                        >

                        <button type="submit" class="btn-primary">
                            Kirim Kode OTP
                        </button>
                    </form>
                @endif

                <div class="otp-panel__back">
                    <a href="{{ route('login') }}" class="back-link">Kembali ke Halaman Masuk &rsaquo;</a>
                </div>
            </div>
        </section>
    </main>

</body>
</html>