<?php

namespace App\Http\Controllers\Component;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB; // Gunakan DB Facade Laravel
use Illuminate\Support\Str;        // Gunakan helper String untuk memotong teks
use Carbon\Carbon;                 // Gunakan Carbon untuk memformat tanggal

class NewsController extends Controller
{
    /**
     * Mengambil data berita terbaru dari database.
     */
    public function getData(int $limit = 3)
    {
        try {
            // Mengambil data dari tabel 'berita' menggunakan cara Laravel
            $newsItems = DB::table('berita')
                ->select('id', 'judul', 'isi_lengkap', 'gambar_unggulan', 'tanggal_publish', 'penulis')
                ->orderByDesc('tanggal_publish')
                ->limit($limit)
                ->get();

            // Memproses setiap item berita untuk persiapan di view
            $processedNews = $newsItems->map(function ($item) {
                // Mengubah item (stdClass) menjadi array
                $itemArray = (array)$item;

                // Memotong isi berita menggunakan Str::limit() (pengganti truncate_text)
                $itemArray['summary'] = Str::limit(strip_tags($item->isi_lengkap), 150, '...');

                // Memformat tanggal menggunakan Carbon
                $itemArray['formatted_date'] = Carbon::parse($item->tanggal_publish)->translatedFormat('d F Y');

                return $itemArray;
            });

            return $processedNews;

        } catch (\Exception $e) {
            report($e); // Laporkan error ke log Laravel
            return collect(); // Kembalikan koleksi kosong jika ada error
        }
    }
}