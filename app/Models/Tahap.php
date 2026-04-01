<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tahap extends Model
{
    protected $table = 'tahap';

    protected $fillable = [
        'semester_id',
        'kategori_tahap_id',
        'deadline',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function kategoriTahap()
    {
        return $this->belongsTo(KategoriTahap::class);
    }

    public function dokumen()
    {
        return $this->hasMany(Dokumen::class, 'tahap_id');
    }

    public function beritaAcara()
    {
        return $this->hasMany(BeritaAcara::class, 'tahap_id');
    }

    // Accessor untuk backward compatibility
    public function getNamaTahapAttribute()
    {
        return $this->kategoriTahap?->nama_tahap;
    }

    public function getDeskripsiAttribute()
    {
        return $this->kategoriTahap?->deskripsi;
    }

    public function getUrutanAttribute()
    {
        return $this->kategoriTahap?->urutan;
    }

    public function getJenisDokumenAttribute()
    {
        return $this->kategoriTahap?->jenis_dokumen;
    }
}

