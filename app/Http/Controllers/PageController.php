<?php
// app/Http/Controllers/PageController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Panggil kedua controller spesialis yang sudah kita buat
use App\Http\Controllers\Weather\BerandaController;
use App\Http\Controllers\Weather\PeringatanDiniController;
use App\Http\Controllers\Component\YoutubeController;
use App\Http\Controllers\Component\EarthquakeController;
use App\Http\Controllers\Component\HotspotController;
use App\Http\Controllers\Component\FdrsController;
use App\Http\Controllers\Component\NewsController;

class PageController extends Controller
{
    public function index()
    {
        // 1. Buat instance dari controller spesialis
        $berandaController = new BerandaController();
        $peringatanDiniController = new PeringatanDiniController();
        $youtubeController = new YoutubeController();
        $earthquakeController = new EarthquakeController();
        $hotspotController = new HotspotController();
        $fdrsController = new FdrsController();
        $newsController = new NewsController();

        // 2. Panggil metode getData() dari masing-masing controller
        $dataCuaca = $berandaController->getData();
        $dataPeringatan = $peringatanDiniController->getData();
        $channel_id = 'UC-KYgQQBxl7zNV60yHnHhxA'; // ID Channel Anda
        $dataVideo = $youtubeController->getData($channel_id, 5);
        $dataGempa = $earthquakeController->getData();
        $dataHotspot = $hotspotController->getData();
        $dataFdrs = $fdrsController->getData();
        $dataBerita = $newsController->getData(3);

        // 3. Kirim semua data yang sudah terkumpul ke view induk
        return view('index', [
            'cuaca_tiga_hari_beranda' => $dataCuaca,
            'peringatan_dini_riau' => $dataPeringatan,
            'dataVideo'               => $dataVideo,
            'dataGempa'               => $dataGempa,
            'dataHotspot' => $dataHotspot,
            'dataFdrs' => $dataFdrs,
            'dataBerita' => $dataBerita,
        ]);
    }
}