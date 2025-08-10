// File: /js/peta_hotspot_beranda.js

document.addEventListener('DOMContentLoaded', function () {
    // Cek apakah kita di halaman home dan data tersedia
    if (typeof hotspotDataBeranda === 'undefined' || typeof hotspotDataTanggalInfo === 'undefined') {
        console.warn("Data hotspot atau info tanggal untuk peta beranda tidak tersedia dari PHP.");
        const tsEl = document.getElementById('hotspotMapTimestampInfo');
        if (tsEl) tsEl.textContent = "Informasi tanggal data tidak termuat.";
        return;
    }

    const mapContainer = document.getElementById('hotspotMapContainer');
    const timestampInfoEl = document.getElementById('hotspotMapTimestampInfo');

    // === ISI TIMESTAMP DI SINI ===
    if (timestampInfoEl) {
        timestampInfoEl.textContent = hotspotDataTanggalInfo; // Langsung gunakan string dari PHP
    } else {
        
    }
    // === AKHIR ISI TIMESTAMP ===

    if (!mapContainer) {
       
        return;
    }

    if (timestampInfoEl) {
         // Perbarui pesan jika tidak ada hotspot tapi tanggalnya ada
            if (hotspotDataTanggalInfo !== "Data hotspot terbaru tidak tersedia" && hotspotDataTanggalInfo !== "Informasi tanggal tidak tersedia") {
                 timestampInfoEl.textContent = hotspotDataTanggalInfo + "";
            } else {
                 timestampInfoEl.textContent = "Data hotspot tidak tersedia.";
            }
    }

    // Hapus placeholder teks "Peta hotspot akan ditampilkan di sini."
    mapContainer.innerHTML = ''; 
    mapContainer.style.backgroundColor = '#fff'; // Hapus background abu-abu

    try {
        // Inisialisasi Peta Leaflet
        const petaHotspotBeranda = L.map(mapContainer).setView([0.5071, 101.4478], 7); // Center di Riau, zoom 7

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(petaHotspotBeranda);

        // Fungsi untuk membuat ikon custom (sama seperti sebelumnya)
        function createHotspotDivIconBeranda(backgroundColor) {
            return L.divIcon({
                className: 'custom-div-icon-beranda', // Beri kelas berbeda jika perlu styling khusus
                html: `<span style="background-color:${backgroundColor}; width: 10px; height: 10px; border-radius: 50%; display: inline-block; border: 1px solid #333; box-shadow: 0 0 2px rgba(0,0,0,0.5);"></span>`,
                iconSize: [10, 10],
                iconAnchor: [5, 5]
            });
        }

        const iconRendahBeranda = createHotspotDivIconBeranda('green');
        const iconSedangBeranda = createHotspotDivIconBeranda('yellow');
        const iconTinggiBeranda = createHotspotDivIconBeranda('red');
        const iconLainnyaBeranda = createHotspotDivIconBeranda('grey');

        let jumlahRendah = 0, jumlahSedang = 0, jumlahTinggi = 0, jumlahLainnya = 0;

        if (hotspotDataBeranda && hotspotDataBeranda.length > 0) {
            hotspotDataBeranda.forEach(function(spot) {
                if (spot.lat != null && spot.lon != null) {
                    let currentIcon;
                    let confidence = parseInt(spot.confidence); // Pastikan integer

                    if (confidence >= 9 || (typeof spot.confidence === 'string' && spot.confidence.toLowerCase() === 'tinggi')) { // Tinggi >= 9 atau teks "tinggi"
                        currentIcon = iconTinggiBeranda;
                        jumlahTinggi++;
                    } else if (confidence === 8 || (typeof spot.confidence === 'string' && spot.confidence.toLowerCase() === 'sedang')) { // Sedang = 8 atau teks "sedang"
                        currentIcon = iconSedangBeranda;
                        jumlahSedang++;
                    } else if (confidence === 7 || (typeof spot.confidence === 'string' && spot.confidence.toLowerCase() === 'rendah')) { // Rendah = 7 atau teks "rendah"
                        currentIcon = iconRendahBeranda;
                        jumlahRendah++;
                    } else {
                        currentIcon = iconLainnyaBeranda;
                        jumlahLainnya++;
                    }
                    
                    let popupContent = `<b>Kab/Kec:</b> ${spot.kabupaten || 'N/A'} / ${spot.kecamatan || 'N/A'}<br>`;
                    popupContent += `<b>Koordinat:</b> ${parseFloat(spot.lat).toFixed(4)}, ${parseFloat(spot.lon).toFixed(4)}<br>`;
                    popupContent += `<b>Kepercayaan:</b> ${spot.confidence || 'N/A'}<br>`;
                    popupContent += `<b>Satelit:</b> ${spot.satelit || 'N/A'}<br>`;
                    popupContent += `<b>Waktu:</b> ${spot.tanggal || 'N/A'} ${spot.waktu || ''} WIB`;

                    L.marker([spot.lat, spot.lon], { icon: currentIcon })
                        .addTo(petaHotspotBeranda)
                        .bindPopup(popupContent);
                }
            });

            // Tambahkan Legenda (opsional, jika dirasa perlu di beranda)
            const legend = L.control({ position: 'bottomright' });
            legend.onAdd = function (map) {
                const div = L.DomUtil.create('div', 'info legend legend-beranda'); // Beri kelas berbeda
                div.innerHTML = '<h4>Kepercayaan</h4>' +
                    '<i style="background:red"></i> Tinggi ('+jumlahTinggi+')<br>' +
                    '<i style="background:yellow"></i> Sedang ('+jumlahSedang+')<br>' +
                    '<i style="background:green"></i> Rendah ('+jumlahRendah+')<br>' +
                    (jumlahLainnya > 0 ? '<i style="background:grey"></i> Lainnya ('+jumlahLainnya+')' : '');
                return div;
            };
            legend.addTo(petaHotspotBeranda);

        } else {
            // Jika tidak ada hotspot, tampilkan pesan di peta
             L.popup()
                .setLatLng(petaHotspotBeranda.getCenter())
                .setContent('Tidak ada hotspot terdeteksi untuk tanggal terbaru.')
                .openOn(petaHotspotBeranda);
            if (timestampInfoEl) { // Update juga timestamp info
                timestampInfoEl.textContent = hotspotDataTanggalInfo + " (Tidak ada hotspot)";
            }
        }

    } catch (e) {
        console.error("Error saat membuat peta hotspot beranda:", e);
        mapContainer.innerHTML = '<p style="color:red; text-align:center;">Gagal memuat peta hotspot.</p>';
    }
});