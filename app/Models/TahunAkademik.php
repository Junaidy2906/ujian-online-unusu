<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAkademik extends Model
{
    protected $table = 'tahun_akademik';

    protected $fillable = [
        'nama',
        'kode',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class, 'tahun_akademik_id');
    }

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class, 'tahun_akademik_id');
    }

    public function ujian(): HasMany
    {
        return $this->hasMany(Ujian::class, 'tahun_akademik_id');
    }
}