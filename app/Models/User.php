<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\ResumeDocument;
use App\Models\Profile;
use App\Models\Company;
use App\Models\Application;


#[Fillable(['name', 'email', 'role', 'password', 'auth_provider', 'external_auth_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array //Format database fields
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile(): HasOne //Applicant's profile (1-to-1)
    {
        return $this->hasOne(Profile::class);
    }

    public function company(): HasOne //Employer's company (1-to-1)
    {
        return $this->hasOne(Company::class);
    }

    public function applications(): HasMany //Applicant's job applications (1-to-many)
    {
        return $this->hasMany(Application::class);
    }

    public function resumeDocuments(): HasMany //Get resumes uploaded by user
    {
        return $this->hasMany(ResumeDocument::class);
    }

    public function currentResumeDocument(): HasOne //Grab current resume
    {
        return $this->hasOne(ResumeDocument::class)->where('is_current', true);
    }

    public function savedJobs(): HasMany //Jobs saved by the user
    {
        return $this->hasMany(SavedJob::class);
    }

    public function appNotifications(): HasMany //User's notifications
    {
        return $this->hasMany(AppNotification::class);
    }

    public function aiJobMatches(): HasMany //AI job match results
    {
        return $this->hasMany(AiJobMatch::class);
    }
}