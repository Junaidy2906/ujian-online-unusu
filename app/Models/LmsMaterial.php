<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsMaterial extends Model
{
    public const TYPE_BAHAN_AJAR = 'bahan_ajar';
    public const TYPE_KUIS = 'kuis';
    public const TYPE_PENUNJUK_PENUGASAN = 'penunjuk_penugasan';
    public const TYPE_CONTOH_PEMBELAJARAN = 'contoh_pembelajaran';

    protected $fillable = [
        'mata_kuliah_id',
        'dosen_id',
        'lms_meeting_id',
        'week_number',
        'title',
        'type',
        'access_code',
        'description',
        'file_path',
        'file_name',
        'file_mime',
        'resource_url',
    ];

    protected function casts(): array
    {
        return [
            'week_number' => 'integer',
        ];
    }

    public static function typeOptions(): array
    {
        return [
            self::TYPE_BAHAN_AJAR => 'Bahan Ajar',
            self::TYPE_KUIS => 'Kuis',
            self::TYPE_PENUNJUK_PENUGASAN => 'Penunjuk Penugasan',
            self::TYPE_CONTOH_PEMBELAJARAN => 'Contoh Pembelajaran',
        ];
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(LmsMeeting::class, 'lms_meeting_id');
    }
}
