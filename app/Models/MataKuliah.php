<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    protected $table = 'mata_kuliah';

    protected $fillable = [
        'nama_mk',
        'kode_mk',
    ];


    public function dokumen()
    {
        return $this->hasMany(Dokumen::class, 'mata_kuliah_id');
    }

    // Current/active dokumen only
    public function dokumenCurrent()
    {
        return $this->hasMany(Dokumen::class, 'mata_kuliah_id')
                    ->where('is_current', true);
    }

    public function dosen()
    {
        return $this->belongsToMany(User::class, 'dosen_mata_kuliah', 'mata_kuliah_id', 'dosen_id')
                    ->withPivot('lokasi', 'semester_id', 'status_dosen');
    }

    public function progressTahap(int $tahapId): int
    {
        $requiredDocs = Tahap::find($tahapId)?->jenis_dokumen ?? [];
        if (empty($requiredDocs)) {
            return 0;
        }
        $uploaded = $this->dokumen()
            ->where('tahap_id', $tahapId)
            ->where('is_current', true)
            ->where('status', 'approved')
            ->count();
        return (int) round(($uploaded / count($requiredDocs)) * 100);
    }

    public function overallProgress(): int
    {
        $tahapList = Tahap::all();
        $totalRequired = 0;
        $totalApproved = 0;

        foreach ($tahapList as $tahap) {
            $required = count($tahap->jenis_dokumen ?? []);
            $totalRequired += $required;

            $approved = $this->dokumen()
                ->where('tahap_id', $tahap->id)
                ->where('is_current', true)
                ->where('status', 'approved')
                ->count();
            $totalApproved += $approved;
        }

        if ($totalRequired === 0) {
            return 0;
        }

        return (int) round(($totalApproved / $totalRequired) * 100);
    }
}
