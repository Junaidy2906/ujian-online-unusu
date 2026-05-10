<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PilihanJawaban extends Model
{
    protected $table = 'pilihan_jawaban';

    protected $fillable = [
        'soal_id',
        'kode',
        'jawaban',
        'is_benar',
    ];

    protected function casts(): array
    {
        return [
            'is_benar' => 'boolean',
        ];
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(Soal::class, 'soal_id');
    }
}