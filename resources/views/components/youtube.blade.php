{{-- resources/views/components/youtube.blade.php --}}

@php
    // Jangan tampilkan section jika tidak ada data video sama sekali
    if (empty($dataVideo)) {
        return;
    }
    // Ambil video pertama untuk dijadikan video utama, sisanya untuk daftar
    $video_utama = array_shift($dataVideo);
@endphp

<section class="youtube-section">
    <div class="section-header">
        <h2>Prakiraan Cuaca Mingguan</h2>
        <a href="https://www.youtube.com/channel/UC-KYgQQBxl7zNV60yHnHhxA" target="_blank" rel="noopener noreferrer" class="lihat-semua">
            Kunjungi Channel <span class="material-symbols-outlined">arrow_forward</span>
        </a>
    </div>
    <div class="youtube-layout-grid">
        @if ($video_utama)
            <div class="youtube-main-video">
                <div class="video-card-main">
                    <div class="video-embed-container">
                        {{-- URL ini dimodifikasi sesuai file Anda --}}
                        <iframe src="https://www.youtube.com/embed/{{ $video_utama['video_id'] }}" 
                                title="YouTube video player" frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                allowfullscreen>
                        </iframe>
                    </div>
                </div>
            </div>
        @endif

        <div class="youtube-video-list">
            {{-- Loop sisa video yang ada di dalam $dataVideo --}}
            @foreach ($dataVideo as $video)
                <a href="{{ $video['link'] }}" target="_blank" rel="noopener noreferrer" class="video-list-item">
                    <div class="list-item-thumbnail">
                        <img src="{{ $video['thumbnail'] }}" alt="{{ $video['title'] }}" loading="lazy">
                    </div>
                    <h4 class="list-item-title">{{ $video['title'] }}</h4>
                </a>
            @endforeach
        </div>
    </div>
</section>