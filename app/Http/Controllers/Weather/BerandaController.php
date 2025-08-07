<?php

namespace App\Http\Controllers\Weather;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http; // Gunakan HTTP Client bawaan Laravel
use Carbon\Carbon; // Gunakan Carbon untuk manajemen tanggal

class BerandaController extends Controller
{
    /**
     * Metode publik ini adalah "pintu masuk" yang dipanggil oleh PageController.
     */
    public function getData(): array
    {
        return $this->getProcessedWeatherDataForHomepage();
    }

    /**
     * Metode private untuk mengambil dan memproses data prakiraan cuaca
     * untuk 12 ibukota yang akan ditampilkan di beranda.
     * Logika dari beranda.php lama Anda diintegrasikan di sini.
     */
    private function getProcessedWeatherDataForHomepage(): array
    {
        $ibukota_adm4_beranda = [
            'Pekanbaru'         => '14.71.02.1001',
            'Dumai'             => '14.72.06.1003',
            'Bengkalis'         => '14.03.01.1001',
            'Bangkinang'        => '14.01.01.1011',
            'Siak'              => '14.08.01.1001',
            'Bagan Siapi-api'   => '14.07.02.1012',
            'Pasir Pengaraian'  => '14.06.03.1001',
            'Pangkalan Kerinci' => '14.05.02.1008',
            'Taluk Kuantan'     => '14.09.02.2015',
            'Selat Panjang'     => '14.10.01.1001',
            'Rengat'            => '14.02.01.1006',
            'Tembilahan'        => '14.04.04.1001',
        ];

        $cuaca_tiga_hari_beranda = [];
        $target_jam_representatif_utc = 7; // Jam 7 UTC (14:00 WIB), bisa disesuaikan

        foreach ($ibukota_adm4_beranda as $nama_kota => $kode_adm4) {
            
            // 1. Panggil API BMKG untuk kota ini menggunakan HTTP Client Laravel
            $weather_api_data = $this->getWeatherDataRiau($kode_adm4);


            // Siapkan struktur data untuk kota ini
            $data_prakiraan_kota_ini = [
                'nama_kota_asli' => $nama_kota,
                'link_detail' => '#' // Menggunakan route helper
            ];

            $kunci_target_hari = ['hari_ini', 'besok', 'lusa'];

            // 2. Loop untuk 3 hari: Hari Ini, Besok, Lusa
            for ($hari_ke_idx = 0; $hari_ke_idx < 3; $hari_ke_idx++) {
                
                $nama_kunci_output = $kunci_target_hari[$hari_ke_idx];
                $tanggal_target = Carbon::now('UTC')->addDays($hari_ke_idx);

                // Siapkan data default
                $data_per_hari = [
                    'tanggal'   => $tanggal_target->translatedFormat('j F Y'),
                    'icon'      => 'thermostat',
                    'suhu'      => '--',
                    'deskripsi' => 'N/A',
                ];

                $prakiraan_terpilih_untuk_tampil = null;
                
                // Cek jika data dari API valid
                if (isset($weather_api_data['data'][0]['cuaca'][$hari_ke_idx]) && is_array($weather_api_data['data'][0]['cuaca'][$hari_ke_idx])) {
                    $prakiraan_harian_dari_api = $weather_api_data['data'][0]['cuaca'][$hari_ke_idx];

                    // 3. Cari prakiraan yang paling representatif untuk hari itu (sekitar siang/sore)
                    foreach ($prakiraan_harian_dari_api as $prakiraan_jam) {
                        $jam_prakiraan_utc = (int) Carbon::parse($prakiraan_jam['utc_datetime'])->format('G');
                        if ($jam_prakiraan_utc >= $target_jam_representatif_utc) {
                            $prakiraan_terpilih_untuk_tampil = $prakiraan_jam;
                            break; // Ditemukan, hentikan loop
                        }
                    }

                    // Fallback: Jika tidak ada jam yang cocok (misal, data hanya sampai pagi), ambil data terakhir yang ada
                    if (!$prakiraan_terpilih_untuk_tampil && !empty($prakiraan_harian_dari_api)) {
                        $prakiraan_terpilih_untuk_tampil = end($prakiraan_harian_dari_api);
                    }
                }
                
                // 4. Jika prakiraan representatif ditemukan, isi data_per_hari
                if ($prakiraan_terpilih_untuk_tampil) {
                    $deskripsiCuaca = $prakiraan_terpilih_untuk_tampil['weather_desc'] ?? 'N/A';
                    $data_per_hari['icon']      = $this->getIconForWeather($deskripsiCuaca);
                    $data_per_hari['suhu']      = $prakiraan_terpilih_untuk_tampil['t'] ?? '--';
                    $data_per_hari['deskripsi'] = $deskripsiCuaca;
                }
                
                $data_prakiraan_kota_ini[$nama_kunci_output] = $data_per_hari;
            } // Akhir loop for 3 hari
            
            $cuaca_tiga_hari_beranda[] = $data_prakiraan_kota_ini;
        } // Akhir foreach (loop kota)

        return $cuaca_tiga_hari_beranda;
    }
    
