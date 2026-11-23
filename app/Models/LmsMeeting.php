<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LmsMeeting extends Model
{
    protected $fillable = [
        'mata_kuliah_id',
        'semester_id',
        'meeting_number',
    ];

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(LmsMaterial::class, 'lms_meeting_id');
    }
}
