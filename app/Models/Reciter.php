<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reciter extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id',
        'reciter_name',
    ];

    public function recitations() {
        return $this->hasMany(Recitation::class);
    }

}
