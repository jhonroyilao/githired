<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiJobMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_listing_id',
        'resume_document_id',
        'match_score',
        'score_breakdown',
        'matching_skills',
        'missing_skills',
        'explanation',
        'suggested_action',
        'provider',
        'model',
        'prompt_version',
        'profile_hash',
        'resume_hash',
        'job_hash',
        'generation_status',
        'error_message',
        'generated_at',
    ];

    protected $casts = [
        'match_score' => 'decimal:2',
        'score_breakdown' => 'array',
        'matching_skills' => 'array',
        'missing_skills' => 'array',
        'generated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobListing(): BelongsTo
    {
        return $this->belongsTo(JobListing::class);
    }

    public function resumeDocument(): BelongsTo
    {
        return $this->belongsTo(ResumeDocument::class);
    }
}
