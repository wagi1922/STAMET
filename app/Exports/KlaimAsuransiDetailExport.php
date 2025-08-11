<?php

namespace App\Exports;

use App\Models\KlaimAsuransi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class KlaimAsuransiDetailExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $klaim;

    public function __construct(KlaimAsuransi $klaim)
    {
        $this->klaim = $klaim;
    }

    /**
    * Mengubah satu model menjadi koleksi agar bisa diproses.
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Mengubah satu data menjadi format collection yang berisi array
        return new Collection([
            [
                'id'                        => $this->klaim->id,
                'nama_perusahaan'           => $this->klaim->nama_perusahaan,
                'nomor_whatsapp'            => $this->klaim->nomor_whatsapp,
                'tanggal_surat_permohonan'  => $this->klaim->tanggal_surat_permohonan,
                'path_berkas_surat'         => $this->klaim->getRawOriginal('path_berkas_surat'),
                'jumlah_kejadian_input'     => $this->klaim->jumlah_kejadian_input,
                'tanggal_pengajuan_form'    => $this->klaim->tanggal_pengajuan_form,
                'status_permohonan'         => $this->klaim->status_permohonan,
                'catatan_admin'            => $this->klaim->catatan_admin ?? 'Tidak ada catatan',
            ]
        ]);
    }

    /**
     * Mendefinisikan judul untuk setiap kolom.
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
