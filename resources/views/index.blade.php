{{-- =============================================== --}}
{{-- FILE 1: FILE INDUK (index.blade.php)          --}}
{{-- =============================================== --}}
{{-- File ini berfungsi sebagai layout utama untuk --}}
{{-- memanggil header, footer, dan semua komponen. --}}
{{-- =============================================== --}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prakiraan Cuaca Riau - Stamet SSK II Pekanbaru</title>

    {{-- Link ke Font dan Ikon --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Gruppo&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    {{-- Link ke file CSS menggunakan helper asset() Laravel --}}
    <link rel="stylesheet" href="{{ asset('css/style_beranda.css') }}">
    
</head>
<body>
    {{-- Di sini Anda bisa menaruh @include('layouts.header') jika ada --}}

    <main class="main-content container">

        {{-- Memanggil komponen Beranda. Variabel yang dibuat di dalam --}}
        {{-- beranda.blade.php akan tersedia di file ini. --}}
        @include('components.beranda')

        {{-- Memanggil komponen Peringatan Dini. --}}
        @include('components.peringatan_dini')

        {{-- Memanggil komponen Youtube. --}}
        @include('components.youtube')

        {{-- Memanggil komponen GempaBumi. --}}
        @include('components.earthquake')

        {{-- Memanggil komponen udarah. --}}
        @include('components.air_quality')

    </main>

    {{-- Di sini Anda bisa menaruh @include('layouts.footer') jika ada --}}


    {{-- Data untuk JavaScript --}}
    {{-- Variabel $cuaca_tiga_hari_beranda didefinisikan di dalam --}}
    {{-- components/beranda.blade.php, tapi bisa diakses di sini --}}
    <script>
        var dataPrakiraanTigaHariGlobal = @json($cuaca_tiga_hari_beranda ?? []);
    </script>

    {{-- Script JS menggunakan helper asset() --}}
    <script src="{{ asset('js/slide_cuaca_beranda.js') }}"></script> 

    @stack('scripts')
</body>
</html>