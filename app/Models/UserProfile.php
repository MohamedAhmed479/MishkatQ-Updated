<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'username',
        'profile_image',
        'verses_memorized_count',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
