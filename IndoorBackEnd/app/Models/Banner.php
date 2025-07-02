<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    // Explicitly define the table name if it's different from the plural form of the class name
    protected $table = 'banners';

    protected $fillable = ['image'];
}