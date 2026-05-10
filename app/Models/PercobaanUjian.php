<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PercobaanUjian extends Model
{
    protected $table = 'percobaan_ujian';

    protected $fillable = [
        'ujian_id',
        'mahasiswa_id',
        'percobaan_ke',
        'mulai_at',
        'selesai_at',
        'status',
        'nilai_pg',
        'nilai_essay',
        'nilai_akhir',
        'is_lulus',
    ];

    protected function casts(): array
    {
        return [
            'mulai_at' => 'datetime',
            'selesai_at' => 'datetime',
            'is_lulus' => 'boolean',
        ];
    }

    public function ujian(): BelongsTo
    {
        return $this->belongsTo(Ujian::class, 'ujian_id');
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function jawaban(): HasMany
    {
        return $this->hasMany(JawabanMahasiswa::class, 'percobaan_ujian_id');
    }

    public function nilai(): HasMany
    {
        return $this->hasMany(NilaiUjian::class, 'percobaan_ujian_id');
    }
}