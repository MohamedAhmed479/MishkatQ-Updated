<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recitation extends Model
{
    public $timestamps = false;

    protected $fillable = ['verse_id', 'reciter_id', 'audio_url'];

    /**
     * Base URL for audio files from verses.quran.foundation
     */
    const AUDIO_BASE_URL = 'https://verses.quran.foundation/';

    /**
     * Get the full audio URL
     */
    public function getFullAudioUrlAttribute(): ?string
    {
        if (!$this->audio_url) {
            return null;
        }

        // If already a full URL, return as is
        if (str_starts_with($this->audio_url, 'http://') || str_starts_with($this->audio_url, 'https://')) {
            return $this->audio_url;
        }

        return self::AUDIO_BASE_URL . $this->audio_url;
    }

    public function verse()
    {
        return $this->belongsTo(Verse::class);
    }

    public function reciter()
    {
        return $this->belongsTo(Reciter::class);
    }
}
