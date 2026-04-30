@php
    $popupId = $popupId ?? 'user-profile-popup';
    $user = auth()->user();
@endphp

<div id="{{ $popupId }}" class="lh-profile-popup" hidden>
    <div class="lh-profile-popup__card" role="dialog" aria-modal="false" aria-label="Profil pengguna">
        <!-- Avatar -->
        <div class="lh-profile-popup__avatar" aria-hidden="true">
            <iconify-icon icon="mdi:user" width="40" height="40"></iconify-icon>
        </div>

        <!-- Name Field -->
        <div class="lh-profile-popup__field js-editable-field" data-field="name" title="Klik untuk mengedit">
            <iconify-icon icon="mdi:account"></iconify-icon>
            <span>{{ $user?->name ?? 'Tambahkan nama' }}</span>
        </div>

        <!-- Email Field -->
        <div class="lh-profile-popup__field js-editable-field" data-field="email" title="Klik untuk mengedit">
            <iconify-icon icon="mdi:email"></iconify-icon>
            <span>{{ $user?->email ?? 'Tambahkan email' }}</span>
        </div>

        <!-- Phone Field -->
        <div class="lh-profile-popup__field js-editable-field" data-field="phone" title="Klik untuk mengedit">
            <iconify-icon icon="mdi:whatsapp"></iconify-icon>
            <span>{{ $user?->phone_number ?: 'Tambahkan nomor' }}</span>
        </div>

        <!-- Logout Button -->
        @auth
            <form method="POST" action="{{ route('logout') }}" class="lh-profile-popup__logout-form">
                @csrf
                <button type="submit" class="lh-profile-popup__logout">
                    <iconify-icon icon="mdi:logout"></iconify-icon>
                    <span>Keluar Akun</span>
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="lh-profile-popup__logout">
                <iconify-icon icon="mdi:login"></iconify-icon>
                <span>Masuk</span>
            </a>
        @endauth
    </div>
</div>

<!-- Modal Edit Field -->
<div id="field-edit-modal" class="lh-field-edit-modal" hidden>
    <div class="lh-field-edit-modal__overlay"></div>
    <div class="lh-field-edit-modal__card">
        <div class="lh-field-edit-modal__header">
            <h3 id="field-edit-title">Edit Informasi</h3>
            <button class="lh-field-edit-modal__close" type="button" aria-label="Tutup">
                <iconify-icon icon="mdi:close" width="24" height="24"></iconify-icon>
            </button>
        </div>
        <form class="lh-field-edit-modal__form" id="field-edit-form">
            @csrf
            <input type="hidden" id="field-edit-type" name="field_type" value="">
            <input 
                type="text" 
                id="field-edit-input" 
                name="field_value" 
                class="lh-field-edit-modal__input" 
                placeholder="Masukkan nilai baru"
                required
            >
            <div class="lh-field-edit-modal__actions">
                <button type="button" class="lh-field-edit-modal__btn-cancel" id="field-edit-cancel">
                    Batal
                </button>
                <button type="submit" class="lh-field-edit-modal__btn-save">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
