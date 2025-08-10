<?php

// File: routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ApiKeyMiddleware;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BeritaController;
use App\Http\Controllers\Api\PengajuanMagangController;


Route::middleware([ApiKeyMiddleware::class])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::post('/pengajuan-magang', [PengajuanMagangController::class, 'store']);
    
    Route::get('/berita', [BeritaController::class, 'index']);
    Route::get('/berita/{berita}', [BeritaController::class, 'show']);
});

Route::middleware(['auth:sanctum', ApiKeyMiddleware::class])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/pengajuan-magang', [PengajuanMagangController::class, 'index']);
    Route::get('/pengajuan-magang/{pengajuanMagang}', [PengajuanMagangController::class, 'show']);
    Route::put('/pengajuan-magang/{pengajuanMagang}', [PengajuanMagangController::class, 'update']);
    Route::delete('/pengajuan-magang/{pengajuanMagang}', [PengajuanMagangController::class, 'destroy']);

    Route::post('/berita', [BeritaController::class, 'store']);
    Route::put('/berita/{berita}', [BeritaController::class, 'update']);
    Route::delete('/berita/{berita}', [BeritaController::class, 'destroy']);
});
