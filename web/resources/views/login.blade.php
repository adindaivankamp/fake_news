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

    {{-- Vite / compiled CSS (gunakan salah satu sesuai setup) --}}
    {{-- @vite(['resources/css/login.css']) --}}

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

    {{-- ===== NAVBAR ===== --}}
    <header class="navbar">
        <a href="{{ route('landing') }}" class="navbar__logo" aria-label="Lensa Hoax">
            <img src="{{ asset('img/logo-lensa.png') }}" alt="Logo Lensa Hoax" class="navbar__logo-img">
        </a>

        <nav class="navbar__actions">
            {{-- Trending button --}}
            <a href="#" class="nav-btn nav-btn--trend" title="Trending">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <polyline points="22 7 13.5 15.5 8.5 10.5 2 17" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    <polyline points="16 7 22 7 22 13" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
            {{-- WhatsApp button --}}
            <a href="https://wa.me/" target="_blank" class="nav-btn nav-btn--wa" title="WhatsApp">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z" fill="#fff"/>
                    <path d="M12.004 2C6.477 2 2 6.477 2 12.004c0 1.763.461 3.418 1.268 4.852L2 22l5.31-1.237A9.956 9.956 0 0012.004 22C17.53 22 22 17.523 22 12.004 22 6.477 17.53 2 12.004 2zm0 18.172a8.15 8.15 0 01-4.149-1.131l-.297-.177-3.154.735.765-3.073-.194-.316a8.169 8.169 0 01-1.314-4.51c0-4.517 3.674-8.19 8.19-8.19 4.517 0 8.19 3.673 8.19 8.19 0 4.516-3.673 8.172-8.037 8.172z" fill="#fff"/>
                </svg>
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
                    <a href="#" class="btn-social btn-social--google" aria-disabled="true">
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
                    <a href="#" class="btn-social btn-social--whatsapp" aria-disabled="true">
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
                        <span class="btn-social__label">Dapatkan melalui Whatsapp</span>
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
