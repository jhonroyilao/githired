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
        'content_hash',
        'extraction_status',
        'is_current',
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
}