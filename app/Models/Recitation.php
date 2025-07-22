<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recitation extends Model
{
    public $timestamps = false;

    protected $fillable = ['verse_id', 'reciter_id', 'audio_url'];

    public function verse()
    {
        return $this->belongsTo(Verse::class);
    }

    public function reciter()
    {
        return $this->belongsTo(Reciter::class);
    }
}
