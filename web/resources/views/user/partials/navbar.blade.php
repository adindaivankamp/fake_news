<header class="lh-navbar {{ isset($variant) && $variant === 'wa' ? 'wa-navbar' : '' }}">
    <!-- Logo -->
    <a href="{{ route('beranda') }}" class="lh-logo {{ isset($variant) && $variant === 'wa' ? 'wa-logo' : '' }}" @if(isset($variant) && $variant === 'wa') aria-label="Kembali ke Pencarian" @endif>
        <img src="{{ asset('img/logo-lensa.png') }}" alt="Logo Lensa Hoax" class="lh-logo__img {{ isset($variant) && $variant === 'wa' ? 'wa-logo__img' : '' }}">
    </a>

    <!-- Nav Icons -->
    <nav class="lh-nav-icons {{ isset($variant) && $variant === 'wa' ? 'wa-nav-actions' : '' }}" @if(isset($variant) && $variant === 'wa') aria-label="Aksi pengguna" @endif>
        <a href="{{ route('pencarian.populer') }}" class="lh-nav-btn {{ isset($variant) && $variant === 'wa' ? 'wa-nav-btn' : '' }}" aria-label="Pencarian Terpopuler">
            <iconify-icon icon="iconamoon:trend-up-fill" width="26" height="26"></iconify-icon>
            <span class="lh-nav-tooltip {{ isset($variant) && $variant === 'wa' ? '' : '' }}" role="tooltip">
                <iconify-icon icon="iconamoon:trend-up-fill" width="18" height="18"></iconify-icon>
                <span>Pencarian Terpopuler</span>
            </span>
        </a>
        <a href="#" class="lh-nav-btn {{ isset($variant) && $variant === 'wa' ? 'wa-nav-btn' : '' }}" aria-label="Riwayat" onclick="alert('Fitur Riwayat akan segera hadir')">
            <iconify-icon icon="fontisto:history" width="24" height="24"></iconify-icon>
            <span class="lh-nav-tooltip" role="tooltip">
                <iconify-icon icon="fontisto:history" width="17" height="17"></iconify-icon>
                <span>Riwayat Pencarian Anda</span>
            </span>
        </a>
        <a href="{{ route('whatsapp.page') }}"
           class="lh-nav-btn {{ isset($variant) && $variant === 'wa' ? 'lh-nav-btn--whatsapp-visible wa-nav-btn wa-nav-btn--whatsapp' : '' }} {{ isset($activeWhatsApp) && $activeWhatsApp ? 'wa-nav-btn--active' : '' }}"
           aria-label="WhatsApp" @if(isset($activeWhatsApp) && $activeWhatsApp) aria-current="page" @endif>
            <iconify-icon icon="garden:whatsapp-fill-16" width="24" height="24"></iconify-icon>
            <span class="lh-nav-tooltip {{ isset($variant) && $variant === 'wa' ? 'lh-nav-tooltip--always-visible' : '' }}" role="tooltip">
                <iconify-icon icon="garden:whatsapp-fill-16" width="18" height="18"></iconify-icon>
                <span>Dapatkan Melalui Whatsapp</span>
            </span>
        </a>
        <a href="#" class="lh-nav-btn lh-nav-btn--user js-profile-toggle {{ isset($variant) && $variant === 'wa' ? 'wa-nav-btn' : '' }}" aria-label="Profil" aria-controls="user-profile-popup" aria-expanded="false" data-profile-toggle="user-profile-popup">
            <iconify-icon icon="mdi:user" width="26" height="26"></iconify-icon>
            <span class="lh-nav-tooltip lh-nav-tooltip--left" role="tooltip">
                <iconify-icon icon="mdi:user" width="18" height="18"></iconify-icon>
                <span>Profil Pengguna</span>
            </span>
        </a>
    </nav>

    @include('user.partials.profile-popup', ['popupId' => 'user-profile-popup'])
</header>
