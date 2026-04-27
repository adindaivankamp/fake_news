<!DOCTYPE html>
<html>
<head>
    <title>@yield('title')</title>

    <link rel="stylesheet" href="{{ asset('css/dashboard/admin-style.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<!-- HEADER -->
<div class="header">

    <!-- KIRI -->
    <div class="header-left">
        <img src="{{ asset('img/logo-lensa.png') }}" class="logo-header" alt="Logo">
    </div>

    <!-- KANAN -->
    <div class="header-right">

        @hasSection('header-search')
            @yield('header-search')
        @endif

        <!-- ADMIN INFO -->
        <div class="admin-info">
            <div class="admin-text">
                <strong>Admin</strong>
            </div>
            <div class="admin-icon">
                <i class="fa fa-user"></i>
            </div>
        </div>

    </div>

</div>

<div class="admin-layout">

    <!-- SIDEBAR -->
    <div class="sidebar">

        <ul>
            <div class="sidebar-toggle">
                <button id="toggleSidebar" class="toggle-btn">
                    <i class="fa fa-bars"></i>
                </button>
            </div>

            <li class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
                <a href="/admin/dashboard">
                    <i class="fa fa-home"></i>
                    <span>Beranda</span>
                </a>
            </li>

            <li class="{{ request()->is('admin/cek-berita') ? 'active' : '' }}">
                <a href="/admin/cek-berita">
                    <i class="fa fa-search"></i>
                    <span>Cek Berita</span>
                </a>
            </li>

            <li class="{{ request()->is('admin/riwayat') ? 'active' : '' }}">
                <a href="/admin/riwayat">
                    <i class="fa fa-globe"></i>
                    <span>Riwayat Global</span>
                </a>
            </li>

            <li class="{{ request()->is('admin/user') ? 'active' : '' }}">
                <a href="/admin/user">
                    <i class="fa fa-users"></i>
                    <span>Data Pengguna</span>
                </a>
            </li>

            <li class="{{ request()->is('admin/umpanbalik') ? 'active' : '' }}">
                <a href="/admin/umpanbalik">
                    <i class="fa fa-comment"></i>
                    <span>Umpan Balik</span>
                </a>
            </li>

            <li>
                <a href="#">
                    <i class="fa fa-cog"></i>
                    <span>Setting</span>
                </a>
            </li>
        </ul>

        <div class="logout">
            <a href="#">
                <i class="fa fa-sign-out-alt"></i>
                <span>Keluar Akun</span>
            </a>
        </div>

    </div>

    <!-- MAIN -->
    <div class="main-content">
        @yield('content')
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.querySelector('.sidebar');

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');

        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('active');
        }
    });
});
</script>

</body>
</html>