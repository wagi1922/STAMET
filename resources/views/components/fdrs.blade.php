{{-- resources/views/components/fdrs.blade.php --}}



    <section class="fdrs-content-section" style="flex: 1; min-width: 300px; display: flex;">
        <div class="card fdrs-card" style="width: 100%; display: flex; flex-direction: column;"> 
            <div class="card-header fdrs-header">
                <span class="material-symbols-outlined card-icon">forest</span>
                <h3>Informasi FDRS (Tingkat Bahaya Kebakaran)</h3>
            </div>
            <div class="fdrs-tabs" style="margin-bottom: 15px; text-align: center;">
                {{-- Tombol tab sekarang tidak perlu logika PHP untuk class 'active' --}}
                {{-- JavaScript yang akan menanganinya --}}
                <button class="fdrs-tab-button active" data-fdrs-day="obs">Observasi</button>
                <button class="fdrs-tab-button" data-fdrs-day="00">Hari Ini</button>
                <button class="fdrs-tab-button" data-fdrs-day="01">Besok</button>
            </div>
            <div class="card-content fdrs-content" style="flex-grow: 1;">
                <div id="fdrsDataContainer" style="text-align: center;">
                    {{-- Tampilkan gambar dan info default --}}
                    <img id="fdrsImage" 
                         src="{{ $dataFdrs['default']['map_image_url'] }}" 
                         alt="Peta FDRS {{ $dataFdrs['default']['info_hari'] }}" 
                         style="max-width: 100%; height: auto; border-radius: 6px; border: 1px solid #ddd;">
    
                    <p id="fdrsImageInfo" style="font-size: 0.8em; color: #666; margin-top: 5px;">
                        {{ $dataFdrs['default']['info_hari'] }}
                    </p>
                </div>
            </div>
            <div class="card-footer fdrs-footer" style="text-align: right; margin-top:15px; flex-shrink: 0;">
                <a href="https://web-meteo.bmkg.go.id/id/peringatan/kebakaran-hutan" target="_blank" rel="noopener noreferrer" class="selengkapnya-link">
                    Selengkapnya <span class="material-symbols-outlined">arrow_forward</span>
                </a>
            </div>
        </div>
    </section>


{{-- Kirim semua data tab ke JavaScript agar bisa diakses oleh fdrs.js --}}
@push('scripts')
<script>
    var dataFdrsLengkap = @json($dataFdrs['tabs'] ?? []);
</script>
@endpush