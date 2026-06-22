<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResumeDocument extends Model
{
    use HasFactory;

    protected $fillable = [ //Fields we can save on
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

    protected $casts = [ //Ensure data types are correct when pulling from the database
        'is_current' => 'boolean',
        'file_size' => 'integer',
    ];

    //Link to resume owner
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true); //Filter for the active resume
    }

    //Link to the applicant's job submissions
    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    //Link to the AI matching results
    public function aiJobMatches()
    {
        return $this->hasMany(AiJobMatch::class);
    }
}