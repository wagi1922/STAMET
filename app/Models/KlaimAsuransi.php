<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class KlaimAsuransi extends Model
{
    use HasFactory;

    /**
     * fillable
     * 
     * @var array
    */
    
    public $timestamps = false;
     protected $table = 'permohonan_klaim';
     protected $primaryKey = 'id_permohonan';

     protected $fillable = [
        'nama_perusahaan',
        'nomor_whatsapp',
        'tanggal_surat_permohonan',
        'path_berkas_surat',
        'jumlah_kejadian_input',
        'tanggal_pengajuan_form',
        'status_permohonan',
     ];

     protected function pathBerkasSurat(): Attribute
     {
        return Attribute::make(
            get: fn ($path_berkas_surat) => url ('/storage/KlaimAsuransi/' . $path_berkas_surat),
        );
     }
}
