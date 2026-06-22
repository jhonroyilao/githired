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
        'user_id', 'company_id', 'category_id', 'title', 'slug', 'location',
        'location_type', 'type', 'experience_level', 'description', 'requirements',
        'skills_required', 'salary_min', 'salary_max', 'salary_currency', 'status',
        'rejection_reason', 'submitted_at', 'approved_at', 'approved_by',
        'rejected_at', 'rejected_by', 'closed_at', 'published_at', 'expires_at',
    ];

    protected $casts = [
        'status'          => JobStatus::class,
        'skills_required' => 'array',
        'submitted_at'    => 'datetime',
        'approved_at'     => 'datetime',
        'rejected_at'     => 'datetime',
        'closed_at'       => 'datetime',
        'published_at'    => 'datetime',
        'expires_at'      => 'datetime',
        'deleted_at'      => 'datetime', // binalik q na yung sa softdeletes
        'salary_min'      => 'decimal:2',
        'salary_max'      => 'decimal:2',
    ];

    // ── relationships ────────────────────────────────────────────────────

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
        return $this->hasMany(SavedJob::class); // FIX
    }

    // ── scopes ───────────────────────────────────────────────────────────

    public function scopePubliclyVisible(Builder $query): void
    {
    $query->where('status', JobStatus::Active);
    
    /* ------------------ NOTE: cinomment out ko muna kasi walang lalabas na job card if nilagay ko lahat ng restrictions
    since naka NULL yung approved sa 

    $query->where('status', JobStatus::Active)
          ->whereNotNull('approved_at')
          ->whereNotNull('published_at')
          ->whereNull('closed_at')
          ->where(function ($q) {
              $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
          });
    */
    }

    // ── helpers ──────────────────────────────────────────────────────────

    public function isPubliclyVisible(): bool
    {
        return $this->status === JobStatus::Active 
            && !is_null($this->approved_at)
            && !is_null($this->published_at)
            && is_null($this->closed_at)
            && (is_null($this->expires_at) || $this->expires_at->isFuture())
            && !$this->trashed();
    }

    public function salaryRange(): ?string
    {
        if (! $this->salary_min && ! $this->salary_max) return null;
        $currency = $this->salary_currency ?? 'PHP';
        if ($this->salary_min && $this->salary_max) {
            return $currency.' '.number_format($this->salary_min).' – '.number_format($this->salary_max);
        }
        return $currency.' '.number_format($this->salary_min ?? $this->salary_max);
    }
}