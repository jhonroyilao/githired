<?php

namespace App\Models;

use App\Enums\JobStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'views_count',
        'deleted_by',
        'delete_reason',
    ];

    protected $casts = [
        'skills_required' => 'array',
        'salary_min'       => 'decimal:2',
        'salary_max'       => 'decimal:2',
        'submitted_at'     => 'datetime',
        'approved_at'      => 'datetime',
        'rejected_at'      => 'datetime',
        'closed_at'        => 'datetime',
        'published_at'     => 'datetime',
        'expires_at'       => 'datetime',
        'deleted_at'       => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class, 'category_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function savedJobs(): HasMany
    {
        return $this->hasMany(SavedJob::class);
    }

    public function aiJobMatches(): HasMany
    {
        return $this->hasMany(AiJobMatch::class);
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query->where('status', JobStatus::Active->value)
            ->whereNotNull('approved_at')
            ->whereNotNull('published_at')
            ->whereNull('closed_at')
            ->where(function (Builder $query): void {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function isPubliclyVisible(): bool
    {
        return $this->status === JobStatus::Active->value
            && ! is_null($this->approved_at)
            && ! is_null($this->published_at)
            && is_null($this->closed_at)
            && (is_null($this->expires_at) || $this->expires_at->isFuture())
            && ! $this->trashed();
    }

    public function salaryRange(): string
    {
        if (!$this->salary_min && !$this->salary_max) {
            return 'Negotiable';
        }

        $currency = $this->salary_currency ?? 'PHP';

        if ($this->salary_min && $this->salary_max) {
            return $currency . ' ' . number_format($this->salary_min) . ' - ' . number_format($this->salary_max);
        }

        return $currency . ' ' . number_format($this->salary_min ?? $this->salary_max);
    }
}
