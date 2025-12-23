<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\JobApplicationStatus;
use App\Models\JobPost;
use App\Models\User as Worker;

class JobApplication extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'job_id',
        'worker_id',
        'status',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'status' => JobApplicationStatus::class
    ];

    /**
     * Relationship: JobApplication belongs to a Job
     */
    public function job()
    {
        return $this->belongsTo(JobPost::class,'job_id');
    }

    /**
     * Relationship: JobApplication belongs to a Worker (User)
     */
    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