    /**
     * Metode helper untuk memanggil API BMKG.
     */

    function getWeatherDataRiau($kode_adm4) {
    if (empty($kode_adm4)) {
        return ["error" => "ERROR: Kode ADM4 lokasi tidak disediakan."];
    }
    $api_url = "https://api.bmkg.go.id/publik/prakiraan-cuaca?adm4=" . urlencode($kode_adm4);
    $context_options = [
        'http' => [
            'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.102 Safari/537.36\r\n" .
                        "Accept: application/json\r\n",
            'timeout' => 15,
            'ignore_errors' => true
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ]
    ];
    $context = stream_context_create($context_options);
    $response_body = @file_get_contents($api_url, false, $context);

    $status_line = $http_response_header[0] ?? 'HTTP/1.1 500 Unknown Error';
    preg_match('{HTTP\/\S*\s(\d{3})}', $status_line, $match);
    $status_code = isset($match[1]) ? intval($match[1]) : 500;

    if ($response_body === false || $status_code >= 400) {
        $error_detail = "Kode Status HTTP: " . $status_code;
        if ($response_body !== false && !empty($response_body)) {
            $error_detail .= ". Pesan Server: " . htmlspecialchars(strip_tags(substr($response_body, 0, 500)));
        } elseif (($err = error_get_last()) !== null) {
            $error_detail .= ". Detail PHP: " . $err['message'];
        }
        return ["error" => "ERROR: Gagal mengambil data dari BMKG. " . $error_detail . " - URL: " . $api_url];
    }

    $data = json_decode($response_body, true);

    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        return ["error" => "ERROR: Data bukan format JSON yang valid atau data kosong. " . htmlspecialchars(json_last_error_msg()) . " - URL: " . $api_url . " - Response: " . htmlspecialchars(substr($response_body, 0, 200))];
    }
    
    if (isset($data["meta"]["code"]) && is_numeric($data["meta"]["code"]) && $data["meta"]["code"] >= 300 ) {
         return [
            "error" => "ERROR dari API BMKG: " . htmlspecialchars($data["meta"]["message"] ?? "Tidak ada pesan detail") . " (Code: ".$data["meta"]["code"].")",
            "url_called" => $api_url,
            "raw_response_for_debug" => $data
        ];
    }

    if (!isset($data["lokasi"]) || !isset($data["data"]) || !isset($data["data"][0]["cuaca"])) {
        return [
            "error" => "ERROR: Struktur data JSON dasar ('lokasi' atau 'data[0]['cuaca']') tidak ditemukan. Kode ADM4 mungkin tidak valid atau struktur API berbeda untuk wilayah ini.",
            "url_called" => $api_url,
            "raw_response_for_debug" => $data
        ];
    }
    return $data;
}


    private function fetchWeatherFromApi(string $kode_adm4)
    {
        if (empty($kode_adm4)) return [];

        $api_url = "https://api.bmkg.go.id/publik/prakiraan-cuaca?adm4=" . urlencode($kode_adm4);
        
        try {
            $response = Http::timeout(15) // Set timeout
                              ->withHeaders(['User-Agent' => 'StametSSK2-Website/1.0'])
                              ->get($api_url);

            if ($response->successful()) {
                return $response->json();
            }
            
            // Jika gagal tapi ada respons JSON (misal error 404 dari BMKG)
            return $response->json() ?? ['error' => 'API call failed with status: ' . $response->status()];

        } catch (\Exception $e) {
            // Jika ada error koneksi (timeout, dll)
            report($e); // Laporkan error ke log Laravel
            return ['error' => 'Connection error while fetching weather data.'];
        }
    }

    /**
     * Helper kecil untuk mendapatkan nama ikon Material Symbols.
     */
    private function getIconForWeather(?string $deskripsi): string
    {
        if ($deskripsi === null) return 'thermostat';
        $deskripsi = strtolower($deskripsi);
        
        if (str_contains($deskripsi, 'hujan petir') || str_contains($deskripsi, 'badai')) return 'thunderstorm';
        if (str_contains($deskripsi, 'hujan lebat')) return 'rainy_heavy';
        if (str_contains($deskripsi, 'hujan sedang')) return 'rainy';
        if (str_contains($deskripsi, 'hujan ringan')) return 'rainy_light';
        if (str_contains($deskripsi, 'hujan')) return 'rainy'; // Fallback umum untuk hujan
        if (str_contains($deskripsi, 'cerah berawan')) return 'partly_cloudy_day';
        if (str_contains($deskripsi, 'berawan tebal')) return 'cloudy';
        if (str_contains($deskripsi, 'berawan')) return 'cloud';
        if (str_contains($deskripsi, 'cerah')) return 'sunny';
        if (str_contains($deskripsi, 'kabut') || str_contains($deskripsi, 'asap')) return 'foggy';

        return 'thermostat'; // Ikon default
    }
}