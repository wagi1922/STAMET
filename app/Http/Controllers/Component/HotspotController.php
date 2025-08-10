<?php

namespace App\Http\Controllers\Component;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB; // Gunakan DB Facade Laravel
use Carbon\Carbon; // Gunakan Carbon untuk tanggal

class HotspotController extends Controller
{
    /**
     * Mengambil data hotspot terkini dari database.
     */
    public function getData()
    {
        $hotspotData = [
            'hotspots' => [],
            'info_tanggal' => 'Data hotspot tidak tersedia.'
        ];

        try {
            // 1. Cari tanggal terakhir yang ada data, menggunakan cara Laravel
            $latest_date = DB::table('hotspot_data')
                            ->where('provinsi', 'RIAU')
                            ->max('tanggal_wib');

            // 2. Jika tanggal ditemukan, ambil semua data pada tanggal tersebut
            if ($latest_date) {
                // Query untuk mengambil detail hotspot menggunakan Query Builder
                $hotspots = DB::table('hotspot_data')
                    ->select('bujur', 'lintang', 'kepercayaan', 'kabupaten', 'kecamatan', 'satelit', 'tanggal_wib', 'waktu_wib')
                    ->where('provinsi', 'RIAU')
                    ->whereDate('tanggal_wib', $latest_date) // Lebih aman menggunakan whereDate
                    ->orderByDesc('timestamp_data')
                    ->get();
                
                // Siapkan informasi tanggal untuk ditampilkan
                $hotspotData['info_tanggal'] = "Data diperbarui pada: " . Carbon::parse($latest_date)->translatedFormat('d F Y');
                
                // Format ulang data agar siap digunakan oleh JavaScript
                foreach ($hotspots as $hs) {
                    $hotspotData['hotspots'][] = [
                        'lon'        => $hs->bujur,
                        'lat'        => $hs->lintang,
                        'confidence' => $hs->kepercayaan,
                        'kabupaten'  => $hs->kabupaten,
                        'kecamatan'  => $hs->kecamatan,
                        'satelit'    => $hs->satelit,
                        'tanggal'    => Carbon::parse($hs->tanggal_wib . " " . $hs->waktu_wib)->translatedFormat('d/m/y'),
                        'waktu'      => Carbon::parse($hs->waktu_wib)->format('H.i'),
                    ];
                }
            }
        } catch (\Exception $e) {
            // Jika ada error koneksi atau query, laporkan ke log Laravel
            report($e);
        }

        return $hotspotData;
    }
}