<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = [
        'translation_key_id',
        'locale_id',
        'content',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function translationKey()
    {
        return $this->belongsTo(TranslationKey::class);
    }

    public function locale()
    {
        return $this->belongsTo(Locale::class);
    }
}
