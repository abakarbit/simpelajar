<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function isDosen(): bool
    {
        return $this->role === 'dosen';
    }

    public function isGkmp(): bool
    {
        return $this->role === 'gkmp';
    }

    public function mataKuliah()
    {
        return $this->belongsToMany(MataKuliah::class, 'dosen_mata_kuliah', 'dosen_id', 'mata_kuliah_id')
                    ->withPivot('lokasi', 'semester_id')
                    ->withTimestamps();
    }

    public function dokumen()
    {
        return $this->hasMany(Dokumen::class, 'uploaded_by');
    }
}
