<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiaryEntry extends Model
{
    protected $fillable = [
        'title',
        'description',
        'rating',
        'uploaded_file'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
