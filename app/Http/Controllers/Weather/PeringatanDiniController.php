<?php

// app/Http/Controllers/Weather/PeringatanDiniController.php

namespace App\Http\Controllers\Weather;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // Ganti file_get_contents dengan HTTP Client Laravel
use DOMDocument;
use DOMXPath;

class PeringatanDiniController extends Controller
{
    public function getData()
    {
        $url = 'https://www.bmkg.go.id/cuaca/peringatan-dini-cuaca.bmkg?id=14&prop=14&nama_prop=Riau';
        $peringatan = [
            'is_warning_present' => false,
            'narrative' => 'Saat ini Riau belum ada Peringatan Dini Cuaca.',
            'source_url' => $url
        ];

        // Gunakan HTTP Client Laravel yang lebih modern dan aman
        try {
            $response = Http::get($url);
            if ($response->failed()) {
                $peringatan['narrative'] = "Tidak dapat mengambil data peringatan dini saat ini.";
                return $peringatan;
            }
            $html_content = $response->body();
        } catch (\Exception $e) {
            $peringatan['narrative'] = "Terjadi masalah koneksi saat mengambil data peringatan dini.";
            return $peringatan;
        }
        
        $dom = new DOMDocument();
        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html_content);
        $xpath = new DOMXPath($dom);

        $queryXPath = "//p[contains(@class, 'prose') and contains(@class, 'text-[#334155]') and (starts-with(normalize-space(.), 'Peringatan Dini Cuaca Wilayah Riau') or starts-with(normalize-space(.), 'UPDATE Peringatan Dini Cuaca Wilayah Riau'))]";
        $nodesPeringatan = $xpath->query($queryXPath);

        if ($nodesPeringatan && $nodesPeringatan->length > 0) {
            $mainWarningNode = $nodesPeringatan->item(0);
            if ($mainWarningNode) {
                $scraped_narrative_full = trim($mainWarningNode->nodeValue);
                $scraped_narrative_full = trim(preg_replace('/Prakirawan BMKG - Riau\s*$/i', '', $scraped_narrative_full));
                
                if (!empty($scraped_narrative_full) && stripos($scraped_narrative_full, 'tidak terdapat peringatan dini') === false && strlen($scraped_narrative_full) > 30) {
                    $peringatan['is_warning_present'] = true;
                    $peringatan['narrative'] = nl2br(e($scraped_narrative_full));
                }
            }
        }
        
        return $peringatan;
    }
}