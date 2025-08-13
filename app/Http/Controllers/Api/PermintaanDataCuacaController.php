<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\PermintaanDataCuaca;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PermintaanDataCuacaResource;

class PermintaanDataCuacaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permintaanDataCuaca = PermintaanDataCuaca::latest( 'tanggal_submit_form')->paginate(10);
        return new PermintaanDataCuacaResource(true, 'Daftar Permintaan Data Cuaca!',  $permintaanDataCuaca);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_pemohon' => 'required|string|max:255',
            'nomor_whatsapp' => 'required|string|max:20',
            'tanggal_surat_resmi' => 'required|date',
            'jenis_data' => 'required|string|max:255',
            'tipe_data_periode' => 'required|string|max:255',
            'durasi_periode' => 'required',
            'path_berkas_permohonan' => 'file|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $path = $request->file('path_berkas_permohonan')->store('PermintaanDataCuaca', 'public');

        $permintaan = PermintaanDataCuaca::create([
            'nama_pemohon' => $request->nama_pemohon,
            'nomor_whatsapp' => $request->nomor_whatsapp,
            'tanggal_surat_resmi' => $request->tanggal_surat_resmi,
            'jenis_data' => $request->jenis_data,
            'tipe_data_periode' => $request->tipe_data_periode,
            'durasi_periode' => $request->durasi_periode,
            'note_permintaan' => $request->note_permohonan,
            'path_berkas_permohonan' => basename($path),
            'tanggal_submit_form' => now(),
            'status_permohonan' => 'Baru Diajukan',
            'catatan_admin' => $request->catatan_admin,
        ]);

        return new PermintaanDataCuacaResource(true, 'Permintaan Data Cuaca Berhasil Disimpan!', $permintaan);

    }

    /**
     * Display the specified resource.
     */
    public function show(PermintaanDataCuaca $permintaanDataCuaca)
    {
        return new PermintaanDataCuacaResource(true, 'Data Permintaan Data Cuaca Ditemukan!', $permintaanDataCuaca);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PermintaanDataCuaca $permintaanDataCuaca)
    {
        $validator = Validator::make($request->all(), [
            'nama_pemohon' => 'sometimes|required|string|max:255',
            'nomor_whatsapp' => 'sometimes|required|string|max:20',
            'tanggal_surat_resmi' => 'sometimes|required|date',
            'jenis_data' => 'sometimes|required|string|max:255',
            'tipe_data_periode' => 'sometimes|required|string|max:255',
            'durasi_periode' => 'sometimes|required|integer',
            'note_permohonan' => 'sometimes|nullable|string|max:255',
            'path_berkas_permohonan' => 'sometimes|required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->hasFile('path_berkas_permohonan')) {
            $path = $request->file('path_berkas_permohonan')->store('PermintaanDataCuaca', 'public');
            $permintaanDataCuaca->path_berkas_permohonan = basename($path);
        }
        
        $permintaanDataCuaca->update($request->only([
            'nama_pemohon',
            'nomor_whatsapp',
            'tanggal_surat_resmi',
            'jenis_data',
            'tipe_data_periode',
            'durasi_periode',
            'note_permohonan',
            'catatan_admin',
        ]));

        
        return new PermintaanDataCuacaResource(true, 'Data Permintaan Data Cuaca Berhasil Diperbarui!', $permintaanDataCuaca);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PermintaanDataCuaca $permintaanDataCuaca)
    {
        // Hapus berkas surat jika ada
        if ($permintaanDataCuaca->path_berkas_surat) {
            Storage::disk('public')->delete('PermintaanDataCuaca/' . $klaimAsuransi->path_berkas_surat);
        }

        $permintaanDataCuaca->delete();

        return new PermintaanDataCuacaResource(true, 'Data Klaim Asuransi Berhasil Dihapus!', null); 
    }
}
