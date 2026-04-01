<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $table = 'semester';

    protected $fillable = [
        'nama_semester',
        'tahun_ajaran',
        'tipe',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function mataKuliah()
    {
        return $this->hasMany(MataKuliah::class, 'semester_id');
    }

    public static function getActive(): ?self
    {
        return static::where('is_active', true)->first();
    }

    public function getLabelAttribute(): string
    {
        return "{$this->nama_semester} - {$this->tahun_ajaran}";
    }
}
