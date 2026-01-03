<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Exception;
use Throwable;

class ReviewController extends Controller
{
    protected ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function store(Request $request, string $jobuuid)
    {
        try {

            // 1️⃣ Validate request
            $validated = $request->validate([
                'rating'        => 'required|integer|min:1|max:5',
                'comment'       => 'nullable|string|max:2000',
                'reviewee_id'   => 'required|integer',
                'reviewee_type' => 'required|string|in:App\Models\User,App\Models\Recruiter',
            ]);

            // 2️⃣ Auth user
            $reviewer = $request->user();

            if (!$reviewer) {
                return response()->json([
                    'message' => 'Unauthorized.',
                ], 401);
            }

            // 3️⃣ Delegate logic to service
            $review = $this->reviewService->createReview(
                $validated,
                $jobuuid,
                $reviewer
            );

            return response()->json([
                'message' => 'Review submitted successfully.',
                'data'    => $review,
            ], 201);

        } catch (Exception $e) {

            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 422);

        } catch (Throwable $e) {

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function update(Request $request, string $reviewUuid)
{
    try {

        // 1️⃣ Validate request
        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        // 2️⃣ Auth user
        $reviewer = $request->user();

        if (!$reviewer) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 401);
        }

        // 3️⃣ Delegate logic to service
        $review = $this->reviewService->updateReview(
            $validated,
            $reviewUuid,
            $reviewer
        );

        return response()->json([
            'message' => 'Review updated successfully.',
            'data'    => $review,
        ], 200);

    } catch (Exception $e) {

        return response()->json([
            'message' => $e->getMessage(),
        ], $e->getCode() ?: 422);

    } catch (Throwable $e) {

        return response()->json([
            'message' => 'Something went wrong.',
        ], 500);
    }
}

}
