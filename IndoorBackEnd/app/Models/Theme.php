<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;

    protected $fillable = [
        'color',
        'button_color',
        'options_ribbon_color',
        'browse_venue_ribbon_color',
        'categories_background_color',
        'browse_venue_text_color', // Include this too
        'map_container_color', // Add this line
        'lots_color',
    ];
}