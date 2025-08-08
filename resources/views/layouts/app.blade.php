{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Stamet SSK II Pekanbaru' }}</title> {{-- Judul halaman dinamis --}}
    
    {{-- Memanggil Aset CSS --}}
    <link rel="icon" href="{{ asset('image/icon_stamet.svg') }}" type="image/svg">
    <link rel="stylesheet" href="{{ asset('css/style_beranda.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
</head>
<body>
    <div class="page-wrapper">
        
        {{-- Memanggil komponen header --}}
        @include('partials.header')

        {{-- Ini adalah placeholder untuk konten utama dari setiap halaman --}}
        @yield('content')
    
    </div> {{-- Penutup .page-wrapper --}}

    {{-- Memanggil komponen footer --}}
    @include('partials.footer')

    {{-- Memanggil Aset JavaScript --}}
    <script src="{{ asset('hamburger.js') }}"></script>
    <script src="{{ asset('js/profil.js') }}" defer></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script src="{{ asset('js/slide_cuaca_beranda.js') }}" defer></script>
    
    {{-- Ini adalah placeholder untuk script tambahan dari halaman lain --}}
    @stack('scripts')

    
</body>
</html>