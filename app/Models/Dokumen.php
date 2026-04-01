<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    protected $table = 'dokumen';

    protected $fillable = [
        'mata_kuliah_id',
        'tahap_id',
        'nama_file',
        'jenis_dokumen',
        'file_path',
        'uploaded_by',
        'status',
        'komentar',
        'parent_id',
        'is_current',
    ];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    public function tahap()
    {
        return $this->belongsTo(Tahap::class, 'tahap_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Parent dokumen (dokumen yang di-revisi)
    public function parent()
    {
        return $this->belongsTo(Dokumen::class, 'parent_id');
    }

    // Child dokumen (revisi dari dokumen ini)
    public function revisions()
    {
        return $this->hasMany(Dokumen::class, 'parent_id');
    }

    // Scope untuk dokumen yang aktif/current
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    // Scope untuk dokumen dalam kategori (mata_kuliah + tahap + jenis)
    public function scopeForCategory($query, $mkId, $tahapId, $jenis)
    {
        return $query->where('mata_kuliah_id', $mkId)
                     ->where('tahap_id', $tahapId)
                     ->where('jenis_dokumen', $jenis);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'success',
            'revisi'   => 'warning',
            default    => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'Approved',
            'revisi'   => 'Perlu Revisi',
            default    => 'Pending',
        };
    }
}
