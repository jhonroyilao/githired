<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'title',
        'slug',
        'location',
        'location_type',
        'type',
        'experience_level',
        'category',
        'description',
        'requirements',
        'skills_required',
        'salary_min',
        'salary_max',
        'salary_currency',
        'status',
        'rejection_reason',
        'published_at',
        'expires_at',
        'views_count',
    ];

    protected $casts = [
        'skills_required' => 'array',
        'published_at'     => 'datetime',
        'expires_at'       => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    // ── Scopes ──────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // ── Helpers ─────────────────────────────────────────────
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