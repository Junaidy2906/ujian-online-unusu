<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dosen extends Model
{
    protected $table = 'dosen';

    protected $fillable = [
        'user_id',
        'nidn',
        'gelar_depan',
        'gelar_belakang',
        'telepon',
        'alamat',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mataKuliah(): HasMany
    {
        return $this->hasMany(MataKuliah::class, 'dosen_id');
    }

    public function kelasWali(): HasMany
    {
        return $this->hasMany(Kelas::class, 'dosen_wali_id');
    }

    public function ujian(): HasMany
    {
        return $this->hasMany(Ujian::class, 'dosen_id');
    }
}
