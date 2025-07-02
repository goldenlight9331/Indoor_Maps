<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class forntEnd extends Model
{
    protected $fillable = ['home', 'navigation', 'more'];

    use HasFactory;
}