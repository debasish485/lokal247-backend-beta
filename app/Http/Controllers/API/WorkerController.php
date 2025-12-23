<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WorkerResource;
use App\Models\JobPost;
use Illuminate\Http\Request;
use App\Services\WorkerService;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Throwable;

class WorkerController extends Controller
{
    protected WorkerService $workerService;

    public function __construct(WorkerService $workerService)
    {
        $this->workerService = $workerService;
    }

    public function loginOtp(Request $request)
    {
        $request->validate([
            'firebase_token' => 'required|string',
        ]);

        try {
            $result = $this->workerService
                ->authenticateWithOtp($request->firebase_token);

            return response()->json([
                'status' => true,
                'token'  => $result['token'],
                'worker' => $result['worker'],
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Get logged-in worker profile
     */
    public function showProfile(Request $request)
    {
        

        return response()->json([
            'status' => true,
            'data'   =>new WorkerResource( $this->workerService->getProfile($request->user()))
        ]);
    }

public function updateProfile(Request $request)
{
    $validated = $request->validate([
        'name'               => 'sometimes|string|max:50',
        'email'              => 'sometimes|email|unique:users,email,' . $request->user()->id,
        'gender'             => 'sometimes|in:male,female,other',
        'age'                => 'sometimes|integer|min:18|max:65',
        'mobile_number'      => 'sometimes|string|max:15',
        'work_preference'    => 'sometimes|string',
        'work_duration_type' => 'sometimes|string',
        'profile_photo'      => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    try {
        $worker = $this->workerService->updateProfile(
            $request->user(),
            $validated,
            $request->file('profile_photo')
        );

        return (new WorkerResource($worker))
            ->additional([
                'status'  => true,
                'message' => 'Profile updated successfully',
            ]);

    } catch (Exception $e) {



        return response()->json([
            'status'  => false,
            'message' => 'Unable to update profile. Please try again.',
        ], 500);
    }
}

public function applyForJob(Request $request, string $uuid)
{
    try {
        // Validate UUID format
        if (! Str::isUuid($uuid)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid job UUID',
            ], 422);
        }

        // Get authenticated worker
        $worker = $request->user();

        // Fetch job using UUID
        $job = JobPost::where('uuid', $uuid)->first();

        if (! $job) {
            return response()->json([
                'status'  => false,
                'message' => 'Job not found',
            ], 404);
        }

$application = $this->workerService->applyForJob($worker, $job);


        return response()->json([
            'status'  => true,
            'message' => 'Job applied successfully',
            'data'    => $application,
        ], 201);

    } catch (Throwable $e) {


        return response()->json([
            'status'  => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

}
