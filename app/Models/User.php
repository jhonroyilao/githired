<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Profile;
use App\Models\Company;
use App\Models\Application;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;


#[Fillable(['name', 'email', 'role', 'password', 'auth_provider', 'external_auth_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Applicant's profile (1-to-1)
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Employer's company (1-to-1)
     */
    public function company(): HasOne
    {
        return $this->hasOne(Company::class);
    }

    /**
     * Applicant's job applications (1-to-many)
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function resumeDocuments(): HasMany
    {
        return $this->hasMany(ResumeDocument::class);
    }

    public function currentResumeDocument(): HasOne
    {
        return $this->hasOne(ResumeDocument::class)->where('is_current', true);
    }

    public function savedJobs(): HasMany
    {
        return $this->hasMany(SavedJob::class);
    }

    public function appNotifications(): HasMany
    {
        return $this->hasMany(AppNotification::class);
    }

    public function aiJobMatches(): HasMany
    {
        return $this->hasMany(AiJobMatch::class);
    }
}
