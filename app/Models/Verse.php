<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Verse extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'chapter_id',
        'verse_number',
        'verse_key',
        'text_uthmani',
        'text_imlaei',
        'page_number',
        'juz_number',
        'hizb_number',
        'rub_number',
        'sajda',
    ];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class, 'chapter_id');
    }

    public function recitations()
    {
        return $this->hasMany(Recitation::class);
    }

    public function words()
    {
        return $this->hasMany(Word::class);
    }

}
