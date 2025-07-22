<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Juz extends Model
{
    public $timestamps = false;

    protected $fillable = ['start_verse_id', 'end_verse_id', 'juz_number', 'verses_count'];

    public function startVerse()
    {
        return $this->belongsTo(Verse::class, 'start_verse_id');
    }

    public function endVerse()
    {
        return $this->belongsTo(Verse::class, 'end_verse_id');
    }
}
