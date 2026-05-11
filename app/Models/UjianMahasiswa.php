<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UjianMahasiswa extends Model
{
    protected $table = 'ujian_mahasiswa';

    protected $fillable = [
        'ujian_id',
        'mahasiswa_id',
        'kode_soal',
        'tambahan_percobaan',
    ];

    protected function casts(): array
    {
        return [
            'tambahan_percobaan' => 'integer',
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
}
