<?php

namespace App\Http\Controllers\Api;

use App\Models\Berita;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BeritaResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BeritaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $berita = Berita::latest('tanggal_publish')->paginate(10);
        return new BeritaResource(true, 'Daftar Berita', $berita);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul'           => 'required|string|max:255',
            'isi_lengkap'     => 'required|string',
            'gambar_unggulan' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'penulis'         => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Simpan gambar ke disk 'public' di dalam folder 'Berita'
        $gambarPath = $request->file('gambar_unggulan')->store('Berita', 'public');

        $berita = Berita::create([
            'judul'           => $request->judul,
            'isi_lengkap'     => $request->isi_lengkap,
            'gambar_unggulan' => basename($gambarPath), // Simpan hanya nama filenya
            'tanggal_publish' => now(),
            'penulis'         => $request->penulis,
        ]);

        return new BeritaResource(true, 'Berita Berhasil Disimpan!', $berita);
    }

    /**
     * Display the specified resource.
     */
    public function show(Berita $berita)
    {
        return new BeritaResource(true, 'Berita Ditemukan!', $berita);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Berita $berita)
    {
        $validator = Validator::make($request->all(), [
            'judul'           => 'sometimes|required|string|max:255',
            'isi_lengkap'     => 'sometimes|required|string',
            'gambar_unggulan' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'penulis'         => 'sometimes|required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Ambil data yang akan diupdate
        $dataToUpdate = $request->only(['judul', 'isi_lengkap', 'penulis']);

        // Cek jika ada file gambar baru yang di-upload
        if ($request->hasFile('gambar_unggulan')) {
            // Hapus gambar lama
            Storage::disk('public')->delete('Berita/' . $berita->getRawOriginal('gambar_unggulan'));

            // Simpan gambar baru
            $gambarPath = $request->file('gambar_unggulan')->store('Berita', 'public');
            
            // PERBAIKAN: Tambahkan nama file gambar baru ke data yang akan diupdate
            $dataToUpdate['gambar_unggulan'] = basename($gambarPath);
        }

        // Lakukan update dengan semua data yang sudah disiapkan
        $berita->update($dataToUpdate);

        return new BeritaResource(true, 'Berita Berhasil Diperbarui!', $berita);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Berita $berita)
    {
        // Gunakan getRawOriginal untuk memastikan kita mendapatkan nama file mentah
        Storage::disk('public')->delete('Berita/' . $berita->getRawOriginal('gambar_unggulan'));
        $berita->delete();

        return new BeritaResource(true, 'Berita Berhasil Dihapus!', null);
    }
}
