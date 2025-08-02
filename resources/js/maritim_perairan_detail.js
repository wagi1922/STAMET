// File: js/maritim_perairan_detail.js

document.addEventListener('DOMContentLoaded', function() {
    if (typeof phpSelectedSumberFilePerairan === 'undefined' || !phpSelectedSumberFilePerairan || 
        typeof phpForecastDataDay1Perairan === 'undefined') {
        return; 
    }

    const forecastDataDay1 = phpForecastDataDay1Perairan; 
    const timeSlider = document.getElementById('timeSliderPerairan'); // ID Slider baru
    
    // Elemen DOM dengan ID baru (akhiran -perairan)
    const displayedDateEl = document.getElementById('displayed-date-perairan');
    const displayedTimeEl = document.getElementById('displayed-time-perairan');
    const weatherIconEl = document.getElementById('weather-icon-display-perairan');
    const weatherConditionEl = document.querySelector('#weather-text-display-perairan .condition');
    const temperatureEl = document.querySelector('#weather-text-display-perairan .temperature');
    const humidityEl = document.querySelector('#weather-text-display-perairan .humidity');
    
    const windValueEl = document.querySelector('#wind-text-display-perairan .value');
    const windGustEl = document.querySelector('#wind-text-display-perairan .gust');
    
    const waveValueEl = document.querySelector('#wave-text-display-perairan .value');
    const waveCategoryEl = document.querySelector('#wave-text-display-perairan .category');

    // Elemen BARU untuk Arus
    const currentDirectionEl = document.querySelector('#current-text-display-perairan .value'); // Mengasumsikan struktur .value untuk arah
    const currentSpeedEl = document.querySelector('#current-text-display-perairan .speed');   // Mengasumsikan struktur .speed untuk kecepatan

    const sliderTimeLabelStart = document.getElementById('slider-time-label-start-perairan');
    const sliderTimeLabelEnd = document.getElementById('slider-time-label-end-perairan');

    // Fungsi formatIndonesianDate dan getWeatherSymbolName bisa Anda salin dari maritim_pelabuhan_detail.js
    // atau letakkan di file JS global jika ingin dipakai bersama.
    // Untuk sekarang, kita anggap sudah ada atau Anda salin.
    // Pastikan fungsi formatIndonesianDate dan getWeatherSymbolName terdefinisi SEBELUM updateDisplay

    function formatIndonesianDate(dateStringUTC, type = 'full') {
        // ... (Definisi fungsi formatIndonesianDate yang sama persis) ...
        if (!dateStringUTC) return "N/A";
        let parsableDateStringUTC = dateStringUTC;
        if (dateStringUTC.includes(" ") && !dateStringUTC.includes("T")) {
            parsableDateStringUTC = dateStringUTC.replace(" ", "T");
        }
        if (!parsableDateStringUTC.endsWith("Z")) {
            parsableDateStringUTC += "Z";
        }
        const date = new Date(parsableDateStringUTC);
        if (isNaN(date.getTime())) { return "Invalid Date"; }
        const wibOffset = 7 * 60 * 60 * 1000;
        const dateWIB = new Date(date.getTime() + wibOffset);
        const dayNames = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
        const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        if (type === 'dateonly') return `${dayNames[dateWIB.getUTCDay()]}, ${dateWIB.getUTCDate()} ${monthNames[dateWIB.getUTCMonth()]} ${dateWIB.getUTCFullYear()}`;
        if (type === 'timeonly') return `${String(dateWIB.getUTCHours()).padStart(2, '0')}.${String(dateWIB.getUTCMinutes()).padStart(2, '0')} WIB`;
        return `${dayNames[dateWIB.getUTCDay()]}, ${dateWIB.getUTCDate()} ${monthNames[dateWIB.getUTCMonth()]} ${dateWIB.getUTCFullYear()} ${String(dateWIB.getUTCHours()).padStart(2, '0')}.${String(dateWIB.getUTCMinutes()).padStart(2, '0')} WIB`;
    }

    function getWeatherSymbolName(weatherDesc) {
        // ... (Definisi fungsi getWeatherSymbolName yang sama persis) ...
        if (!weatherDesc) return 'thermostat';
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
        return symbolToReturn;
    }


    function updateDisplayPerairan(index) {
        if (!forecastDataDay1 || !forecastDataDay1[index]) {
            console.error("[PERAIRAN] Data tidak valid untuk index:", index);
            return;
        }
        const currentForecast = forecastDataDay1[index];

        if (displayedDateEl) displayedDateEl.textContent = formatIndonesianDate(currentForecast.forecast_time_utc, 'dateonly');
        if (displayedTimeEl) displayedTimeEl.textContent = formatIndonesianDate(currentForecast.forecast_time_utc, 'timeonly');
        
        if (weatherIconEl) {
            weatherIconEl.textContent = getWeatherSymbolName(currentForecast.weather_condition || "");
        }
        if (weatherConditionEl) weatherConditionEl.textContent = currentForecast.weather_condition || "N/A";
        if (temperatureEl) temperatureEl.textContent = (currentForecast.temp_avg_celsius !== null ? currentForecast.temp_avg_celsius : "--") + "°C";
        if (humidityEl) humidityEl.textContent = "RH " + (currentForecast.rh_avg_percent !== null ? currentForecast.rh_avg_percent : "--") + "%";
        
        const windDir = currentForecast.wind_direction || ($currentForecast.wind_form || "--");
        const windSpeed = currentForecast.wind_speed_unit !== null ? currentForecast.wind_speed_unit : '--';
        if (windValueEl) windValueEl.textContent = `${windDir} / ${windSpeed} KT`;
        if (windGustEl) windGustEl.textContent = `Hembusan ${currentForecast.wind_gust_unit !== null ? currentForecast.wind_gust_unit : '--'} KT`;
        
        const waveHeight = currentForecast.wave_height_m !== null ? parseFloat(currentForecast.wave_height_m).toFixed(1) : '--';
        if (waveValueEl) waveValueEl.textContent = `${waveHeight} M`;
        if (waveCategoryEl) waveCategoryEl.textContent = (currentForecast.wave_category || "---").toUpperCase();

        // Update Info Arus
        if (currentDirectionEl) currentDirectionEl.textContent = currentForecast.current_direction || "--";
        if (currentSpeedEl) {
            // Sesuaikan unit "CM/S" jika data Anda berbeda. Di DB kita simpan nilainya saja.
            // Anda mungkin perlu konversi unit atau memiliki kolom unit terpisah jika bervariasi.
            currentSpeedEl.textContent = (currentForecast.current_speed_unit !== null ? currentForecast.current_speed_unit : "--") + " CM/S"; 
        }
    }

    if (forecastDataDay1.length === 0 && phpSelectedSumberFilePerairan) {
        const mainWeatherInfoEl = document.querySelector('.main-weather-info-perairan');
        if(mainWeatherInfoEl) {
            mainWeatherInfoEl.innerHTML = "<p style='text-align:center; color:orange;'>Data prakiraan detail untuk hari ini tidak tersedia.</p>";
        }
        if(timeSlider) timeSlider.disabled = true;
        return;
    }
    
    if (timeSlider && forecastDataDay1 && forecastDataDay1.length > 0) {
        timeSlider.max = forecastDataDay1.length - 1;
        timeSlider.value = 0; 
        
        // Update label slider
        const firstForecastTime = forecastDataDay1[0].forecast_time_utc;
        if (sliderTimeLabelStart && firstForecastTime) {
             const firstDate = new Date(firstForecastTime.replace(" ", "T") + "Z");
             firstDate.setHours(firstDate.getHours() + 7);
             sliderTimeLabelStart.textContent = String(firstDate.getUTCHours()).padStart(2, '0');
        }
        const lastForecastTime = forecastDataDay1[forecastDataDay1.length - 1].forecast_time_utc;
        if (sliderTimeLabelEnd && lastForecastTime) {
             const lastDate = new Date(lastForecastTime.replace(" ", "T") + "Z");
             lastDate.setHours(lastDate.getHours() + 7);
             sliderTimeLabelEnd.textContent = String(lastDate.getUTCHours()).padStart(2, '0');
        }

        timeSlider.addEventListener('input', function() {
            updateDisplayPerairan(parseInt(this.value, 10));
        });
        
        updateDisplayPerairan(0); // Tampilkan data awal
    }
});