<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    public $timestamps = false;

    protected $fillable = ['verse_id', 'position', 'text', 'audio_url'];

    public function verse()
    {
        return $this->belongsTo(Verse::class);
    }
}
