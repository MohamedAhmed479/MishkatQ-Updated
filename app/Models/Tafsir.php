<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tafsir extends Model
{
    public $timestamps = false;

    protected $fillable = ['name'];

    public function preferences()
    {
        return $this->hasMany(UserPreference::class, 'tafsir_id', "id");
    }

}
