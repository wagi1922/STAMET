<?php

namespace App\Http\Controllers\Component;

use App\Http\Controllers\Controller;

class FdrsController extends Controller
{
    /**
     * Menyiapkan data FDRS untuk semua tab (Observasi, Hari Ini, Besok).
     */
    public function getData()
    {
        // Kunci untuk setiap tab
        $targets = ['obs', '00', '01'];
        $allFdrsData = [];

        foreach ($targets as $target) {
            $allFdrsData[$target] = $this->prepareSingleFdrsData($target);
        }

        return [
            'default' => $allFdrsData['obs'], // Tampilkan data 'Observasi' sebagai default
            'tabs' => $allFdrsData // Kirim data semua tab ke JavaScript
        ];
    }

    /**
     * Helper private untuk membangun data untuk satu target hari.
     * Logika ini diambil dari file infoFDRS.php Anda.
     */
    private function prepareSingleFdrsData(string $targetHari): array
    {
        $baseUrl = 'https://web-meteo.bmkg.go.id/media/data/bmkg/fdrs/';
        $namaFileGambar = '04_riau_ffmc_' . $targetHari . '.png';
        $directImageUrl = $baseUrl . $namaFileGambar;

        $info_hari = 'Data FDRS Riau (FFMC)';
        switch ($targetHari) {
            case 'obs': $info_hari = 'Data Observasi FDRS Riau (FFMC)'; break;
            case '00':  $info_hari = 'Prakiraan FDRS Hari Ini (FFMC)'; break;
            case '01':  $info_hari = 'Prakiraan FDRS Besok (FFMC)'; break;
        }

        return [
            'map_image_url' => filter_var($directImageUrl, FILTER_VALIDATE_URL) ? $directImageUrl : null,
            'info_hari'     => $info_hari
        ];
    }
}