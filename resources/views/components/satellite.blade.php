{{-- resources/views/components/satellite.blade.php --}}

<section class="satellite-imagery-section">
    <div class="section-header" style="justify-content: center; text-align: center;">
        <h2>Citra Satelit Cuaca Wilayah Riau</h2>
    </div>

    <div class="satellite-cards-container">
        <div class="satellite-card">
            <div class="satellite-image-container">
                <img class="satellite-image-zoomable" src="https://inderaja.bmkg.go.id/IMAGE/HIMA/H08_EH_Riau.png" alt="Citra Satelit Himawari-9 IR Enhanced">
            </div>
            <div class="satellite-card-content">
                <h3>Himawari-9 IR Enhanced</h3>
                <div class="satellite-description-full">
                    <p>Pada produk Himawari-9 EH menunjukkan suhu puncak awan yang didapat dari pengamatan radiasi pada panjang gelombang 10.4 mikrometer yang kemudian diklasifikasi dengan pewarnaan tertentu, dimana warna hitam atau biru menunjukkan tidak terdapat pembentukan awan yang banyak (cerah), sedangkan semakin dingin suhu puncak awan, dimana warna mendekati jingga hingga merah, menunjukan pertumbuhan awan yang signifikan dan berpotensi terbentuknya awan Cumulonimbus.</p>
                </div>
                <a href="#" class="satellite-toggle-description selengkapnya-link">
                    <span class="toggle-text">Lihat Selengkapnya</span> 
                    <span class="material-symbols-outlined">arrow_forward</span>
                </a>
            </div>
        </div>

        <div class="satellite-card">
            <div class="satellite-image-container">
                <img class="satellite-image-zoomable" src="https://inderaja.bmkg.go.id/IMAGE/HIMA/H08_RP_Riau.png" alt="Citra Satelit Himawari-9 Rainfall Potential">
            </div>
            <div class="satellite-card-content">
                <h3>Himawari-9 Rainfall Potential</h3>
                <div class="satellite-description-full">
                    <p>Produk turunan Himawari-9 Potential Rainfall adalah produk yang dapat digunakan untuk mengestimasi potensi curah hujan, yang disajikan berdasarkan kategori ringan, sedang, lebat, hingga sangat lebat, dengan menggunakan hubungan antara suhu puncak awan dengan curah hujan yang berpotensi dihasilkan.</p>
                </div>
                <a href="#" class="satellite-toggle-description selengkapnya-link">
                    <span class="toggle-text">Lihat Selengkapnya</span>  
                    <span class="material-symbols-outlined">arrow_forward</span>
                </a>
            </div>
        </div>

        <div class="satellite-card">
            <div class="satellite-image-container">
                <img class="satellite-image-zoomable" src="https://inderaja.bmkg.go.id/IMAGE/HCAI/CLC/HCAI_CLC_Riau.png" alt="Citra Satelit Himawari-9 Convective Cloud">
            </div>
            <div class="satellite-card-content">
                <h3>Himawari-9 Convective Cloud</h3>
                <div class="satellite-description-full">
                    <p>Produk ini adalah hasil kolaborasi penelitian BMKG dengan JMA untuk mengidentifikasi secara objektif jenis awan konvektif yang ditangkap oleh band infrared dan visibel dari satelit Himawari. Produk ini diupdate setiap 1 jam.</p>
                </div>
                <a href="#" class="satellite-toggle-description selengkapnya-link">
                    <span class="toggle-text">Lihat Selengkapnya</span> 
                    <span class="material-symbols-outlined">arrow_forward</span>
                </a>
            </div>
        </div>
    </div>
</section>