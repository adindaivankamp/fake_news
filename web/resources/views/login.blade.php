<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk - Lensa Hoax</title>

    {{-- Google Fonts: DM Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    {{-- Vite / compiled CSS (gunakan salah satu sesuai setup) --}}
    {{-- @vite(['resources/css/login.css']) --}}

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

    {{-- ===== NAVBAR ===== --}}
    <header class="lh-navbar">
        <a href="{{ route('landing') }}" class="lh-logo" aria-label="Lensa Hoax">
            <img src="{{ asset('img/logo-lensa.png') }}" alt="Logo Lensa Hoax" class="lh-logo__img">
        </a>

        <nav class="lh-nav-icons" aria-label="Aksi pengguna">
            {{-- Trending button --}}
            <a href="{{ route('pencarian.populer') }}" class="lh-nav-btn" aria-label="Tren" title="Trending">
                <iconify-icon icon="iconamoon:trend-up-fill" width="26" height="26"></iconify-icon>
                <span class="lh-nav-tooltip" role="tooltip">
                    <iconify-icon icon="iconamoon:trend-up-fill" width="18" height="18"></iconify-icon>
                    <span>Pencarian Terpopuler</span>
                </span>
            </a>

            {{-- WhatsApp button --}}
            <a href="{{ route('whatsapp.page') }}" class="lh-nav-btn" aria-label="WhatsApp" title="WhatsApp">
                <iconify-icon icon="garden:whatsapp-fill-16" width="24" height="24"></iconify-icon>
                <span class="lh-nav-tooltip" role="tooltip">
                    <iconify-icon icon="garden:whatsapp-fill-16" width="18" height="18"></iconify-icon>
                    <span>Hubungkan via WhatsApp</span>
                </span>
            </a>
        </nav>
    </header>

    {{-- ===== MAIN CONTENT ===== --}}
    <main class="login-page">
        {{-- Left: hero image area --}}
        <div class="login-page__hero" aria-hidden="true">
            <img src="{{ asset('img/login.png') }}" alt="Ilustrasi keamanan data" class="hero-image">
            <div class="hero-fade"></div>
        </div>

        {{-- Right: form panel --}}
        <section class="login-panel">
            <div class="login-panel__inner">

                <h1 class="login-panel__title">Hai, Selamat datang !</h1>
                <p class="login-panel__subtitle">Bergabung untuk mendapatkan akses yang lebih luas</p>

                <div class="login-panel__buttons">

                    {{-- Google Login --}}
                    <a href="{{ route('google.redirect') }}" class="btn-social btn-social--google">
                        <span class="btn-social__icon">
                            {{-- Google "G" SVG --}}
                            <svg width="28" height="28" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>
                                <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/>
                                <path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238A11.91 11.91 0 0124 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/>
                                <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303a12.04 12.04 0 01-4.087 5.571l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/>
                            </svg>
                        </span>
                        <span class="btn-social__label">Hubungkan dengan Akun Google</span>
                    </a>

                    {{-- WhatsApp Login --}}
                    <a href="{{ route('login.wa') }}" class="btn-social btn-social--whatsapp">
                        <span class="btn-social__icon">
                            {{-- WhatsApp SVG --}}
                            <svg width="30" height="30" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <circle cx="24" cy="24" r="23" fill="url(#waGrad)"/>
                                <path d="M33.944 13.998C31.416 11.466 28.054 10.07 24.479 10.07c-7.375 0-13.38 6.003-13.38 13.378 0 2.357.614 4.658 1.784 6.685L11 37l7.045-1.847a13.38 13.38 0 006.398 1.628h.006c7.371 0 13.374-6.003 13.374-13.378 0-3.574-1.39-6.936-3.879-9.405zM24.479 34.66a11.1 11.1 0 01-5.664-1.547l-.405-.241-4.204.986 1.003-4.1-.264-.421a11.104 11.104 0 01-1.703-5.881c0-6.136 4.993-11.127 11.133-11.127a11.07 11.07 0 017.875 3.262 11.063 11.063 0 013.254 7.876c-.004 6.14-4.997 11.193-11.025 11.193zm6.107-8.33c-.335-.168-1.981-.977-2.288-1.089-.306-.11-.529-.168-.752.168-.223.335-.863 1.089-1.058 1.312-.195.224-.391.251-.726.084-.335-.168-1.415-.521-2.695-1.663-.997-.888-1.67-1.983-1.865-2.318-.196-.335-.021-.516.147-.683.151-.152.335-.391.503-.587.167-.195.223-.335.335-.559.112-.223.056-.419-.028-.587-.084-.168-.752-1.816-1.031-2.487-.271-.652-.548-.563-.752-.574-.195-.01-.419-.012-.642-.012-.224 0-.587.084-.894.419-.307.335-1.172 1.145-1.172 2.793 0 1.648 1.2 3.24 1.368 3.463.167.223 2.36 3.604 5.717 5.054.799.346 1.423.552 1.909.706.802.254 1.533.218 2.11.132.644-.095 1.981-.809 2.261-1.59.279-.78.279-1.449.195-1.59-.083-.14-.307-.224-.642-.392z" fill="white"/>
                                <defs>
                                    <linearGradient id="waGrad" x1="0" y1="48" x2="48" y2="0" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#1FAF38"/>
                                        <stop offset="1" stop-color="#60D669"/>
                                    </linearGradient>
                                </defs>
                            </svg>
                        </span>
                        <span class="btn-social__label">Hubungkan dengan Akun Whatsapp</span>
                    </a>

                </div>

                <div class="login-panel__back">
                    <a href="{{ route('landing') }}" class="back-link">Kembali ke Halaman Utama &rsaquo;</a>
                </div>

            </div>
        </section>
    </main>

</body>
</html>
