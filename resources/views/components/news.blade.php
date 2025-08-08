{{-- resources/views/components/news.blade.php --}}

@if ($dataBerita->isNotEmpty())
<section class="berita-section">
    <div class="section-header">
        <h2>Berita Terkini</h2>
        {{-- TODO: Buat halaman arsip berita nanti --}}
        <a href="#" class="lihat-semua">Lihat Semua Berita <span class="material-symbols-outlined">arrow_forward</span></a>
    </div>
    <div class="berita-cards-container">
        @foreach ($dataBerita as $berita)
            <div class="berita-card">
                <div class="berita-card-content">
                    {{-- TODO: Buat halaman detail berita nanti --}}
                    <a href="#" class="card-image-link">
                        <img src="{{ asset($berita['gambar_unggulan']) }}" alt="{{ $berita['judul'] }}" class="berita-card-image" loading="lazy">
                    </a>
                    <p class="berita-card-date">{{ $berita['formatted_date'] }}</p>
                    <h3 class="berita-card-title">
                        <a href="#">{{ $berita['judul'] }}</a>
                    </h3>
                    <p class="berita-card-summary">{{ $berita['summary'] }}</p>
                    <a href="#" class="berita-card-readmore">Baca Selengkapnya</a>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif