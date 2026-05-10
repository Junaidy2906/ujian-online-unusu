<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiUjian extends Model
{
    protected $table = 'nilai_ujian';

    protected $fillable = [
        'percobaan_ujian_id',
        'ujian_id',
        'mahasiswa_id',
        'dosen_id',
        'nilai_pg',
        'nilai_essay',
        'nilai_akhir',
        'status_penilaian',
        'status_lulus',
        'catatan',
        'dinilai_at',
    ];

    protected function casts(): array
    {
        return [
            'status_lulus' => 'boolean',
            'dinilai_at' => 'datetime',
        ];
    }

    public function percobaanUjian(): BelongsTo
    {
        return $this->belongsTo(PercobaanUjian::class, 'percobaan_ujian_id');
    }

    public function ujian(): BelongsTo
    {
        return $this->belongsTo(Ujian::class, 'ujian_id');
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }
}