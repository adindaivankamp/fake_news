@php
    $isWhatsAppVariant = isset($variant) && $variant === 'wa';
    $isMobileLayout = $isWhatsAppVariant
        ? false
        : (
            request()->header('Sec-CH-UA-Mobile') === '?1'
            || preg_match('/Mobile|Android|iPhone|iPad|iPod/i', request()->userAgent() ?? '')
        );
    $navbarModeClass = $isMobileLayout ? 'lh-navbar--mobile' : 'lh-navbar--desktop';
    $isLandingActive = request()->routeIs('landing');
    $isSearchActive = request()->routeIs('beranda');
    $isPopularActive = request()->routeIs('pencarian.populer');
    $isWhatsAppActive = request()->routeIs('whatsapp.page');
    $isHistoryActive = request()->routeIs('riwayat');
    $isLoginActive = request()->routeIs('login');
@endphp

<header class="lh-navbar {{ $navbarModeClass }} {{ $isWhatsAppVariant ? 'wa-navbar' : '' }}">
    <!-- Logo -->
    @if($isWhatsAppVariant)
        <a href="{{ route('beranda') }}" class="lh-logo wa-logo" aria-label="Kembali ke Pencarian">
            <img src="{{ asset('img/logo-lensa.png') }}" alt="Logo Lensa Hoax" class="lh-logo__img wa-logo__img">
        </a>
    @else
        <a href="{{ route('beranda') }}" class="lh-logo {{ $isMobileLayout ? 'lh-logo--mobile' : 'lh-logo--desktop' }}">
            <img src="{{ asset('img/logo-lensa.png') }}" alt="Logo Lensa Hoax" class="lh-logo__img">
        </a>
    @endif

    <!-- Nav Icons -->
    @if($isWhatsAppVariant)
        <nav class="lh-nav-icons wa-nav-actions" aria-label="Aksi pengguna">
    @else
        <nav class="lh-nav-icons {{ $isMobileLayout ? 'lh-nav-icons--mobile' : 'lh-nav-icons--desktop' }}">
    @endif
        <a href="{{ route('landing') }}" class="lh-nav-btn {{ $isWhatsAppVariant ? 'wa-nav-btn' : '' }} {{ $isLandingActive ? 'lh-nav-btn--active' : '' }}" aria-label="Beranda Utama" @if($isLandingActive) aria-current="page" @endif>
            <iconify-icon icon="mdi:home" width="24" height="24"></iconify-icon>
            <span class="lh-nav-tooltip" role="tooltip">
                <iconify-icon icon="mdi:home" width="17" height="17"></iconify-icon>
                <span>Beranda Utama</span>
            </span>
        </a>
        <a href="{{ route('pencarian.populer') }}" class="lh-nav-btn {{ $isWhatsAppVariant ? 'wa-nav-btn' : '' }} {{ $isPopularActive ? 'lh-nav-btn--active' : '' }}" aria-label="Pencarian Terpopuler" @if($isPopularActive) aria-current="page" @endif>
            <iconify-icon icon="iconamoon:trend-up-fill" width="26" height="26"></iconify-icon>
            <span class="lh-nav-tooltip" role="tooltip">
                <iconify-icon icon="iconamoon:trend-up-fill" width="18" height="18"></iconify-icon>
                <span>Pencarian Terpopuler</span>
            </span>
        </a>
        <a href="{{ route('riwayat') }}" class="lh-nav-btn {{ $isWhatsAppVariant ? 'wa-nav-btn' : '' }} {{ $isHistoryActive ? 'lh-nav-btn--active' : '' }}" aria-label="Riwayat" @if($isHistoryActive) aria-current="page" @endif>
            <iconify-icon icon="fontisto:history" width="24" height="24"></iconify-icon>
            <span class="lh-nav-tooltip" role="tooltip">
                <iconify-icon icon="fontisto:history" width="17" height="17"></iconify-icon>
                <span>Riwayat Pencarian Anda</span>
            </span>
        </a>
        <a href="{{ route('beranda') }}" class="lh-nav-btn lh-nav-btn--search {{ $isWhatsAppVariant ? 'wa-nav-btn' : '' }} {{ $isSearchActive ? 'lh-nav-btn--active' : '' }}" aria-label="Pencarian" @if($isSearchActive) aria-current="page" @endif>
            <iconify-icon icon="mdi:magnify" width="24" height="24"></iconify-icon>
            <span class="lh-nav-tooltip" role="tooltip">
                <iconify-icon icon="mdi:magnify" width="17" height="17"></iconify-icon>
                <span>Telusuri Informasi Berita</span>
            </span>
        </a>
          <a href="{{ route('whatsapp.page') }}"
              class="lh-nav-btn {{ $isWhatsAppVariant ? 'lh-nav-btn--whatsapp-visible wa-nav-btn wa-nav-btn--whatsapp' : '' }} {{ $isWhatsAppActive ? 'lh-nav-btn--active' : '' }}"
              aria-label="WhatsApp" @if($isWhatsAppActive) aria-current="page" @endif>
            <iconify-icon icon="garden:whatsapp-fill-16" width="24" height="24"></iconify-icon>
            <span class="lh-nav-tooltip {{ $isWhatsAppVariant ? 'lh-nav-tooltip--always-visible' : '' }}" role="tooltip">
                <iconify-icon icon="garden:whatsapp-fill-16" width="18" height="18"></iconify-icon>
                <span>Dapatkan Melalui Whatsapp</span>
            </span>
        </a>
        @auth
            <a href="#"
            class="lh-nav-btn lh-nav-btn--user js-profile-toggle {{ $isWhatsAppVariant ? 'wa-nav-btn' : '' }}"
            aria-label="Profil"
            aria-controls="user-profile-popup"
            aria-expanded="false"
            data-profile-toggle="user-profile-popup"
            data-navbar-role="profile">

                <iconify-icon icon="mdi:user" width="26" height="26"></iconify-icon>

                <span class="lh-nav-tooltip lh-nav-tooltip--left" role="tooltip">
                    <iconify-icon icon="mdi:user" width="18" height="18"></iconify-icon>
                    <span>Profil Pengguna</span>
                </span>
            </a>
        @else
            <a href="{{ route('login') }}"
            class="lh-nav-btn lh-nav-btn--user {{ $isWhatsAppVariant ? 'wa-nav-btn' : '' }} {{ $isLoginActive ? 'lh-nav-btn--active' : '' }}"
            aria-label="Daftar atau Masuk" @if($isLoginActive) aria-current="page" @endif>

                <iconify-icon icon="mdi:user" width="26" height="26"></iconify-icon>

                <span class="lh-nav-tooltip lh-nav-tooltip--left" role="tooltip">
                    <iconify-icon icon="mdi:user" width="18" height="18"></iconify-icon>
                    <span>Daftar | Masuk</span>
                </span>
            </a>
        @endauth
    </nav>

    @auth
        @include('user.partials.profile-popup', ['popupId' => 'user-profile-popup'])
    @endauth
</header>
