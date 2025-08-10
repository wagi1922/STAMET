{{-- resources/views/partials/header.blade.php --}}

<header>
    <div class="container">
        <div class="logo">
            {{-- Menggunakan helper route() untuk link ke halaman utama --}}
            <a href="{{ route('home') }}">
                {{-- Menggunakan helper asset() untuk gambar --}}
                <img src="{{ asset('image/logo_stamet.svg') }}" alt="Logo BMKG" class="logo-img">
                <span>STAMET SSK II PEKANBARU</span>
            </a>
        </div>
        <button class="hamburger" id="hamburger-toggle" aria-label="Toggle menu" aria-expanded="false">
            <span class="hamburger-line"></span><span class="hamburger-line"></span><span class="hamburger-line"></span>
        </button>
        <nav>
            <ul id="main-nav-menu">
                {{-- Logika 'active' diubah ke cara Laravel menggunakan request()->routeIs() --}}
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>

                {{-- Untuk menu lain, kita gunakan placeholder '#' karena route-nya belum dibuat --}}
                <li class="dropdown">
                    <a href="#" class="dropbtn">Profil</a>
                    <div class="dropdown-content">
                        <a href="#">Visi & Misi</a>
                        <a href="#">Tugas & Fungsi</a>
                        {{-- ... dan seterusnya untuk link profil --}}
                    </div>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropbtn">Berita</a>
                    <div class="dropdown-content">
                        <a href="#">Berita Utama</a>
                        {{-- ... dan seterusnya untuk link berita --}}
                    </div>
                </li>
                
                {{-- Lanjutkan pola yang sama untuk menu dropdown lainnya --}}
                <li class="dropdown">
                    <a href="#" class="dropbtn">Publik</a>
                    <div class="dropdown-content">
                        <a href="#">Analisis Cuaca</a>
                        <a href="#">Prakiraan Cuaca</a>
                        {{-- ... dst --}}
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Maritim</a>
                    <div class="dropdown-content">
                        <a href="#">Cuaca Pelabuhan</a>
                        {{-- ... dst --}}
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Penerbangan</a>
                    <div class="dropdown-content">
                        <a href="https://inasiam.bmkg.go.id" target="_blank" rel="noopener noreferrer">Ina-SIAM</a>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Pelayanan Data</a>
                    <div class="dropdown-content">
                        <a href="#">Klaim Asuransi/Adendum</a>
                        {{-- ... dst --}}
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</header>