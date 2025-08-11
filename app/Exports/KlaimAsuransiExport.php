<?php

namespace App\Exports;

use App\Models\KlaimAsuransi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KlaimAsuransiExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Mengambil semua data yang dibutuhkan
        return KlaimAsuransi::select(
            'id_permohonan', 
            'nama_perusahaan', 
            'nomor_whatsapp', 
            'tanggal_surat_permohonan',
            'path_berkas_surat',
            'jumlah_kejadian_input',
            'tanggal_pengajuan_form',
            'status_permohonan',
            'catatan_admin' 
        )->get();
    }

    /**
     * Mendefinisikan judul untuk setiap kolom.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nama Perusahaan',
            'Nomor WhatsApp',
            'Tanggal Surat Permohonan',
            'Nama Berkas Surat',
            'Jumlah Kejadian',
            'Tanggal Pengajuan Form',
            'Status Permohonan',
            'Catatan Admin',
        ];
    }
}
