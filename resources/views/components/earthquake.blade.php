{{-- resources/views/components/earthquake.blade.php --}}

{{-- Jangan tampilkan apapun jika data gempa tidak berhasil diambil --}}
@if ($dataGempa)
    <section class="gempa-v2-section">
        <div class="gempa-v2-card">
            <div class="gempa-v2-shakemap">
                <img src="{{ $dataGempa['ShakemapUrl'] }}" alt="Peta Guncangan Gempabumi">
            </div>

            <div class="gempa-v2-info">
                <h2 class="info-title">Gempa Bumi Terkini</h2>
                <p class="info-timestamp">
                    {{ $dataGempa['FormattedDateTime'] }}
                </p>

                <div class="info-potensi-tag {{ $dataGempa['potensi_class'] }}">
                    {{ $dataGempa['Potensi'] }}
                </div>

                <p class="info-wilayah">{{ $dataGempa['Wilayah'] }}</p>

                <div class="info-utama-wrapper">
                    <div class="gempa-magnitude-box">
                        <div class="magnitude-label-top">Magnitudo</div>
                        <div class="magnitude-value-main">{{ $dataGempa['Magnitude'] }}</div>
                    </div>
                    <div class="gempa-sub-details">
                        <div class="sub-detail-item">
                            <span class="material-symbols-outlined">vertical_align_bottom</span>
                            <div>
                                <span class="detail-label">Kedalaman:</span>
                                <span class="detail-value">{{ $dataGempa['Kedalaman'] }}</span>
                            </div>
                        </div>
                        <div class="sub-detail-item">
                            <span class="material-symbols-outlined">location_on</span>
                            <div>
                                <span class="detail-label">Lokasi:</span>
                                <span class="detail-value">{{ $dataGempa['Lintang'] }}, {{ $dataGempa['Bujur'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="info-saran">
                    <span class="material-symbols-outlined">info</span>
                    <strong>Saran BMKG:</strong> Hati-hati terhadap gempabumi susulan yang mungkin terjadi
                </p>

                <a href="https://inatews.bmkg.go.id" target="_blank" rel="noopener noreferrer" class="info-lihat-semua">
                    Lihat Semuanya <span class="material-symbols-outlined">arrow_forward</span>
                </a>
            </div>
        </div>
    </section>
@endif