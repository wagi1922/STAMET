<?php

namespace App\Exports;

use App\Models\PermintaanDataCuaca;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PermintaanDataCuacaExport implements FromCollection, WithHeadings, ShouldAutoSize
{
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
        )-> get();
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
