// File: js/peta_wisata.js (Versi Final dengan Daftar Interaktif)

// Tambahkan ini di bagian atas file js/peta_wisata.js

document.addEventListener('DOMContentLoaded', function () {
    const mapContainer = document.getElementById('petaWisataContainer');
    // UBAH baris di bawah ini untuk menggunakan ID baru
    const listContentContainer = document.getElementById('listPanelContent'); 

    // --- KODE BARU UNTUK KONTROL SCROLL ---
    const scrollUpBtn = document.getElementById('scrollUpBtn');
    const scrollDownBtn = document.getElementById('scrollDownBtn');
    
    if (scrollUpBtn && scrollDownBtn && listContentContainer) {
        scrollUpBtn.addEventListener('click', () => {
            listContentContainer.scrollBy({ top: -150, behavior: 'smooth' });
        });

        scrollDownBtn.addEventListener('click', () => {
            listContentContainer.scrollBy({ top: 150, behavior: 'smooth' });
        });
    }
    // --- AKHIR KODE BARU ---

    // ... sisa kode JavaScript Anda (if !mapContainer, if dataWisataUntukPeta, try...catch, dll) ...
});

document.addEventListener('DOMContentLoaded', function () {
    const mapContainer = document.getElementById('petaWisataContainer');
    const listContentContainer = document.querySelector('#wisataListContainer .list-panel-content');

         
    if (!mapContainer || !listContentContainer) {
        console.error("Elemen #petaWisataContainer atau #wisataListContainer tidak ditemukan.");
        return;
    }

    if (typeof dataWisataUntukPeta === 'undefined' || dataWisataUntukPeta.length === 0) {
        mapContainer.innerHTML = '<p style="text-align:center; padding: 20px;">Data wisata tidak tersedia untuk ditampilkan di peta.</p>';
        listContentContainer.innerHTML = '<p style="text-align:center; padding: 20px;">Tidak ada data wisata.</p>';
        return;
    }

    try {
        var map = L.map(mapContainer).setView([0.5071, 101.4478], 8);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        let markerRefs = {}; // Objek untuk menyimpan referensi ke setiap marker berdasarkan ID lokasi

        // --- FUNGSI HELPER ---

        // Fungsi untuk mendapatkan ikon berdasarkan kategori dari CSV
        function getIconForCategory(category) {
            const catLower = category.toLowerCase();
            if (catLower.includes('air terjun')) return 'waves';
            if (catLower.includes('danau')) return 'water';
            if (catLower.includes('pantai')) return 'kitesurfing';
            if (catLower.includes('sejarah') || catLower.includes('istana')) return 'fort';
            if (catLower.includes('cagar alam') || catLower.includes('suaka')) return 'forest';
            if (catLower.includes('taman')) return 'park';
            return 'attractions'; // Ikon default
        }

        // Fungsi untuk membuat popup (sudah ada, tidak berubah)
        function createPopupContent(lokasi, day = 'hari_ini') {
            // ... (kode fungsi createPopupContent dari langkah sebelumnya, TIDAK PERLU DIUBAH) ...
            const prakiraan = lokasi.prakiraan_cuaca[day];
            if (!prakiraan) { return `<div class="wisata-popup"><strong>${lokasi.nama_wisata}</strong><br>Data cuaca tidak tersedia.</div>`; }
            const temp = prakiraan.t, weatherDesc = prakiraan.weather_desc, iconName = prakiraan.icon_name, kelembapan = prakiraan.hu, kecAngin = prakiraan.ws;
            let tanggalFormatted = '';
            if (prakiraan.local_datetime) {
                const tanggalObj = new Date(prakiraan.local_datetime);
                tanggalFormatted = tanggalObj.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            }
            return `<div class="wisata-popup"><div class="popup-header"><strong class="popup-title">${lokasi.nama_wisata}</strong><div class="popup-date">${tanggalFormatted}</div></div><div class="popup-body"><div class="weather-main-info"><span class="material-symbols-outlined weather-icon-body">${iconName}</span><div class="weather-description">${weatherDesc}</div></div><div class="temperature">${temp}°C</div></div><div class="popup-details"><div class="detail-item"><span class="material-symbols-outlined small-icon">humidity_percentage</span><span>${kelembapan}%</span></div><div class="detail-item"><span class="material-symbols-outlined small-icon">air</span><span>${kecAngin} km/jam</span></div></div></div>`;
        }


        // --- LOGIKA UTAMA ---

        // 1. Loop data untuk membuat marker di peta DAN daftar card di samping
        dataWisataUntukPeta.forEach(lokasi => {
            // Buat Marker dan simpan referensinya
            if (lokasi.lat && lokasi.lon) {
                const marker = L.marker([lokasi.lat, lokasi.lon]);
                marker.bindPopup(createPopupContent(lokasi, 'hari_ini'));
                marker.addTo(map);
                markerRefs[lokasi.id] = marker; // Simpan marker dengan key ID lokasi
            }

            // Buat Card untuk daftar di samping
            const cardIcon = getIconForCategory(lokasi.kategori || '');
            const cardElement = document.createElement('div');
            cardElement.className = 'wisata-card-item';
            cardElement.setAttribute('data-id', lokasi.id); // Set data-id untuk link ke marker
            cardElement.innerHTML = `
                <span class="material-symbols-outlined card-icon">${cardIcon}</span>
                <span class="card-title">${lokasi.nama_wisata}</span>
            `;
            listContentContainer.appendChild(cardElement);
        });

        // 2. Tambahkan event listener untuk interaksi Daftar -> Peta
        listContentContainer.addEventListener('click', function(e) {
            const card = e.target.closest('.wisata-card-item');
            if (card) {
                const lokasiId = card.getAttribute('data-id');
                const targetMarker = markerRefs[lokasiId];

                if (targetMarker) {
                    map.flyTo(targetMarker.getLatLng(), 14); // Zoom ke marker
                    targetMarker.openPopup(); // Buka popup-nya
                }
            }
        });

        // 3. Tambahkan event listener untuk tombol filter hari (mengupdate semua popup)
        const filterButtons = document.querySelectorAll('.day-filter-button');
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                const selectedDay = this.getAttribute('data-day');
                
                // Update semua popup
                dataWisataUntukPeta.forEach(lokasi => {
                    const marker = markerRefs[lokasi.id];
                    if (marker) {
                        const newContent = createPopupContent(lokasi, selectedDay);
                        marker.setPopupContent(newContent);
                    }
                });
            });
        });

    } catch (e) {
        console.error("Terjadi error saat inisialisasi peta atau daftar wisata: ", e);
        mapContainer.innerHTML = '<p style="text-align:center; padding: 20px; color: red;">Gagal memuat peta.</p>';
    }
});