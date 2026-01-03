<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Http\Resources\JobPostResource;
use App\Services\JobPostService;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class JobPostController extends Controller
{
    protected JobPostService $jobPostService;

    public function __construct(JobPostService $jobPostService)

    {

        $this->jobPostService = $jobPostService;
    }

    /**
     * LIST ALL JOB POSTS (with filters)
     */
    public function index(Request $request)
    {

        $filters = [
            'search'            => $request->query('search'),
            'city'              => $request->query('city'),
            'category_id'       => $request->query('category_id'),
            'recruiter_id'      => $request->query('recruiter_id'),
            'work_type'         => $request->query('work_type'),
            'shift_timing'      => $request->query('shift_timing'),
            'gender_preference' => $request->query('gender_preference'),
            'pay_type'          => $request->query('pay_type'),
            'is_active'         => $request->query('is_active'),
            'from_date'         => $request->query('from_date'),
            'to_date'           => $request->query('to_date'),
            'per_page'          => $request->query('per_page'),
        ];
        

        $data = $this->jobPostService->getAll($filters);

        return JobPostResource::collection($data);
    }

    /**
     * GET SINGLE JOB POST (by UUID)
     */
    public function show($uuid)
    {
    try {
        $job = $this->jobPostService->getByUuid($uuid);



        return response()->json([
            'status' => true,
            'data'   => new JobPostResource($job),
        ]);
    } catch (ModelNotFoundException $e) {
        return response()->json([
            'status'  => false,
            'message' => 'Job post not found',
        ], 404);
    }
    }

    /**
     * CREATE JOB POST
     */


public function store(Request $request)
{
    // 🔹 Handle validation errors
    try {
        $validated = $request->validate([
            'category_id'       => 'required|exists:categories,id',
            'recruiter_id'      => 'required|exists:recruiters,id',
            'title'             => 'required|string|max:255',
            'description'       => 'nullable|string',
            'work_type'         => 'required|in:wfh,wfo,hybrid',
            'city'              => 'required|string',
            'locality'          => 'nullable|string',
            'shift_timing'      => 'required|in:day,night,rotational',
            'start_time'        => 'nullable|date_format:H:i',
            'end_time'          => 'nullable|date_format:H:i',
            'pay_type'          => 'required|in:daily,monthly',
            'pay_amount'        => 'required|integer|min:1',
            'number_of_workers' => 'required|integer|min:1',
            'gender_preference' => 'required|in:any,male,female,other',
            'start_date'        => 'nullable|date',
            'is_active'         => 'boolean',
        ]);
    } catch (ValidationException $e) {
        return response()->json([
            'status'  => false,
            'message' => 'Validation failed',
            'errors'  => $e->errors(),
        ], 422);
    }

    // 🔹 Handle DB exceptions
    try {
        $jobPost = $this->jobPostService->create($validated);
    } catch (QueryException $e) {
        // SQL or DB constraint error
        return response()->json([
            'status'  => false,
            'message' => 'Database error occurred while saving job post',
            'error'   => $e->getMessage(),
        ], 500);
    } catch (Exception $e) {
        // Any other unexpected error
        return response()->json([
            'status'  => false,
            'message' => 'Something went wrong',
            'error'   => $e->getMessage(),
        ], 500);
    }

    // 🔹 Success response
    return response()->json([
        'status'  => true,
        'message' => 'Job post created successfully',
        'job'     => new JobPostResource($jobPost),
    ], 201);
}

    /**
     * UPDATE JOB POST (by UUID)
     */
    public function update(Request $request, $uuid)
    {
        $validated = $request->validate([
            'category_id'       => 'sometimes|exists:categories,id',
            'recruiter_id'      => 'sometimes|exists:recruiters,id',
            'title'             => 'sometimes|string|max:255',
            'description'       => 'nullable|string',
            'work_type'         => 'sometimes|in:wfh,wfo,hybrid',
            'city'              => 'sometimes|string',
            'locality'          => 'nullable|string',
            'shift_timing'      => 'sometimes|in:day,night,rotational',
            'start_time'        => 'nullable|date_format:H:i',
            'end_time'          => 'nullable|date_format:H:i',
            'pay_type'          => 'sometimes|in:daily,monthly',
            'pay_amount'        => 'sometimes|integer|min:1',
            'number_of_workers' => 'sometimes|integer|min:1',
            'gender_preference' => 'sometimes|in:any,male,female,other',
            'start_date'        => 'nullable|date',
            'is_active'         => 'boolean',
        ]);

        $jobPost = $this->jobPostService->getByUuid($uuid);
        $updatedJob = $this->jobPostService->update($jobPost, $validated);

        return response()->json([
            'status'  => true,
            'message' => 'Job post updated successfully',
            'job'     => new JobPostResource($updatedJob),
        ]);
    }

    /**
     * DELETE JOB POST (soft delete)
     */
    public function destroy($uuid)
    {
        $jobPost = $this->jobPostService->getByUuid($uuid);
        $this->jobPostService->delete($jobPost);

        return response()->json([
            'status'  => true,
            'message' => 'Job post deleted successfully',
        ]);
    }
}
