<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_listing_id',
        'cover_letter',
        'resume_path',
        'resume_document_id',
        'status',
        'employer_notes',
        'status_updated_at',
    ];

    protected $casts = [
        'status_updated_at' => 'datetime',
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

    public function statusLogs()
    {
        return $this->hasMany(ApplicationStatusLog::class)->orderBy('created_at');
    }

    // ── Helpers ─────────────────────────────────────────────
    public function statusLabel(): string
    {
        return match($this->status) {
            'pending'   => 'Pending Review',
            'interview' => 'Interview',
            'hired'     => 'Hired',
            'rejected'  => 'Rejected',
            default     => ucfirst($this->status),
        };
    }
}
