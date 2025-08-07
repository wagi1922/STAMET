<?php

namespace App\Http\Controllers\Component;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;

class YoutubeController extends Controller
{
    /**
     * Mengambil data video terbaru dari channel YouTube melalui RSS feed.
     */
    public function getData(string $channelId, int $limit = 5)
    {
        // URL ini dimodifikasi sesuai file Anda
        $rss_url = 'https://www.youtube.com/feeds/videos.xml?channel_id=' . $channelId;
        $videos = [];

        try {
            // Menggunakan HTTP Client Laravel, lebih modern dari cURL
            // Kita tambahkan withoutVerifying() untuk lingkungan lokal Anda
            $response = Http::withoutVerifying()->get($rss_url);

            if ($response->failed()) {
                // Jika request gagal (misal: 404, 500), laporkan error
                report("Gagal mengakses RSS Feed YouTube. Status: " . $response->status());
                return [];
            }

            $xmlString = $response->body();
            
            // Nonaktifkan error internal libxml agar tidak mengganggu tampilan
            libxml_use_internal_errors(true);
            $xml = new SimpleXMLElement($xmlString);

            $count = 0;
            foreach ($xml->entry as $entry) {
                if ($count >= $limit) {
                    break;
                }
                
                $media = $entry->children('media', true);
                $yt = $entry->children('yt', true);

                $videos[] = [
                    'title'     => (string)$entry->title,
                    'link'      => (string)$entry->link->attributes()->href,
                    'thumbnail' => (string)$media->group->thumbnail->attributes()->url,
                    'video_id'  => (string)$yt->videoId,
                ];
                $count++;
            }
        } catch (\Exception $e) {
            // Tangani jika ada error koneksi atau parsing XML
            report($e);
            return []; // Kembalikan array kosong jika ada masalah
        }

        return $videos;
    }
}