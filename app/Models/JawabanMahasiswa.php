<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JawabanMahasiswa extends Model
{
    protected $table = 'jawaban_mahasiswa';

    protected $fillable = [
        'percobaan_ujian_id',
        'soal_id',
        'pilihan_jawaban_id',
        'jawaban_text',
        'is_benar',
        'nilai',
    ];

    protected function casts(): array
    {
        return [
            'is_benar' => 'boolean',
        ];
    }

    public function percobaanUjian(): BelongsTo
    {
        return $this->belongsTo(PercobaanUjian::class, 'percobaan_ujian_id');
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(Soal::class, 'soal_id');
    }

    public function pilihanJawaban(): BelongsTo
    {
        return $this->belongsTo(PilihanJawaban::class, 'pilihan_jawaban_id');
    }
}