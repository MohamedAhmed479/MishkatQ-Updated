<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name_ar', 'name_en', 'revelation_place', 'revelation_order', 'verses_count',
    ];

    public function verses()
    {
        return $this->hasMany(Verse::class, 'chapter_id');
    }
}
