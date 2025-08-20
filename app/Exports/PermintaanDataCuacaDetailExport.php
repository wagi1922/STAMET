<?php

namespace App\Exports;

use App\Models\PermintaanDataCuaca;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PermintaanDataCuacaDetailExport implements FromCollection, WithHeadings, ShouldAutoSize
{   
    protected $permintaan;
    
    public function __construct(PermintaanDataCuaca $permintaan)
    {
        $this->permintaan = $permintaan;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return PermintaanDataCuaca::select(
            'id_permintaan_data',
            'nama_pemohon',
            'nomor_whatsapp',
            'tanggal_surat_resmi',
            'jenis_data',
            'tipe_data_periode',
            'durasi_periode',
            'note_permintaan',
            'path_berkas_permohonan',
            'tanggal_submit_form',
            'status_permintaan',
            'catatan_admin'
        )->where('id_permintaan_data', $this->permintaan->id_permintaan_data)->get();
        // Mengambil data berdasarkan ID permintaan yang diberikan
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Pemohon',
            'Nomor WhatsApp',
            'Tanggal Surat Resmi',
            'Jenis Data',
            'Tipe Data Periode',
            'Durasi Periode',
            'Note Permintaan',
            'Path Berkas Permohonan',
            'Tanggal Submit Form',
            'Status Permintaan',
            'Catatan Admin',
        ];
    }
}
