<?php

// File: routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ApiKeyMiddleware;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PengajuanMagangController;

/*
|--------------------------------------------------------------------------
| RUTE DEBUG SEMENTARA (DIPERBAIKI)
|--------------------------------------------------------------------------
|
| Rute ini sekarang mencari file dengan key 'path_dokumen'.
|
*/
Route::post('/test-upload', function (Request $request) {
    // PERUBAHAN DI SINI: Mencari file dengan key 'path_dokumen'
    $hasFile = $request->hasFile('path_dokumen');
    $isFileValid = $request->file('path_dokumen') ? $request->file('path_dokumen')->isValid() : false;

    return response()->json([
        'Pesan' => 'Hasil Debug Upload (Diperbaiki)',
        'Key yang dicari' => 'path_dokumen',
        'Apakah ada file?' => $hasFile,
        'Apakah file valid?' => $isFileValid,
        'Detail File' => $request->file('path_dokumen'),
        'Semua Input' => $request->all()
    ]);
});


/*
|--------------------------------------------------------------------------
| RUTE APLIKASI ANDA
|--------------------------------------------------------------------------
*/

Route::middleware([ApiKeyMiddleware::class])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/pengajuan-magang', [PengajuanMagangController::class, 'store']);
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
});
