<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lensa Hoax - @yield('title', 'Pencarian')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;700&family=Istok+Web:wght@400;700&family=Poppins:wght@400;700&display=swap" rel="stylesheet">

    <!-- Iconify (untuk ikon) -->
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('css/user/pencarian.css') }}">

    @stack('styles')
</head>
<body>
    @yield('content')

    @stack('scripts')
</body>
</html>
