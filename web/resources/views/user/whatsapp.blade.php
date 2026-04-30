<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lensa Hoax - Dapatkan Melalui WhatsApp</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;700;800&family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/user/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user/whatsapp.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user/profile-popup.css') }}">
</head>
<body>
    <div class="wa-page">
        @include('user.partials.navbar', ['variant' => 'wa', 'activeWhatsApp' => true])

        <main class="wa-content">
            <section class="wa-hero">
                <div class="wa-hero__text">
                    <h1>Cek Hoax Langsung<br>Via Whatsapp</h1>
                    <p>
                        Lebih praktis tanpa ribet. Kini Anda dapat memverifikasi kebenaran informasi
                        langsung melalui WhatsApp.
                    </p>
                    <p>
                        Cukup kirim atau teruskan (forward) pesan yang Anda terima tanpa perlu membuka website.
                    </p>
                    <a id="wa-cta" href="#" class="wa-btn-cta">
                        <iconify-icon icon="garden:whatsapp-fill-16" width="25" height="25"></iconify-icon>
                        Cek Via WhatsApp
                    </a>
                </div>

                <div class="wa-hero__visual" aria-hidden="true">
                    <img src="{{ asset('img/wa-top.png') }}" alt="Ilustrasi WhatsApp" class="wa-hero__image">
                </div>
            </section>

            <section class="wa-reason">
                <h2>Mengapa menggunakan WhatsApp?</h2>
                <div class="wa-reason__row">
                    <div class="wa-brand-circle">
                        <img src="{{ asset('img/wa-3d.png') }}" alt="Ilustrasi WhatsApp 3D" class="wa-brand-image">
                    </div>
                    <div class="wa-benefits">
                        <div class="wa-pill wa-pill--blue">Lebih cepat dan praktis</div>
                        <div class="wa-pill wa-pill--pink">Tidak memerlukan login atau pendaftaran</div>
                        <div class="wa-pill wa-pill--green">Dapat langsung meneruskan pesan dari grup</div>
                        <div class="wa-pill wa-pill--yellow">Cocok untuk semua pengguna, termasuk yang belum terbiasa dengan teknologi</div>
                    </div>
                </div>
            </section>

            <section class="wa-steps">
                <h2>Cara Menggunakannya</h2>
                <div class="wa-steps__grid">
                    <article class="wa-step-card">
                        <iconify-icon icon="mdi:cursor-default-click" width="52" height="52"></iconify-icon>
                        <h3>Klik tombol WhatsApp</h3>
                        <p>Anda akan langsung diarahkan ke percakapan dengan Lensa Hoax Bot.</p>
                    </article>

                    <div class="wa-step-arrow"><iconify-icon icon="mdi:arrow-right-bold" width="46" height="46"></iconify-icon></div>

                    <article class="wa-step-card">
                        <iconify-icon icon="mdi:message-question" width="52" height="52"></iconify-icon>
                        <h3>Kirim atau teruskan pesan</h3>
                        <p>Kirim berita, gambar, atau tautan yang ingin Anda periksa.</p>
                    </article>

                    <div class="wa-step-arrow"><iconify-icon icon="mdi:arrow-right-bold" width="46" height="46"></iconify-icon></div>

                    <article class="wa-step-card">
                        <iconify-icon icon="line-md:loading-alt-loop" width="52" height="52"></iconify-icon>
                        <h3>Tunggu hasil verifikasi</h3>
                        <p>Sistem akan menganalisis dan memberikan hasil apakah informasi tersebut hoaks atau fakta.</p>
                    </article>
                </div>
            </section>

            <section class="wa-tips">
                <h2>Tips</h2>
                <div class="wa-tips__content">
                    <ul>
                        <li>Gunakan fitur forward di WhatsApp agar lebih cepat.</li>
                        <li>Anda dapat mengirim teks, gambar, maupun tautan.</li>
                        <li>Pastikan pesan yang dikirim jelas agar hasil lebih akurat.</li>
                    </ul>
                    <a href="#wa-cta" class="wa-btn-cta wa-btn-cta--small">
                        <iconify-icon icon="garden:whatsapp-fill-16" width="24" height="24"></iconify-icon>
                        Cek Via WhatsApp
                    </a>
                </div>
            </section>
        </main>
    </div>
    <script src="{{ asset('js/user/profile-popup.js') }}"></script>
</body>
</html>
