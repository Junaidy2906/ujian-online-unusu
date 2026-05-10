<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ujian extends Model
{
    protected $table = 'ujian';

    protected $fillable = [
        'tahun_akademik_id',
        'semester_id',
        'kelas_id',
        'mata_kuliah_id',
        'dosen_id',
        'nama_ujian',
        'deskripsi',
        'jadwal_mulai',
        'jadwal_selesai',
        'durasi_menit',
        'nilai_minimum_lulus',
        'maksimal_percobaan',
        'bobot_pg',
        'bobot_essay',
        'status',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'jadwal_mulai' => 'datetime',
            'jadwal_selesai' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    public function soal(): HasMany
    {
        return $this->hasMany(Soal::class, 'ujian_id');
    }
}