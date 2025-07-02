<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    protected $fillable = ['kiosk_id', 'store_id', 'timestamp'];

    // Define relationship to kiosk
    public function kiosk()
    {
        return $this->belongsTo(DB::table('kiosks'), 'kiosk_id');
    }

    // Define relationship to store (company)
    public function store()
    {
        return $this->belongsTo(DB::table('companies'), 'store_id');
    }
}