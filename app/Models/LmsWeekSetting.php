<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsWeekSetting extends Model
{
    protected $fillable = [
        'week_1_start_date',
        'meeting_day',
    ];

    protected function casts(): array
    {
        return [
            'week_1_start_date' => 'date',
            'meeting_day' => 'integer',
        ];
    }
}
