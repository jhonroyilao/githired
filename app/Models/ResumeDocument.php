<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResumeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'extracted_text',
        'content_hash',
        'extraction_status',
        'extraction_error',
        'is_current',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'file_size' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_current', true);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function aiJobMatches(): HasMany
    {
        return $this->hasMany(AiJobMatch::class);
    }
}
