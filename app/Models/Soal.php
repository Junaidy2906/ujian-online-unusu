<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Soal extends Model
{
    protected $table = 'soal';

    protected $fillable = [
        'ujian_id',
        'nomor',
        'tipe',
        'pertanyaan',
        'poin',
        'jawaban_benar',
        'rubrik_penilaian',
    ];

    public function ujian(): BelongsTo
    {
        return $this->belongsTo(Ujian::class, 'ujian_id');
    }

    public function pilihanJawaban(): HasMany
    {
        return $this->hasMany(PilihanJawaban::class, 'soal_id');
    }
}