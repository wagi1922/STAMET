<?php

namespace App\Http\Controllers\Component;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon; // Gunakan Carbon untuk memformat tanggal

class EarthquakeController extends Controller
{
    /**
     * Mengambil dan memproses data gempa terkini dari BMKG.
     */
    public function getData()
    {
        $url = "https://data.bmkg.go.id/DataMKG/TEWS/autogempa.json";

        try {
            $response = Http::withoutVerifying()->get($url);

            if ($response->failed() || !isset($response->json()['Infogempa']['gempa'])) {
                report("Gagal mengambil atau memvalidasi data gempa dari BMKG.");
                return null; // Kembalikan null jika gagal
            }

            // Ambil data gempa utama
            $gempa = $response->json()['Infogempa']['gempa'];

            // 1. Tambahkan URL Shakemap yang lengkap
            $gempa['ShakemapUrl'] = "https://data.bmkg.go.id/DataMKG/TEWS/" . $gempa['Shakemap'];

            // 2. Format tanggal menggunakan Carbon (cara Laravel)
            // Mengubah '2025-08-07T12:34:56+07:00' menjadi '07 Agustus 2025, 12:34 WIB'
            $gempa['FormattedDateTime'] = Carbon::parse($gempa['DateTime'])
                                                ->translatedFormat('d F Y, H:i \W\I\B');

            // 3. Tentukan class CSS untuk potensi tsunami
            $gempa['potensi_class'] = 'tsunami-aman'; // Default
            if (stripos($gempa['Potensi'], 'tidak berpotensi') === false) {
                $gempa['potensi_class'] = 'tsunami-waspada';
            }

            return $gempa;

        } catch (\Exception $e) {
            report($e);
            return null; // Kembalikan null jika ada error koneksi
        }
    }
}