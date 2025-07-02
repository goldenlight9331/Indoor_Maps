<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MiscSetting extends Model
{
    protected $table = 'misc_settings';
    protected $fillable = ['screensaver_time', 'city', 'state', 'log_interval', 'day_image', 'night_image'];
}