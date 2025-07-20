<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $guarded = [
        "id"
    ];

    protected static function booted()
    {
        static::creating(function ($device) {
            $exists = self::where('user_id', $device->user_id)
                ->where('device_type', $device->device_type)
                ->where('device_name', $device->device_name)
                ->where('platform', $device->platform)
                ->where('browser', $device->browser)
                ->exists();

            if ($exists) {
                throw new \Exception('This device already exists for this user.');
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function token()
    {
        return $this->belongsTo(PersonalAccessToken::class, 'token_id');
    }
}
