<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Review extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'job_application_id',
        'reviewer_id',
        'reviewer_type',
        'reviewee_id',
        'reviewee_type',
        'rating',
        'comment',
    ];
        protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Relationship: Review belongs to a JobApplication
     */
    public function jobApplication()
    {
        return $this->belongsTo(JobApplication::class);
    }

    /**
     * Polymorphic relationship: Reviewer
     * Can be User (Worker) or Recruiter
     */
    public function reviewer()
    {
        return $this->morphTo();
    }

    /**
     * Polymorphic relationship: Reviewee
     * Can be User (Worker) or Recruiter
     */
    public function reviewee()
    {
        return $this->morphTo();
    }
}
