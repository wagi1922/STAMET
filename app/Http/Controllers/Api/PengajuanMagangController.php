<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\PengajuanMagang;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PengajuanMagangResource;

class PengajuanMagangController extends Controller
{
    /**
     * Menampilkan semua data pengajuan magang.
     */
    public function index()
    {
        $pengajuan = PengajuanMagang::latest('tanggal_pengajuan')->paginate(10);
        return new PengajuanMagangResource(true, 'Daftar Data Pengajuan Magang', $pengajuan);
    }

    /**
     * Menyimpan data pengajuan magang baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_lengkap'    => 'required|string|max:255',
            'asal_instansi'   => 'required|string|max:255',
            'periode_mulai'   => 'required|date',
            'periode_selesai' => 'required|date|after_or_equal:periode_mulai',
            'path_dokumen'    => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // PERUBAHAN DI SINI: Menyimpan file secara eksplisit ke disk 'public'
        $path = $request->file('path_dokumen')->store('PengajuanMagang', 'public');

        $pengajuan = PengajuanMagang::create([
            'nama_lengkap'      => $request->nama_lengkap,
            'asal_instansi'     => $request->asal_instansi,
            'periode_mulai'     => $request->periode_mulai,
            'periode_selesai'   => $request->periode_selesai,
            'path_dokumen'      => basename($path),
            'tanggal_pengajuan' => now(),
        ]);

        return new PengajuanMagangResource(true, 'Data Pengajuan Magang Berhasil Disimpan!', $pengajuan);
    }

    /**
     * Menampilkan satu data pengajuan magang.
     */
    public function show(PengajuanMagang $pengajuanMagang)
    {
        return new PengajuanMagangResource(true, 'Data Pengajuan Magang Ditemukan!', $pengajuanMagang);
    }

    /**
     * Memperbarui data pengajuan magang.
     */
    public function update(Request $request, PengajuanMagang $pengajuanMagang)
    {
        $validator = Validator::make($request->all(), [
            'nama_lengkap'    => 'required|string|max:255',
            'asal_instansi'   => 'required|string|max:255',
            'periode_mulai'   => 'required|date',
            'periode_selesai' => 'required|date|after_or_equal:periode_mulai',
            'path_dokumen'    => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $documentPath = $pengajuanMagang->path_dokumen;

        if ($request->hasFile('path_dokumen')) {
            Storage::disk('public')->delete('PengajuanMagang/'.$pengajuanMagang->getRawOriginal('path_dokumen'));
            
            // PERUBAHAN DI SINI: Menyimpan file baru secara eksplisit ke disk 'public'
            $path = $request->file('path_dokumen')->store('PengajuanMagang', 'public');
            $documentPath = basename($path);
        }

        $pengajuanMagang->update([
            'nama_lengkap'    => $request->nama_lengkap,
            'asal_instansi'   => $request->asal_instansi,
            'periode_mulai'   => $request->periode_mulai,
            'periode_selesai' => $request->periode_selesai,
            'path_dokumen'    => $documentPath,
        ]);

        return new PengajuanMagangResource(true, 'Data Pengajuan Magang Berhasil Diperbarui!', $pengajuanMagang);
    }

    /**
     * Menghapus data pengajuan magang.
     */
    public function destroy(PengajuanMagang $pengajuanMagang)
    {
        // Menghapus file dari disk 'public'
        Storage::disk('public')->delete('PengajuanMagang/'.$pengajuanMagang->getRawOriginal('path_dokumen'));
        $pengajuanMagang->delete();

        return new PengajuanMagangResource(true, 'Data Pengajuan Magang Berhasil Dihapus!', null);
    }
}
