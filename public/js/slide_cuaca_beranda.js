// File: /js/slide_cuaca_beranda.js
document.addEventListener('DOMContentLoaded', function () {
    

    // Pastikan variabel global dari PHP tersedia
    if (typeof dataPrakiraanTigaHariGlobal === 'undefined' || !Array.isArray(dataPrakiraanTigaHariGlobal)) {
        console.error("[Beranda JS] dataPrakiraanTigaHariGlobal tidak tersedia atau bukan array.");
        return; 
    }
    // console.log("[Beranda JS] dataPrakiraanTigaHariGlobal:", JSON.parse(JSON.stringify(dataPrakiraanTigaHariGlobal)));

    const allCitiesForecastsData = dataPrakiraanTigaHariGlobal;
    const cardsPerPage = 5; // Atau variabel cardsPerPageBerandaGlobal jika Anda definisikan dari PHP

    // Elemen untuk slider (sama seperti sebelumnya)
    const container = document.getElementById('cuacaCardsContainer');
    const prevButton = document.getElementById('cuacaPrevArrow');
    const nextButton = document.getElementById('cuacaNextArrow');
    const wrapper = document.querySelector('.cuaca-cards-wrapper');

    if (!container || !wrapper) { // Tombol panah bisa opsional jika kartu < cardsPerPage
        
        return;
    }
    if (!prevButton || !nextButton) {
        console.warn("[Beranda JS] Tombol navigasi slider (#cuacaPrevArrow atau #cuacaNextArrow) tidak ditemukan.");
        // Slider mungkin masih bisa berfungsi tanpa tombol manual jika hanya autoplay
    }

    const cards = container.querySelectorAll('.cuaca-card');
    if (cards.length === 0 && allCitiesForecastsData.length > 0) {
        console.warn("[Beranda JS] Ada data prakiraan, tetapi tidak ada elemen .cuaca-card yang dirender di HTML.");
        // Ini bisa terjadi jika loop PHP untuk render kartu awal gagal atau dikomentari
    }


    // === LOGIKA UNTUK TAB HARI INI, BESOK, LUSA ===
    const tabButtons = document.querySelectorAll('.tabs-cuaca .tab-button');
    

    // Fungsi untuk mengupdate konten satu kartu cuaca
    function updateCardContent(cardElement, forecastEntry) {
        if (!cardElement || !forecastEntry) {
            // console.warn("[Beranda JS] updateCardContent: elemen kartu atau data prakiraan tidak valid.");
            return;
        }

        const index = cardElement.dataset.kotaIndex; // Ambil index dari data-atribut

        const dateEl = cardElement.querySelector(`#card-date-${index}`); // Target elemen di dalam cardElement
        const iconEl = cardElement.querySelector(`#card-icon-${index}`);
        const tempEl = cardElement.querySelector(`#card-temp-${index}`);
        const descEl = cardElement.querySelector(`#card-desc-${index}`);
        // Nama lokasi tidak berubah, jadi tidak perlu diupdate di sini

        if (dateEl) dateEl.textContent = forecastEntry.tanggal || 'N/A';
        if (iconEl) iconEl.textContent = forecastEntry.icon || 'thermostat';
        if (tempEl) tempEl.textContent = (forecastEntry.suhu !== '--' && forecastEntry.suhu !== null ? forecastEntry.suhu : '--') + "°C";
        if (descEl) descEl.textContent = forecastEntry.deskripsi || 'N/A';
    }

    // Fungsi untuk mengupdate semua kartu yang ada berdasarkan hari yang dipilih
    function updateAllCardsForDay(dayKey) { // dayKey: 'hari_ini', 'besok', atau 'lusa'
        console.log(`[Beranda JS] Mengupdate semua kartu untuk: ${dayKey}`);
        if (!allCitiesForecastsData) return;

        allCitiesForecastsData.forEach((kotaData, index) => {
            // kotaData adalah item dari allCitiesForecastsData, contoh:
            // { nama_kota_asli: 'Pekanbaru', link_detail: '...', hari_ini: {...}, besok: {...}, lusa: {...} }
            const cardElement = container.querySelector(`.cuaca-card[data-kota-index="${index}"]`);
            
            if (cardElement && kotaData[dayKey]) {
                updateCardContent(cardElement, kotaData[dayKey]);
            } else {
                // console.warn(`[Beranda JS] Tidak ada data untuk ${kotaData.nama_kota_asli} pada ${dayKey} atau card tidak ditemukan.`);
                // Anda bisa set ke default jika mau
                if (cardElement) {
                    updateCardContent(cardElement, { tanggal: 'N/A', icon: 'thermostat', suhu: '--', deskripsi: 'Data tidak tersedia' });
                }
            }
        });
    }

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            tabButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            const selectedDayText = this.textContent.trim().toLowerCase();
            let dayKeyToUpdate = 'hari_ini';

            if (selectedDayText === 'besok') {
                dayKeyToUpdate = 'besok';
            } else if (selectedDayText === 'lusa') {
                dayKeyToUpdate = 'lusa';
            }
            
            updateAllCardsForDay(dayKeyToUpdate);
        });
    });
    // === AKHIR LOGIKA TAB ===


    // === LOGIKA SLIDER OTOMATIS (dari kode Anda sebelumnya, pastikan variabelnya sesuai) ===
    let cardWidthIncludingGap = 0;
    if (cards.length > 0) {
        const cardStyle = window.getComputedStyle(cards[0]);
        const containerStyle = window.getComputedStyle(container);
        const gap = parseFloat(containerStyle.gap) || 15;
        cardWidthIncludingGap = cards[0].offsetWidth + gap;
    }

    if (cardWidthIncludingGap <= 0 && cards.length > 0) { // Hanya error jika ada kartu tapi lebar salah
        console.error("[Beranda JS] Perhitungan cardWidthIncludingGap tidak valid untuk slider cuaca.");
        if(prevButton) prevButton.style.display = 'none';
        if(nextButton) nextButton.style.display = 'none';
        // Mungkin tidak perlu return, biarkan tab berfungsi
    }

    let currentIndex = 0;
    const totalCards = cards.length;
    const maxSlideIndex = totalCards > cardsPerPage ? totalCards - cardsPerPage : 0;

    let autoplayIntervalId = null;
    const autoplayDelay = 5000;
    let interactionTimeoutId = null;

    function updateSliderPosition() {
        if (!container) return;
        const newTransform = -currentIndex * cardWidthIncludingGap;
        container.style.transform = `translateX(${newTransform}px)`;
        updateArrowStates();
    }

    function updateArrowStates() {
        // ... (fungsi updateArrowStates Anda yang sudah ada dan bekerja) ...
        // Pastikan prevButton dan nextButton di-handle jika null (sudah ada di awal)
        if (!prevButton || !nextButton) return;
        if (totalCards <= cardsPerPage) {
            prevButton.style.display = 'none';
            nextButton.style.display = 'none';
            return;
        } else {
            prevButton.style.display = 'flex';
            nextButton.style.display = 'flex';
        }
        prevButton.disabled = currentIndex === 0;
        prevButton.classList.toggle('disabled', currentIndex === 0);
        nextButton.disabled = currentIndex >= maxSlideIndex;
        nextButton.classList.toggle('disabled', currentIndex >= maxSlideIndex);
    }

    function slideNextAutomatically() {
        if (totalCards <= cardsPerPage || cardWidthIncludingGap <= 0) return;
        currentIndex++;
        if (currentIndex > maxSlideIndex) {
            currentIndex = 0;
        }
        updateSliderPosition();
    }

    function startAutoplay() {
        if (totalCards <= cardsPerPage || cardWidthIncludingGap <= 0) return;
        stopAutoplay();
        autoplayIntervalId = setInterval(slideNextAutomatically, autoplayDelay);
    }

    function stopAutoplay() {
        clearInterval(autoplayIntervalId);
        autoplayIntervalId = null;
    }

    function handleManualNavigation() {
        stopAutoplay();
        clearTimeout(interactionTimeoutId);
        interactionTimeoutId = setTimeout(startAutoplay, autoplayDelay * 2);
    }

    if (nextButton) {
        nextButton.addEventListener('click', () => {
            if (totalCards <= cardsPerPage || currentIndex >= maxSlideIndex || cardWidthIncludingGap <= 0) return;
            currentIndex++;
            updateSliderPosition();
            handleManualNavigation();
        });
    }

    if (prevButton) {
        prevButton.addEventListener('click', () => {
            if (totalCards <= cardsPerPage || currentIndex === 0 || cardWidthIncludingGap <= 0) return;
            currentIndex--;
            updateSliderPosition();
            handleManualNavigation();
        });
    }
    
    if (wrapper) { // Pastikan wrapper ada
        wrapper.addEventListener('mouseenter', stopAutoplay);
        wrapper.addEventListener('mouseleave', () => {
            if (autoplayIntervalId === null) {
                 clearTimeout(interactionTimeoutId);
                 startAutoplay();
            }
        });
    }

    // Inisialisasi
    if (cards.length > 0 && cardWidthIncludingGap > 0) {
       updateSliderPosition(); 
       startAutoplay();        
    } else if (cards.length > 0) { // Jika ada card tapi lebar bermasalah
        // Tombol panah mungkin sudah disembunyikan oleh pengecekan cardWidthIncludingGap di atas
        console.warn("[Beranda JS] Slider tidak diinisialisasi karena cardWidthIncludingGap tidak valid, tapi tab mungkin masih berfungsi.");
    }
    // === AKHIR LOGIKA SLIDER ===

    const fdrsTabButtons = document.querySelectorAll('.fdrs-tabs .fdrs-tab-button');
    const fdrsImageElement = document.getElementById('fdrsImage');
    const fdrsImageInfoElement = document.getElementById('fdrsImageInfo');
    const fdrsDataContainer = document.getElementById('fdrsDataContainer'); // Untuk menampilkan pesan jika gambar gagal

    

    
    const fdrsBaseUrl = 'https://web-meteo.bmkg.go.id/media/data/bmkg/fdrs/'; // Pastikan satu slash di akhir
    const fdrsKodeProvinsi = '04';
    const fdrsNamaProvinsi = 'riau';
    const fdrsIndexType = 'ffmc'; // Fine Fuel Moisture Code

    function updateFDRSImageAndInfo(dayCode) { // dayCode: 'obs', '00', '01', '02', ...
        
        if (!fdrsImageElement) {
            console.error("Elemen gambar FDRS #fdrsImage tidak ditemukan.");
            return;
        }

        const imageName = `${fdrsKodeProvinsi}_${fdrsNamaProvinsi}_${fdrsIndexType}_${dayCode}.png`;
        let newImageUrl = fdrsBaseUrl + imageName;
        // Bersihkan double slash setelah protokol
        newImageUrl = newImageUrl.replace(/(?<!:)\/\//g, '/');
    
        let infoHariText = "Data FDRS"; // Default
        switch (dayCode) {
            case 'obs': infoHariText = 'Data Observasi FDRS Riau (FFMC)'; break;
            case '00':  infoHariText = 'Prakiraan FDRS Hari Ini (FFMC)'; break;
            case '01':  infoHariText = 'Prakiraan FDRS Besok (FFMC)'; break;
            case '02':  infoHariText = 'Prakiraan FDRS H+2 (FFMC)'; break;
            // Tambahkan case lain jika ada lebih banyak tombol tab
            default:
                if (dayCode.length === 2 && !isNaN(parseInt(dayCode))) {
                infoHariText = `Prakiraan FDRS H+${parseInt(dayCode)} (FFMC)`;
                }
        }

        
        fdrsImageElement.src = newImageUrl;
        fdrsImageElement.alt = `Peta FDRS ${infoHariText}`;
        if (fdrsImageInfoElement) {
            fdrsImageInfoElement.textContent = infoHariText;
        }

        // Tampilkan gambar dan sembunyikan pesan error/narrative jika ada
        fdrsImageElement.style.display = 'block';
        const pNarrative = fdrsDataContainer.querySelector('p:not(#fdrsImageInfo)');
        if (pNarrative) pNarrative.style.display = 'none';
        const existingErrorMsg = fdrsDataContainer.querySelector('.fdrs-error-message');
        if (existingErrorMsg) existingErrorMsg.style.display = 'none';

        // Handle jika gambar gagal dimuat (opsional tapi bagus)
        fdrsImageElement.onerror = function() {
            
            if (fdrsDataContainer) {
                // Hapus gambar yang error dan info harinya
                fdrsImageElement.style.display = 'none';
                if (fdrsImageInfoElement) fdrsImageInfoElement.textContent = '';
                
                // Cek apakah sudah ada pesan error, jika belum, tambahkan
                let errorMsgEl = fdrsDataContainer.querySelector('.fdrs-error-message');
                if (!errorMsgEl) {
                    errorMsgEl = document.createElement('p');
                    errorMsgEl.className = 'fdrs-error-message'; // Untuk styling jika perlu
                    errorMsgEl.style.fontStyle = 'italic';
                    errorMsgEl.style.color = '#c0392b'; // Warna error
                    fdrsDataContainer.insertBefore(errorMsgEl, fdrsImageInfoElement); // Sisipkan sebelum info (atau di awal)
                }
                errorMsgEl.innerHTML = `Gambar FDRS untuk ${infoHariText} tidak dapat dimuat. <a href="https://web-meteo.bmkg.go.id/id/peringatan/kebakaran-hutan" target="_blank" rel="noopener noreferrer">Lihat sumber BMKG</a>.`;
                errorMsgEl.style.display = 'block';
            }
            this.onerror = null; 
        };
        // Hapus pesan error jika ada saat gambar berhasil dimuat (atau akan dimuat)
         //const existingErrorMsg = fdrsDataContainer.querySelector('.fdrs-error-message');
         //if (existingErrorMsg) existingErrorMsg.style.display = 'none';
    }

    if (fdrsTabButtons.length > 0 && fdrsImageElement && fdrsDataContainer) {
        
        fdrsTabButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Hapus kelas active dari semua tombol tab FDRS
                console.log("[FDRS Tabs] Tombol tab FDRS diklik:", this.textContent.trim());
                fdrsTabButtons.forEach(btn => btn.classList.remove('active'));
                // Tambahkan kelas active ke tombol yang diklik
                this.classList.add('active');
                
                const dayCode = this.dataset.fdrsDay;
                console.log("[FDRS Tabs] dayCode FDRS yang dipilih:", dayCode);
                updateFDRSImageAndInfo(dayCode);
            });
        });

        // Set gambar awal berdasarkan tab yang aktif saat load (jika ada)
        // atau berdasarkan default yang di-render PHP.
        // Jika PHP sudah merender gambar 'obs' atau '00' dengan benar, baris ini mungkin tidak perlu,
        // tapi ini memastikan konsistensi jika ada perubahan di masa depan.
        const activeTab = document.querySelector('.fdrs-tabs .fdrs-tab-button.active');
        if (activeTab) {
        // updateFDRSImage(activeTab.dataset.fdrsDay); // Panggil ini jika PHP tidak merender gambar awal
                                                    // atau jika ingin JS yang mengontrol penuh
        } else if (fdrsTabButtons.length > 0) {
        // Jika tidak ada yg aktif, aktifkan yg pertama dan muat gambarnya
        // fdrsTabButtons[0].classList.add('active');
        // updateFDRSImage(fdrsTabButtons[0].dataset.fdrsDay);
        }
    } else {
        console.warn("[Beranda JS] Tidak ada tombol tab FDRS (.fdrs-tab-button) yang ditemukan.");
    }

     // === LOGIKA UNTUK Expand/Collapse Deskripsi Citra Satelit ===
    const toggleDescriptionLinks = document.querySelectorAll('.satellite-toggle-description');
    
    
    toggleDescriptionLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            console.log("[Satelit] Link 'Lihat Selengkapnya' diklik. Elemen yang diklik:", this);

            // DEBUG: Cari parent .satellite-card-content
            const contentCard = this.closest('.satellite-card-content');
            console.log("[Satelit] Mencari parent .satellite-card-content:", contentCard);

            if (!contentCard) {
                console.error("[Satelit] Tidak bisa menemukan elemen parent '.satellite-card-content'. Pastikan link berada di dalamnya.");
                return;
            }

            // DEBUG: Cari deskripsi di dalam parent tersebut
            const description = contentCard.querySelector('.satellite-description-full');
            console.log("[Satelit] Mencari anak .satellite-description-full:", description);

            if (description) {
                this.classList.toggle('expanded');
                description.classList.toggle('expanded'); // Toggle kelas 'show' pada deskripsi
                console.log("[Satelit] Kelas 'show' dan 'expanded' di-toggle.");
                
                const linkTextSpan = this.querySelector('.toggle-text'); // Cari span dengan kelas .toggle-text
                const linkIconSpan = this.querySelector('.material-symbols-outlined');

                if (this.classList.contains('expanded')) {
                    if (linkTextSpan) linkTextSpan.textContent = 'Sembunyikan';
                    if (linkIconSpan) linkIconSpan.textContent = 'expand_less'; // Ikon panah ke atas
                } else {
                    if (linkTextSpan) linkTextSpan.textContent = 'Lihat Selengkapnya';
                    if (linkIconSpan) linkIconSpan.textContent = 'expand_more'; // Ikon panah ke bawah
                }
            } else {
                console.error("[Satelit] Tidak bisa menemukan elemen .satellite-description-full di dalam card.");
            }
        });
    });

    // === LOGIKA BARU: Lightbox/Zoom Gambar Citra Satelit ===
    const zoomableImages = document.querySelectorAll('.satellite-image-zoomable');
    
    // Buat elemen modal sekali saja
    const modalOverlay = document.createElement('div');
    modalOverlay.className = 'image-modal-overlay';
    document.body.appendChild(modalOverlay);

    zoomableImages.forEach(image => {
        image.style.cursor = 'zoom-in'; // Ubah kursor menjadi 'perbesar'
        image.addEventListener('click', function() {
            // Isi modal dengan gambar yang diklik
            modalOverlay.innerHTML = `<img src="${this.src}" class="image-modal-content" alt="${this.alt}">`;
            modalOverlay.classList.add('show');
        });
    });

    // Klik pada overlay akan menutup modal
    modalOverlay.addEventListener('click', function() {
        this.classList.remove('show');
        // Kosongkan konten untuk membebaskan memori
        setTimeout(() => {
            this.innerHTML = '';
        }, 300); // Sesuaikan dengan durasi transisi
    });
    
});