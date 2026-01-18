<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVerseNote extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'verse_id', 'note'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verse()
    {
        return $this->belongsTo(Verse::class);
    }
}
