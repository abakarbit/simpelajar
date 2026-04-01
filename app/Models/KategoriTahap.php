<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriTahap extends Model
{
    protected $table = 'kategori_tahap';

    protected $fillable = [
        'nama_tahap',
        'deskripsi',
        'urutan',
        'jenis_dokumen',
    ];

    protected $casts = [
        'jenis_dokumen' => 'array',
    ];

    public function tahap()
    {
        return $this->hasMany(Tahap::class);
    }
}
