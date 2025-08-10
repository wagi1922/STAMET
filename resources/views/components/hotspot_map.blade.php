{{-- resources/views/components/hotspot_map.blade.php --}}
{{-- Link ke Font dan Ikon --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Gruppo&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    {{-- Link ke file CSS menggunakan helper asset() Laravel --}}
    <link rel="stylesheet" href="{{ asset('css/style_beranda.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<section class="hotspot-map-section" style="flex: 1; min-width: 300px; display: flex;">
    <div class="card hotspot-map-card" style="width: 100%; display: flex; flex-direction: column;">
        <div class="card-header hotspot-map-header">
            <span class="material-symbols-outlined card-icon">local_fire_department</span>
            <h3>Peta Sebaran Hotspot Terkini</h3>
        </div>
        <div class="card-content hotspot-map-content" style="flex-grow: 1; display: flex; flex-direction: column;">
            
            <div id="hotspotMapContainer" style="width: 100%; height: 400px; background-color: #e9ecef; border-radius: 6px; flex-grow: 1;">
                {{-- Peta akan dirender di sini oleh Leaflet.js --}}
            </div>

            {{-- Info tanggal akan diisi oleh JavaScript dari data yang kita siapkan --}}
            <p class="map-timestamp-info" id="hotspotMapTimestampInfo" style="text-align: right; font-size: 0.85em; color: #6c757d; margin-top: 10px; flex-shrink: 0;">
                
            </p>   
        </div>
        <div class="card-footer hotspot-map-footer" style="text-align: right; margin-top:15px; flex-shrink: 0;">
            <a href="#" class="selengkapnya-link"> {{-- Ganti href jika ada halaman detail --}}
                Lihat Detail Hotspot <span class="material-symbols-outlined">arrow_forward</span>
            </a>
        </div>
    </div>
</section>

{{-- Menyisipkan data dari PHP ke JavaScript menggunakan @push agar rapi --}}
@push('scripts')
<script>
    // Variabel global ini akan dibaca oleh peta_hotspot_beranda.js
    var hotspotDataBeranda = @json($dataHotspot['hotspots'] ?? []);
    var hotspotDataTanggalInfo = @json($dataHotspot['info_tanggal'] ?? 'Data tidak tersedia');

</script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/peta_hotspot_beranda.js') }}" defer></script>
@endpush