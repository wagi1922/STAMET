<?php
// app/Http/Controllers/PageController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Panggil kedua controller spesialis yang sudah kita buat
use App\Http\Controllers\Weather\BerandaController;
use App\Http\Controllers\Weather\PeringatanDiniController;
use App\Http\Controllers\Component\YoutubeController;
use App\Http\Controllers\Component\EarthquakeController;

class PageController extends Controller
{
    public function index()
    {
        // 1. Buat instance dari controller spesialis
        $berandaController = new BerandaController();
        $peringatanDiniController = new PeringatanDiniController();
        $youtubeController = new YoutubeController();
        $earthquakeController = new EarthquakeController();

        // 2. Panggil metode getData() dari masing-masing controller
        $dataCuaca = $berandaController->getData();
        $dataPeringatan = $peringatanDiniController->getData();
        $channel_id = 'UC-KYgQQBxl7zNV60yHnHhxA'; // ID Channel Anda
        $dataVideo = $youtubeController->getData($channel_id, 5);
        $dataGempa = $earthquakeController->getData();

        // 3. Kirim semua data yang sudah terkumpul ke view induk
        return view('index', [
            'cuaca_tiga_hari_beranda' => $dataCuaca,
            'peringatan_dini_riau' => $dataPeringatan,
            'dataVideo'               => $dataVideo,
            'dataGempa'               => $dataGempa,
        ]);
    }
}