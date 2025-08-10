<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    use HasFactory;
    
    public $timestamps = false;
    protected $table = 'berita';
    protected $primaryKey = 'id';

    protected $fillable = [
        'judul',
        'isi_lengkap',
        'gambar_unggulan',
        'tanggal_publish',
        'penulis',
    ];

    /**
     * Accessor untuk kolom 'gambar_unggulan'.
     * Nama fungsi diubah menjadi 'gambarUnggulan'.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function gambarUnggulan(): Attribute
    {
        return Attribute::make(
            // Variabel di dalam fn() bisa apa saja, tapi lebih baik konsisten
            get: fn ($gambar_unggulan) => $gambar_unggulan ? url(path: '/storage/Berita/' . $gambar_unggulan) : null,
        );
    }
}
