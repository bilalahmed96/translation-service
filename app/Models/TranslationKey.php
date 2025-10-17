<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranslationKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'tkey',
        'namespace',
    ];

    public function translations()
    {
        return $this->hasMany(Translation::class, 'translation_key_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'translation_key_tag');
    }
}
