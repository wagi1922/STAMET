<?php

// File: routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ApiKeyMiddleware;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BeritaController;
use App\Http\Controllers\Api\KlaimAnsuransiController; 
use App\Http\Controllers\Api\PengajuanMagangController;


Route::middleware([ApiKeyMiddleware::class])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Rute publik (tanpa login)
    Route::post('/pengajuan-magang', [PengajuanMagangController::class, 'store']);
    Route::get('/berita', [BeritaController::class, 'index']);
    Route::get('/berita/{berita}', [BeritaController::class, 'show']);
    Route::post('/klaim-asuransi', [KlaimAnsuransiController::class, 'store']);
});

Route::middleware(['auth:sanctum', ApiKeyMiddleware::class])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // --- PENGAJUAN MAGANG ---
    Route::get('/pengajuan-magang', [PengajuanMagangController::class, 'index']);
    Route::get('/pengajuan-magang/{pengajuanMagang}', [PengajuanMagangController::class, 'show']);
    Route::post('/pengajuan-magang/{pengajuanMagang}', [PengajuanMagangController::class, 'update']); 
    Route::delete('/pengajuan-magang/{pengajuanMagang}', [PengajuanMagangController::class, 'destroy']);

    // --- BERITA ---
    Route::post('/berita', [BeritaController::class, 'store']);
    Route::post('/berita/{berita}', [BeritaController::class, 'update']);
    Route::delete('/berita/{berita}', [BeritaController::class, 'destroy']);

    // --- KLAIM ASURANSI ---
    Route::get('/klaim-asuransi/export', [KlaimAnsuransiController::class, 'export']);
    Route::get('/klaim-asuransi/{klaimAsuransi}/export', [KlaimAnsuransiController::class, 'exportDetail']);
    Route::get('/klaim-asuransi', [KlaimAnsuransiController::class, 'index']);
    Route::get('/klaim-asuransi/{klaimAsuransi}', [KlaimAnsuransiController::class, 'show']);
    Route::post('/klaim-asuransi/{klaimAsuransi}', [KlaimAnsuransiController::class, 'update']);
    Route::delete('/klaim-asuransi/{klaimAsuransi}', [KlaimAnsuransiController::class, 'destroy']);
});
