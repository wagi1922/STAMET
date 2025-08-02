// File: js/maritim_pelabuhan_detail.js

document.addEventListener('DOMContentLoaded', function() {
    console.log("[JS Init] DOMContentLoaded event fired."); // LOG A: Pastikan script ini berjalan
    // Cek apakah variabel global dari PHP sudah ada dan valid
    if (typeof phpSelectedPortFile === 'undefined' || !phpSelectedPortFile || 
        typeof phpForecastDataDay1 === 'undefined') {
        console.warn("[JS Init] Data port atau prakiraan (phpSelectedPortFile, phpForecastDataDay1) tidak tersedia. Script detail dihentikan.");
        return; 
    }
    console.log("[JS Init] phpSelectedPortFile:", phpSelectedPortFile); // LOG B
    console.log("[JS Init] phpForecastDataDay1 (jumlah entri):", phpForecastDataDay1.length); // LOG C

    // Variabel global dari PHP
    const forecastDataDay1 = phpForecastDataDay1; 

    // Ambil elemen DOM (pastikan semua ID ini ada di HTML Anda)
    const timeSlider = document.getElementById('timeSlider');
    console.log("[JS Init] Element #timeSlider:", timeSlider); // LOG D: Cek apakah slider ditemukan
    const displayedDateEl = document.getElementById('displayed-date');
    const displayedTimeEl = document.getElementById('displayed-time');
    const weatherIconEl = document.getElementById('weather-icon-display'); // Ini adalah <span>
    const weatherConditionEl = document.querySelector('#weather-text-display .condition');
    const temperatureEl = document.querySelector('#weather-text-display .temperature');
    const humidityEl = document.querySelector('#weather-text-display .humidity');
    const windValueEl = document.querySelector('#wind-text-display .value');
    const windGustEl = document.querySelector('#wind-text-display .gust');
    const waveValueEl = document.querySelector('#wave-text-display .value');
    const waveCategoryEl = document.querySelector('#wave-text-display .category');
    const sliderTimeLabelStart = document.getElementById('slider-time-label-start');
    const sliderTimeLabelEnd = document.getElementById('slider-time-label-end');
    const tidesCanvas = document.getElementById('tidesChart');
    const tidesPlaceholder = document.getElementById('tides-placeholder');
    let tidesChartInstance = null;

    // ==================================================================
    // === DEFINISIKAN SEMUA FUNGSI HELPER DI SINI, SEBELUM DIGUNAKAN ===
    // ==================================================================

    function formatIndonesianDate(dateStringUTC, type = 'full') {
        // ... (isi fungsi formatIndonesianDate seperti yang sudah Anda miliki) ...
        // Pastikan fungsi ini lengkap dan benar
        if (!dateStringUTC) return "N/A";
        let parsableDateStringUTC = dateStringUTC;
        if (dateStringUTC.includes(" ") && !dateStringUTC.includes("T")) {
            parsableDateStringUTC = dateStringUTC.replace(" ", "T");
        }
        if (!parsableDateStringUTC.endsWith("Z")) {
            parsableDateStringUTC += "Z";
        }

        const date = new Date(parsableDateStringUTC);
        if (isNaN(date.getTime())) {
            console.error("Invalid date string for parsing in formatIndonesianDate:", dateStringUTC);
            return "Invalid Date";
        }
        
        const wibOffset = 7 * 60 * 60 * 1000;
        const dateWIB = new Date(date.getTime() + wibOffset);

        const dayNames = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
        const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        if (type === 'dateonly') {
            return `${dayNames[dateWIB.getUTCDay()]}, ${dateWIB.getUTCDate()} ${monthNames[dateWIB.getUTCMonth()]} ${dateWIB.getUTCFullYear()}`;
        } else if (type === 'timeonly') {
            return `${String(dateWIB.getUTCHours()).padStart(2, '0')}.${String(dateWIB.getUTCMinutes()).padStart(2, '0')} WIB`;
        }
        return `${dayNames[dateWIB.getUTCDay()]}, ${dateWIB.getUTCDate()} ${monthNames[dateWIB.getUTCMonth()]} ${dateWIB.getUTCFullYear()} ${String(dateWIB.getUTCHours()).padStart(2, '0')}.${String(dateWIB.getUTCMinutes()).padStart(2, '0')} WIB`;
    }

    function getWeatherSymbolName(weatherDesc) {
        // console.log("Deskripsi cuaca diterima oleh getWeatherSymbolName:", weatherDesc); 
        if (!weatherDesc) {
            // console.log("Deskripsi cuaca kosong, mengembalikan default 'thermostat'.");
            return 'thermostat';
        }
        const descLower = weatherDesc.toLowerCase();
        let symbolToReturn = 'thermostat';

        if (descLower.includes('hujan petir') || descLower.includes('badai')) symbolToReturn = 'thunderstorm';
        else if (descLower.includes('hujan lebat')) symbolToReturn = 'rainy_heavy';
        else if (descLower.includes('hujan sedang')) symbolToReturn = 'rainy';
        else if (descLower.includes('hujan ringan')) symbolToReturn = 'rainy_light';
        else if (descLower.includes('hujan lokal')) symbolToReturn = 'rainy_light';
        else if (descLower.includes('hujan')) symbolToReturn = 'rainy';
        else if (descLower.includes('berawan tebal') || descLower.includes('awan tebal')) symbolToReturn = 'cloudy';
        else if (descLower.includes('berawan') && descLower.includes('cerah')) symbolToReturn = 'partly_cloudy_day';
        else if (descLower.includes('berawan')) symbolToReturn = 'cloud';
        else if (descLower.includes('cerah')) symbolToReturn = 'sunny';
        else if (descLower.includes('kabut')) symbolToReturn = 'foggy';
        else if (descLower.includes('asap')) symbolToReturn = 'dehaze';
        else if (descLower.includes('udara kabur')) symbolToReturn = 'mist';
        else if (descLower.includes('petir') && !descLower.includes('hujan')) symbolToReturn = 'bolt';
        
        // console.log("Simbol ikon yang akan dikembalikan:", symbolToReturn);
        return symbolToReturn;
    }

    function createOrUpdateTidesChart(activeIndex) {
        console.log("------------------------------------------------------"); // Pemisah log
    console.log("[TidesChart] Fungsi createOrUpdateTidesChart dipanggil dengan activeIndex:", activeIndex); // LOG 1

    // (LOG 2 bisa dilihat dengan expand forecastDataDay1 jika perlu, untuk mengurangi clutter di console)
    // console.log("[TidesChart] Data mentah forecastDataDay1:", JSON.parse(JSON.stringify(forecastDataDay1)));

    if (!forecastDataDay1 || !Array.isArray(forecastDataDay1) || forecastDataDay1.length === 0) {
        if (tidesPlaceholder) {
            tidesPlaceholder.textContent = 'Data prakiraan untuk grafik pasang surut tidak tersedia.';
            tidesPlaceholder.style.display = 'flex';
        }
        if (tidesCanvas) tidesCanvas.style.display = 'none';
        if (tidesChartInstance) {
            tidesChartInstance.destroy();
            tidesChartInstance = null;
        }
        console.warn("[TidesChart] forecastDataDay1 kosong atau tidak valid.");
        return;
    }

    const labels = forecastDataDay1.map(item => {
        const timeWIB = formatIndonesianDate(item.forecast_time_utc, 'timeonly');
        return timeWIB.substring(0, 2); // Ambil "HH" dari "HH.MM WIB"
    });

    const tidesValues = forecastDataDay1.map(item => {
        const tideVal = item.tides_m;
        if (tideVal !== null && tideVal !== "" && !isNaN(parseFloat(tideVal))) {
            return parseFloat(tideVal);
        }
        return null;
    });
    
    console.log("[TidesChart] Label Waktu (X-axis):", labels); // LOG 3
    console.log("[TidesChart] Nilai Tides (Y-axis):", tidesValues); // LOG 4
    
    const allTidesNullOrEmpty = tidesValues.every(val => val === null);
    console.log("[TidesChart] Apakah semua nilai tides null/kosong?", allTidesNullOrEmpty); // LOG 5

    if (allTidesNullOrEmpty) {
        if (tidesPlaceholder) {
            tidesPlaceholder.innerHTML = 'Data pasang surut tidak tersedia untuk periode ini (semua nilai kosong).<br>Grafik tidak dapat ditampilkan.';
            tidesPlaceholder.style.display = 'flex';
        }
        if (tidesCanvas) tidesCanvas.style.display = 'none';
        if (tidesChartInstance) {
            tidesChartInstance.destroy();
            tidesChartInstance = null;
            console.log("[TidesChart] Chart lama dihancurkan karena semua data tides kosong.");
        }
        console.warn("[TidesChart] Semua data tides null atau kosong, grafik tidak dibuat.");
        return;
    }

    if (tidesPlaceholder) tidesPlaceholder.style.display = 'none';
    if (tidesCanvas) tidesCanvas.style.display = 'block'; 
    console.log("[TidesChart] Data tides valid, mencoba membuat atau memperbarui grafik."); // LOG 6

    const pointBackgroundColors = tidesValues.map((_, index) => index === activeIndex ? 'rgba(220, 53, 69, 1)' : 'rgba(0, 123, 255, 0.8)');
    const pointRadius = tidesValues.map((_, index) => index === activeIndex ? 6 : 3);
    const pointBorderColor = tidesValues.map((_, index) => index === activeIndex ? 'rgba(220, 53, 69, 1)' : 'rgba(0, 123, 255, 1)');
    const pointHitRadius = tidesValues.map((_, index) => index === activeIndex ? 10 : 5);

    if (tidesChartInstance) {
        console.log("[TidesChart] Memperbarui chart yang sudah ada.");
        tidesChartInstance.data.labels = labels;
        tidesChartInstance.data.datasets[0].data = tidesValues;
        tidesChartInstance.data.datasets[0].pointBackgroundColor = pointBackgroundColors;
        tidesChartInstance.data.datasets[0].pointBorderColor = pointBorderColor;
        tidesChartInstance.data.datasets[0].radius = pointRadius;
        tidesChartInstance.data.datasets[0].hitRadius = pointHitRadius;
        tidesChartInstance.data.datasets[0].hoverRadius = pointRadius.map(r => r > 3 ? r + 2 : 5);
        tidesChartInstance.update();
        console.log("[TidesChart] Chart berhasil diperbarui.");
    } else {
        console.log("[TidesChart] Membuat chart baru.");
        if (!tidesCanvas) {
            console.error("[TidesChart] Elemen canvas #tidesChart tidak ditemukan.");
            return;
        }
        const ctxTides = tidesCanvas.getContext('2d');
        try {
            tidesChartInstance = new Chart(ctxTides, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pasang Surut (m)',
                        data: tidesValues,
                        borderColor: 'rgba(0, 123, 255, 1)',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        fill: true,
                        tension: 0.3,
                        borderWidth: 2,
                        pointBackgroundColor: pointBackgroundColors,
                        pointBorderColor: pointBorderColor,
                        pointRadius: pointRadius,
                        pointHoverRadius: pointRadius.map(r => r > 3 ? r + 2 : 5),
                        pointHitRadius: pointHitRadius,
                        spanGaps: true
                    }]
                },
                options: { // Opsi chart seperti sebelumnya
                    responsive: true, maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: false, title: { display: true, text: 'Ketinggian (m)' }, grid: { color: '#e9ecef' }, ticks: { callback: function(value) { return value.toFixed(1) + ' m'; } } },
                        x: { title: { display: true, text: 'Waktu (Jam WIB)' }, grid: { display: false } }
                    },
                    plugins: { legend: { display: false }, tooltip: { /* ... callbacks ... */ } },
                    interaction: { mode: 'nearest', axis: 'x', intersect: false }
                }
            });
            console.log("[TidesChart] Chart baru berhasil dibuat."); // LOG 7
        } catch (e) {
            console.error("[TidesChart] Error saat membuat instance Chart.js:", e); // LOG 8
            if (tidesPlaceholder) {
                tidesPlaceholder.innerHTML = 'Gagal membuat grafik pasang surut.<br>Error: ' + e.message;
                tidesPlaceholder.style.display = 'flex';
            }
            if (tidesCanvas) tidesCanvas.style.display = 'none';
        }
    }
}

    // === FUNGSI UTAMA UNTUK UPDATE TAMPILAN ===
    function updateDisplay(index) {
        console.log(`[updateDisplay] Fungsi dipanggil untuk index: ${index}.`); // LOG E: Cek apakah updateDisplay dipanggil
        if (!forecastDataDay1 || forecastDataDay1.length === 0 || !forecastDataDay1[index]) {
            console.error("Data prakiraan tidak tersedia untuk index:", index, "pada updateDisplay.");
            return;
        }
        const currentForecast = forecastDataDay1[index];

        if (displayedDateEl) displayedDateEl.textContent = formatIndonesianDate(currentForecast.forecast_time_utc, 'dateonly');
        if (displayedTimeEl) displayedTimeEl.textContent = formatIndonesianDate(currentForecast.forecast_time_utc, 'timeonly');
        
        const weatherDescription = currentForecast.weather_condition || ""; 
        if (weatherIconEl) {
            // Ini adalah baris yang menyebabkan error (line 141 di file Anda)
            weatherIconEl.textContent = getWeatherSymbolName(weatherDescription); 
        }
        
        if (weatherConditionEl) weatherConditionEl.textContent = currentForecast.weather_condition || "N/A";
        if (temperatureEl) temperatureEl.textContent = (currentForecast.temp_avg_celsius !== null ? currentForecast.temp_avg_celsius : "--") + "°C";
        if (humidityEl) humidityEl.textContent = "RH " + (currentForecast.rh_avg_percent !== null ? currentForecast.rh_avg_percent : "--") + "%";
        
        const windDir = currentForecast.wind_direction || ($currentForecast.wind_form || "--"); // Menangani typo wind_form jika masih ada
        const windSpeed = currentForecast.wind_speed_unit !== null ? currentForecast.wind_speed_unit : '--';
        if (windValueEl) windValueEl.textContent = `${windDir} / ${windSpeed} KT`;
        if (windGustEl) windGustEl.textContent = `Hembusan ${currentForecast.wind_gust_unit !== null ? currentForecast.wind_gust_unit : '--'} KT`;
        
        const waveHeight = currentForecast.wave_height_m !== null ? parseFloat(currentForecast.wave_height_m).toFixed(1) : '--';
        if (waveValueEl) waveValueEl.textContent = `${waveHeight} M`;
        if (waveCategoryEl) waveCategoryEl.textContent = (currentForecast.wave_category || "---").toUpperCase();

        // Panggil fungsi untuk membuat atau memperbarui grafik tides
        createOrUpdateTidesChart(index); 
    }

    // === LOGIKA UTAMA SETELAH DOM READY ===
    if (forecastDataDay1.length === 0 && phpSelectedPortFile) {
        console.log("[JS Init] Tidak ada data detail forecastDataDay1, tampilan diatur untuk 'tidak ada data'.");
        // Jika tidak ada data detail sama sekali
        const mainWeatherInfoEl = document.querySelector('.main-weather-info');
        if(mainWeatherInfoEl) {
            mainWeatherInfoEl.innerHTML = "<p style='text-align:center; color:orange;'>Data prakiraan detail untuk hari ini tidak tersedia untuk port ini.</p>";
        }
        if (tidesPlaceholder) {
            tidesPlaceholder.textContent = 'Data prakiraan tidak tersedia, grafik pasang surut juga tidak dapat ditampilkan.';
            tidesPlaceholder.style.display = 'flex';
        }
        if (tidesCanvas) tidesCanvas.style.display = 'none';
        if (timeSlider) timeSlider.disabled = true;
        return; // Hentikan jika tidak ada data sama sekali
    }
    
    if (timeSlider && forecastDataDay1.length > 0) {
        console.log("[JS Init] Memasang event listener ke timeSlider."); // LOG F: Cek apakah kita sampai sini
        timeSlider.max = forecastDataDay1.length - 1;
        timeSlider.value = 0; // Selalu mulai dari index 0
        
        const firstForecastTime = forecastDataDay1[0].forecast_time_utc;
        if (sliderTimeLabelStart && firstForecastTime) {
             const firstDate = new Date(firstForecastTime.replace(" ", "T") + "Z");
             firstDate.setHours(firstDate.getHours() + 7); // WIB
             sliderTimeLabelStart.textContent = String(firstDate.getUTCHours()).padStart(2, '0');
        }

        const lastForecastTime = forecastDataDay1[forecastDataDay1.length - 1].forecast_time_utc;
        if (sliderTimeLabelEnd && lastForecastTime) {
             const lastDate = new Date(lastForecastTime.replace(" ", "T") + "Z");
             lastDate.setHours(lastDate.getHours() + 7); // WIB
             sliderTimeLabelEnd.textContent = String(lastDate.getUTCHours()).padStart(2, '0');
        }

        timeSlider.addEventListener('input', function() {
            updateDisplay(parseInt(this.value, 10));
             // DEBUG untuk slider event:
            console.log("[Slider Event] Slider digerakkan. Nilai index baru:", newIndex); // LOG G: INI YANG HARUSNYA MUNCUL SAAT SLIDER DIGERAKKAN
            updateDisplay(newIndex);
        });
        
        console.log("[JS Init] Memanggil updateDisplay(0) untuk tampilan awal."); // LOG H
        updateDisplay(0); 

    } else {
        // Kondisi ini akan terpenuhi jika timeSlider tidak ditemukan atau forecastDataDay1 kosong
        console.warn("[JS Init] Slider tidak diinisialisasi.");
        if (!timeSlider) console.error("[JS Init] Elemen #timeSlider TIDAK DITEMUKAN di DOM!");
        if (!forecastDataDay1 || forecastDataDay1.length === 0) console.warn("[JS Init] forecastDataDay1 kosong atau tidak valid untuk slider.");
    }
});