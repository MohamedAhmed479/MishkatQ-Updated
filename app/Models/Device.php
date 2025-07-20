<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $guarded = [
        "id"
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function token()
    {
        return $this->belongsTo(PersonalAccessToken::class, 'token_id');
    }
}
