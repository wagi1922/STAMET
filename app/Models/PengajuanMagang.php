<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class PengajuanMagang extends Model
{
    use HasFactory;

    /**
     * fillable
     * 
     * @var array
    */
    
    public $timestamps = false;
     protected $table = 'pengajuan_magang';
     protected $primaryKey = 'id';

     protected $fillable = [
        'nama_lengkap',
        'asal_instansi',
        'periode_mulai',
        'periode_selesai',
        'path_dokumen',
        'tanggal_pengajuan',
     ];

     protected function pathDokumen(): Attribute
     {
        return Attribute::make(
            get: fn ($path_dokumen) => url ('/storage/PengajuanMagang/' . $path_dokumen),
        );
     }
}
