<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobPostResource;
use Illuminate\Http\Request;
use App\Services\RecruiterService;
use App\Http\Resources\RecruiterResource;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Foundation\Auth\User;
use PHPUnit\Event\Code\Throwable;

class RecruiterController extends Controller
{
    protected RecruiterService $recruiterService;

    public function __construct(RecruiterService $recruiterService)
    {
        $this->recruiterService = $recruiterService;
    }

    /**
     * LOGIN METHOD (calls service->login)
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        try {
            $result = $this->recruiterService->login($data['email'], $data['password']);
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid credentials',
                'errors'  => $e->errors(),
            ], 422);
        }catch(Exception $e){
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }catch(Throwable $e){
            return response()->json([
                'status'  => false,
                'message' => 'Internal server error',
            ], 500);
        }

        return response()->json([
            'status'    => true,
            'message'   => 'Login successful',
            'token'     => $result['token'],
            'recruiter' => new RecruiterResource($result['recruiter']),
        ]);
    }

    /**
     * LOGOUT METHOD
     */
    public function logout(Request $request)
    {
        if ($request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'status'  => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * INDEX METHOD (list all recruiters)
     */
    public function index(Request $request)
    {
        $filters = [
            'per_page'   => $request->query('per_page'),
            'search'     => $request->query('search'),
            'city'       => $request->query('city'),
            'is_blocked' => $request->query('is_blocked'),
        ];

        $data = $this->recruiterService->getAll($filters);

        return RecruiterResource::collection($data);
    }

    /**
     * SHOW METHOD (single recruiter by ID)
     */
    public function show($uuid)
    {
        $recruiter = $this->recruiterService->getById($uuid);

        return new RecruiterResource($recruiter);
    }

public function jobs(Request $request)
{
    $recruiter = $request->user();

    $perPage = (int) $request->query('per_page', 15);

    return JobPostResource::collection(
        $this->recruiterService->getJobs($recruiter, $perPage)
    );
}

public function profile(Request $request)
{
    return new RecruiterResource(
        $this->recruiterService->getProfile($request->user()->id)
    );
}

public function updateProfile(Request $request)
{
$data = $request->validate([
    'name'              => 'required|string|max:255',
    'company_name'      => 'required|string|max:255',
    'designation'       => 'nullable|string|max:255',
    'industry'          => 'nullable|string|max:255',
    'phone'             => 'nullable|string|max:20',
    'alternate_phone'   => 'nullable|string|max:20',
    'city'              => 'nullable|string|max:100',
    'company_address'   => 'nullable|string|max:500',
    'company_logo'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
]);


    $recruiter = $request->user();

    $updatedRecruiter = $this->recruiterService->updateProfile($recruiter,$data,$request->file('logo'));

    return new RecruiterResource($updatedRecruiter);
}

public function createJobPost(Request $request)
    {
        $validated = $request->validate([
            'category_id'       => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
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
$validated['recruiter_id'] = auth('sanctum')->id();



        $jobPost = $this->recruiterService->createJobPost($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Job post created successfully',
            'job'     => new JobPostResource($jobPost),
        ], 200);
    }

}
