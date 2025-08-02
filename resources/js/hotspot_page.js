document.addEventListener('DOMContentLoaded', function () {
    // Jalankan setiap fungsi setelah halaman dimuat
    initRiauMap();
    initKabupatenChart();
    initProvinsiChart();
});

function initRiauMap() {
    try {
        const mapContainer = document.getElementById('hotspotMapRiau_Konten');
        if (!mapContainer) return; // Keluar jika elemen tidak ada

        const petaHotspot = L.map(mapContainer).setView([0.5071, 101.4478], 8);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(petaHotspot);

        if (typeof hotspotMapData !== 'undefined' && hotspotMapData.length > 0) {
            const iconTinggi = L.divIcon({ className: 'hotspot-icon-wrapper', html: `<span class="hotspot-icon tinggi"></span>` });
            const iconSedang = L.divIcon({ className: 'hotspot-icon-wrapper', html: `<span class="hotspot-icon sedang"></span>` });
            const iconRendah = L.divIcon({ className: 'hotspot-icon-wrapper', html: `<span class="hotspot-icon rendah"></span>` });
            
            let jumlahTinggi = 0, jumlahSedang = 0, jumlahRendah = 0;

            hotspotMapData.forEach(function(spot) {
                if (spot.lat != null && spot.lon != null) {
                    let currentIcon;
                    if (spot.confidence >= 9) { currentIcon = iconTinggi; jumlahTinggi++; } 
                    else if (spot.confidence == 8) { currentIcon = iconSedang; jumlahSedang++; }
                    else { currentIcon = iconRendah; jumlahRendah++; }
                    L.marker([spot.lat, spot.lon], { icon: currentIcon }).addTo(petaHotspot);
                }
            });
            
            const legend = L.control({position: 'bottomright'});
            legend.onAdd = function (map) {
                const div = L.DomUtil.create('div', 'info legend');
                div.innerHTML = '<h4>Kepercayaan</h4>' +
                    '<i style="background: red"></i> Tinggi (' + jumlahTinggi + ')<br>' +
                    '<i style="background: yellow"></i> Sedang (' + jumlahSedang + ')<br>' +
                    '<i style="background: limegreen"></i> Rendah (' + jumlahRendah + ')';
                return div;
            };
            legend.addTo(petaHotspot);
        }
    } catch (e) { console.error("Error Peta Leaflet:", e); }
}


/**
 * Fungsi untuk menggambar Grafik Hotspot per Kabupaten.
 */
function initKabupatenChart() {
    try {
        const canvasElement = document.getElementById('hotspotKabupatenChart');
        if (!canvasElement) return;

        if (typeof hotspotChartData !== 'undefined' && Object.keys(hotspotChartData).length > 0) {
            const dataGrafik = hotspotChartData;
            const labels = Object.keys(dataGrafik);
            const dataTinggi = labels.map(kab => dataGrafik[kab].tinggi);
            const dataSedang = labels.map(kab => dataGrafik[kab].sedang);
            const dataRendah = labels.map(kab => dataGrafik[kab].rendah);

            const ctx = canvasElement.getContext('2d');
            if (window.myKabupatenChart) window.myKabupatenChart.destroy();

            window.myKabupatenChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Tinggi', data: dataTinggi, backgroundColor: 'rgba(217, 30, 24, 0.8)' },
                        { label: 'Sedang', data: dataSedang, backgroundColor: 'rgba(241, 196, 15, 0.8)' },
                        { label: 'Rendah', data: dataRendah, backgroundColor: 'rgba(39, 174, 96, 0.8)' }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                    // PERUBAHAN DI SINI
                        tooltip: {
                            mode: 'index', // Tampilkan semua data pada index yang sama
                            intersect: false, // Tooltip akan muncul meski tidak pas di atas bar
                        },
                    },
                    scales: {
                        x: { stacked: true },
                        y: { stacked: true, beginAtZero: true, title: { display: true, text: 'Jumlah Hotspot' } }
                    }
                }
            });
        }
    } catch (e) {
        console.error("Error saat inisialisasi Grafik Kabupaten:", e);
    }
}

/**
 * Fungsi untuk menggambar Grafik Hotspot per Provinsi.
 */
function initProvinsiChart() {
    try {
        const canvasElement = document.getElementById('hotspotProvinsiChart');
        if (!canvasElement) return;

        if (typeof hotspotProvinsiData !== 'undefined' && Object.keys(hotspotProvinsiData).length > 0) {
            const labels = Object.keys(hotspotProvinsiData);
            const data = Object.values(hotspotProvinsiData);

            // Membuat warna dinamis untuk setiap batang
            const backgroundColors = labels.map((_, index) => {
                const colors = ['#d94e4e', '#3498db', '#f1c40f', '#2ecc71', '#9b59b6', '#e67e22', '#1abc9c', '#34495e'];
                return colors[index % colors.length];
            });

            const ctx = canvasElement.getContext('2d');
            if (window.myProvinsiChart) window.myProvinsiChart.destroy();

            window.myProvinsiChart = new Chart(ctx, {
                // DIUBAH: Tipe grafik menjadi 'bar'
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Hotspot',
                        data: data,
                        backgroundColor: backgroundColors,
                    }]
                },
                options: {
                    indexAxis: 'y', // Membuat bar menjadi horizontal agar nama provinsi muat
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // Sembunyikan legenda karena sudah jelas dari label
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            title: { display: true, text: 'Jumlah Hotspot' }
                        }
                    }
                }
            });
        } else {
             const context = canvasElement.getContext('2d');
             context.clearRect(0, 0, canvasElement.width, canvasElement.height);
             context.textAlign = 'center';
             context.font = '16px Arial';
             context.fillStyle = '#666';
             context.fillText('Tidak ada data hotspot di Sumatera untuk periode ini.', canvasElement.width / 2, 50);
        }
    } catch (e) { console.error("Error saat inisialisasi Grafik Provinsi:", e); }
}
