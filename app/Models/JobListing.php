<?php

namespace App\Models;

use App\Enums\JobStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobListing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_id',
        'category_id',
        'title',
        'slug',
        'location',
        'location_type',
        'type',
        'experience_level',
        'description',
        'requirements',
        'skills_required',
        'salary_min',
        'salary_max',
        'salary_currency',
        'status',
        'rejection_reason',
        'submitted_at',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'closed_at',
        'published_at',
        'expires_at',
    ];

    protected $casts = [
        'status'         => JobStatus::class,
        'skills_required' => 'array',
        'submitted_at'   => 'datetime',
        'approved_at'    => 'datetime',
        'rejected_at'    => 'datetime',
        'closed_at'      => 'datetime',
        'published_at'   => 'datetime',
        'expires_at'     => 'datetime',
        'salary_min'     => 'decimal:2',
        'salary_max'     => 'decimal:2',
    ];

    // ── Relationships ────────────────────────────────────────────────────

    /** The employer who posted this listing. */
    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function savedBy(): HasMany
    {
        return $this->hasMany(SavedJob::class);
    }

    // ── Scopes ───────────────────────────────────────────────────────────

    /**
     * Jobs that applicants may browse and apply to.
     * Requires status = active and not soft-deleted.
     */
    public function scopePubliclyVisible(Builder $query): void
    {
        $query->where('status', JobStatus::Active);
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    public function isPubliclyVisible(): bool
    {
        return $this->status === JobStatus::Active && ! $this->trashed();
    }

    /**
     * Formatted salary range, e.g. "PHP 40,000 – 80,000".
     */
    public function salaryRange(): ?string
    {
        if (! $this->salary_min && ! $this->salary_max) {
            return null;
        }

        $currency = $this->salary_currency ?? 'PHP';

        if ($this->salary_min && $this->salary_max) {
            return $currency.' '.number_format($this->salary_min).' – '.number_format($this->salary_max);
        }

        return $currency.' '.number_format($this->salary_min ?? $this->salary_max);
    }
}