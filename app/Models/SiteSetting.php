<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'app_name',
        'logo_path',
        'campus_image_path',
    ];
}
