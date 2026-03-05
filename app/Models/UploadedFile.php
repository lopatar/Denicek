<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UploadedFile extends Model
{
    protected $fillable = [
        'file_path',
    ];

    public function diary_entry(): BelongsTo
    {
        return $this->belongsTo(DiaryEntry::class);
    }
}
