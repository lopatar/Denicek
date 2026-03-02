<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UploadedFile extends Model
{
    protected $fillable = [
        'file_path',
    ];

    public function entry(): BelongsTo
    {
        return $this->belongsTo(DiaryEntry::class, 'diary_entry_id');
    }
}
