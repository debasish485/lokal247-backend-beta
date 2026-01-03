<?php

namespace App\Services;

use App\Models\JobApplication;
use App\Enums\JobApplicationStatus;
use App\Http\Resources\AppliedJobCollection;
use App\Http\Resources\JobPostResource;
use App\Models\JobPost;
use App\Models\User as Worker;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Kreait\Firebase\Auth;


class WorkerService
{
    protected Auth $firebaseAuth;

    /*
    public function __construct(Auth $firebaseAuth)
    {
        $this->firebaseAuth = $firebaseAuth;
    }
    */

    public function authenticateWithOtp(string $firebaseToken): array
    {
        if (empty($firebaseToken)) {
            throw new \InvalidArgumentException('Firebase token is required');
        }

        // TEMP: hardcoded test phone number
        $testPhoneNumber = '9073742826';

        $worker = Worker::where('mobile_number', $testPhoneNumber)->first();

        if (! $worker) {
            throw new \RuntimeException('Worker not found');
        }

        return [
            'worker' => $worker,
            'token'  => $worker->createToken('worker_token')->plainTextToken,
        ];
    }

    /**
     * Fetch worker profile
     */
    public function getProfile(Worker $worker): Worker
    {
        return $worker;
    }

    /**
     * Update worker profile + optional profile photo
     */
    public function updateProfile(
        Worker $worker,
        array $formData,
        ?UploadedFile $profile_photo = null
    ): Worker {
        return DB::transaction(function () use ($worker, $formData, $profile_photo) {

            // Remove image from mass assignment data
            unset($formData['profile_photo']);

            // Update profile fields
            if (!empty($formData)) {
                $worker->update($formData);
            }

            // Handle profile photo if provided
            if ($profile_photo) {

                // Delete old photo if exists
                if ($worker->profile_photo) {
                    Storage::disk('public')->delete($worker->profile_photo);
                }

                // Generate slug from worker name
                $workerSlug = Str::slug($worker->name);

                // Safe extension (MIME-based)
                $extension = $profile_photo->extension();

                // Build filename
                $fileName = $workerSlug . '-' . $worker->id . '-' . time() . '.' . $extension;

                // Store new photo
                $path = $profile_photo->storeAs(
                    'workers/profile-photos',
                    $fileName,
                    'public'
                );

                // Save path in DB
                $worker->update([
                    'profile_photo' => $path,
                ]);
            }

            return $worker->fresh();
        });
    }

public function applyForJob(User $worker, JobPost $job): JobApplication
{
    $exists = JobApplication::where('job_id', $job->id)
        ->where('worker_id', $worker->id)
        ->exists();

    if ($exists) {
        throw new \Exception('Worker has already applied for this job');
    }

    return JobApplication::create([
        'job_id'    => $job->id,
        'worker_id' => $worker->id,
        'status'    => JobApplicationStatus::APPLIED,
    ]);
}

public function viewAppliedJobs(User $worker)
{
    $jobs = JobPost::whereHas('applications', function ($query) use ($worker) {
        $query->where('worker_id', $worker->id);
    })->paginate(5);

    return new AppliedJobCollection($jobs);
}

}
