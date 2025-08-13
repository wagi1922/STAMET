<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class PermintaanDataCuaca extends Model
{
    use HasFactory;

    /**
     * fillable
     * 
     * @var array
    */
    
     public $timestamps = false;
     protected $table = 'permintaan_data_cuaca';
     protected $primaryKey = 'id_permintaan_data';

     protected $fillable = [
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
        'catatan_admin',
     ];

     protected function pathBerkasPermohonan(): Attribute
     {
        return Attribute::make(
            get: fn ($path_berkas_permohonan) => url ('/storage/PermintaanDataCuaca/' . $path_berkas_permohonan),
        );
     }
}
