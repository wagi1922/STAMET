

{{-- resources/views/index.blade.php --}}

{{-- 1. Beritahu Laravel untuk menggunakan bingkai 'app.blade.php' --}}
@extends('layouts.app')

{{-- 2. Definisikan konten yang akan disuntikkan ke dalam @yield('content') --}}
@section('content')
<div class="page-wrapper">
    <main class="main-content container">
        <div class="container">

    @include('components.beranda')
    @include('components.peringatan_dini')
    @include('components.youtube')
    @include('components.earthquake')
    @include('components.air_quality')

    <div class="home-row-display">
        @include('components.hotspot_map')
        @include('components.fdrs')
    </div>

    @include('components.satellite')
    @include('components.news')

</div>
</div>
    </main>
@endsection