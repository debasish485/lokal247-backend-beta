<?php

namespace App\Services;

use App\Enums\JobApplicationStatus;
use App\Models\JobApplication;
use App\Models\Review;
use Exception;

class ReviewService
{
    public function createReview(array $validated, string $jobuuid, $reviewer)
    {
        // 1️⃣ Find JobApplication using UUID
        $jobApplication = JobApplication::where('job_uuid', $jobuuid)->first();

        if (!$jobApplication) {
            throw new Exception('Job application not found.', 404);
        }

        // 2️⃣ Ensure job is completed
        if ($jobApplication->status !== JobApplicationStatus::APPLIED) {
            throw new Exception('Review allowed only after job completion.', 422);
        }

        // 3️⃣ Reviewer details
        $reviewerId   = $reviewer->id;
        $reviewerType = get_class($reviewer);

        // 4️⃣ Prevent self-review
        if (
            $reviewerId === (int) $validated['reviewee_id'] &&
            $reviewerType === $validated['reviewee_type']
        ) {
            throw new Exception('You cannot review yourself.', 422);
        }

        // 5️⃣ Prevent duplicate review
        $alreadyReviewed = Review::where('job_application_id', $jobApplication->id)
            ->where('reviewer_id', $reviewerId)
            ->where('reviewer_type', $reviewerType)
            ->exists();

        if ($alreadyReviewed) {
            throw new Exception('You have already submitted a review for this job.', 409);
        }

        // 6️⃣ Create review
        return Review::create([
            'job_application_id' => $jobApplication->id,
            'reviewer_id'        => $reviewerId,
            'reviewer_type'      => $reviewerType,
            'reviewee_id'        => $validated['reviewee_id'],
            'reviewee_type'      => $validated['reviewee_type'],
            'rating'             => $validated['rating'],
            'comment'            => $validated['comment'] ?? null,
        ]);
    }

    public function updateReview(array $data, string $reviewUuid, $reviewer)
    {
        // 1️⃣ Find review
        $review = Review::where('uuid', $reviewUuid)->first();

        if (!$review) {
            throw new Exception('Review not found.', 404);
        }

        // 2️⃣ Ensure the authenticated user owns the review
        if ($review->reviewer_id !== $reviewer->id ||
            $review->reviewer_type !== get_class($reviewer)) {
            throw new Exception('You are not allowed to update this review.', 403);
        }

        // 3️⃣ Update review
        $review->update([
            'rating'  => $data['rating'],
            'comment' => $data['comment'] ?? $review->comment,
        ]);

        return $review->fresh();
    }
}
