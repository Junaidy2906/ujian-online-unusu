<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';

    protected $fillable = [
        'user_id',
        'nim',
        'prodi',
        'semester',
        'angkatan',
        'telepon',
        'alamat',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(Kelas::class, 'kelas_mahasiswa', 'mahasiswa_id', 'kelas_id');
    }

    public function percobaanUjian(): HasMany
    {
        return $this->hasMany(PercobaanUjian::class, 'mahasiswa_id');
    }

    public function nilaiUjian(): HasMany
    {
        return $this->hasMany(NilaiUjian::class, 'mahasiswa_id');
    }

    public function aksesUjian(): HasMany
    {
        return $this->hasMany(UjianMahasiswa::class, 'mahasiswa_id');
    }
}
