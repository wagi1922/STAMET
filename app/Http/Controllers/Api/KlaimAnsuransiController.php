<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\KlaimAsuransi;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Exports\KlaimAsuransiDetailExport;
use App\Exports\KlaimAsuransiExport;
use App\Http\Resources\KlaimAsuransiResource;


class KlaimAnsuransiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $klaim = KlaimAsuransi::latest('tanggal_pengajuan_form')->paginate(10);
        return new KlaimAsuransiResource(true, 'Daftar Data Klaim Asuransi', $klaim);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_perusahaan'          => 'required|string|max:255',
            'nomor_whatsapp'           => 'required|string|max:20',
            'tanggal_surat_permohonan' => 'required|date',
            'path_berkas_surat'        => 'file|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $path = $request->file('path_berkas_surat')->store('KlaimAsuransi', 'public');

        $klaim = KlaimAsuransi::create([
            'nama_perusahaan'          => $request->nama_perusahaan,
            'nomor_whatsapp'           => $request->nomor_whatsapp,
            'tanggal_surat_permohonan' => $request->tanggal_surat_permohonan,
            'path_berkas_surat'        => basename($path),
            'jumlah_kejadian_input'    => $request->jumlah_kejadian_input,
            'tanggal_pengajuan_form'   => now(),
            'status_permohonan'        => 'Baru Diajukan',
        ]);

        return new KlaimAsuransiResource(true, 'Data Klaim Asuransi Berhasil Disimpan!', $klaim);
    }

    /**
     * Display the specified resource.
     */
    public function show(klaimAsuransi $klaimAsuransi)
    {
        return new KlaimAsuransiResource(true, 'Data Klaim Asuransi Ditemukan!', $klaimAsuransi);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, klaimAsuransi $klaimAsuransi)
    {
        $validator = Validator::make($request->all(), [
            'nama_perusahaan'          => 'sometimes|required|string|max:255',
            'nomor_whatsapp'           => 'sometimes|required|string|max:20',
            'tanggal_surat_permohonan' => 'sometimes|required|date',
            'path_berkas_surat'        => 'sometimes|required|file|mimes:pdf,doc,docx|max:2048',
            'jumlah_kejadian_input'    => 'sometimes|required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->hasFile('path_berkas_surat')) {
            $path = $request->file('path_berkas_surat')->store('KlaimAsuransi', 'public');
            $klaimAsuransi->path_berkas_surat = basename($path);
        }

        $klaimAsuransi->update($request->only([
            'nama_perusahaan',
            'nomor_whatsapp',
            'tanggal_surat_permohonan',
            'jumlah_kejadian_input',
        ]));

        $klaimAsuransi->save();

        return new KlaimAsuransiResource(true, 'Data Klaim Asuransi Berhasil Diperbarui!', $klaimAsuransi);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(klaimAsuransi $klaimAsuransi)
    {
        // Hapus berkas surat jika ada
        if ($klaimAsuransi->path_berkas_surat) {
            Storage::disk('public')->delete('KlaimAsuransi/' . $klaimAsuransi->path_berkas_surat);
        }

        $klaimAsuransi->delete();

        return new KlaimAsuransiResource(true, 'Data Klaim Asuransi Berhasil Dihapus!', null); 
    }

    public function export()
    {
        // Membuat nama file dengan tanggal saat ini
        $fileName = 'klaim_asuransi_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        // Memulai download file Excel
        return Excel::download(new KlaimAsuransiExport, $fileName);
    }

    public function exportDetail(KlaimAsuransi $klaimAsuransi)
    {
        $fileName = 'detail_klaim_' . $klaimAsuransi->nama_perusahaan . '_' . now()->format('Y-m-d') . '.xlsx';
        
        // Menggunakan Export Class yang baru untuk satu data
        return Excel::download(new KlaimAsuransiDetailExport($klaimAsuransi), $fileName);
    }
}
