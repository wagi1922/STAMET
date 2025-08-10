{{-- components/beranda.blade.php (SUDAH BERSIH) --}}

<section class="cuaca-hari-ini-section"> 
    <div class="section-header"> 
        <h2>Prakiraan Cuaca</h2>
        <div class="tabs-cuaca"> 
            <button class="tab-button active">HARI INI</button>
            <button class="tab-button">BESOK</button>
            <button class="tab-button">LUSA</button>
        </div>
        <a href="#" class="lihat-semua">Lihat Semuanya <span class="material-symbols-outlined">arrow_forward</span></a>
    </div>
    <div class="cuaca-cards-wrapper">
        <button class="nav-arrow prev-arrow" id="cuacaPrevArrow"> 
            <span class="material-symbols-outlined">chevron_left</span>
        </button>
        <div class="cuaca-cards-container" id="cuacaCardsContainer"> 
            @forelse ($cuaca_tiga_hari_beranda as $index_kota => $data_kota_item)
                @php $data_render = $data_kota_item['hari_ini']; @endphp
                <a href="#" class="cuaca-card" data-kota-index="{{ $index_kota }}"> 
                    <div class="card-location">{{ $data_kota_item['nama_kota_asli'] }}</div>
                    <div class="card-date">{{ $data_render['tanggal'] }}</div>
                    <div class="card-icon"><span class="material-symbols-outlined">{{ $data_render['icon'] }}</span></div>
                    <div class="card-temp">{{ $data_render['suhu'] }}°C</div>
                    <div class="card-desc">{{ $data_render['deskripsi'] }}</div>
                    <div class="card-arrow"><span class="material-symbols-outlined">chevron_right</span></div>
                </a>
            @empty
                <p>Data cuaca belum tersedia.</p>
            @endforelse
        </div>
        <button class="nav-arrow next-arrow" id="cuacaNextArrow">
            <span class="material-symbols-outlined">chevron_right</span>
        </button>
    </div>
</section>

@push('scripts')
<script>
    // Menyimpan data dari PHP ke JavaScript.
    // Pastikan nama variabelnya cocok dengan yang dikirim dari PageController.
    // Jika di PageController Anda mengirim ['cuaca_tiga_hari_beranda' => $data], maka gunakan $cuaca_tiga_hari_beranda
    const dataPrakiraanLengkap = @json($cuaca_tiga_hari_beranda ?? []);
    var dataPrakiraanTigaHariGlobal = @json($cuaca_tiga_hari_beranda ?? []);

    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tabs-cuaca .tab-button');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                tabButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                // Dapatkan key hari dari text content atau data-attribute
                const hariYangDipilih = this.textContent.trim().toLowerCase().replace(' ', '_'); // 'hari_ini', 'besok', 'lusa'
                updateWeatherCards(hariYangDipilih);
            });
        });
    });

    function updateWeatherCards(hariKey) {
        const weatherCards = document.querySelectorAll('.cuaca-card');

        weatherCards.forEach(card => {
            const index = card.dataset.kotaIndex; // Pastikan card Anda punya `data-kota-index`
            const dataKota = dataPrakiraanLengkap[index];
            
            if (dataKota && dataKota[hariKey]) {
                const prakiraan = dataKota[hariKey];

                card.querySelector('.card-date').textContent = prakiraan.tanggal;
                card.querySelector('.card-icon span').textContent = prakiraan.icon;
                card.querySelector('.card-temp').textContent = prakiraan.suhu + '°C';
                card.querySelector('.card-desc').textContent = prakiraan.deskripsi;
            }
        });
    }
</script>
@endpush