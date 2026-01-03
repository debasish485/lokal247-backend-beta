<?php

namespace App\Services;

use App\Models\JobPost;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class JobPostService
{
    /**
     * Default pagination size.
     */
    protected int $defaultPerPage = 15;

    /**
     * Maximum allowed per-page size (safety cap).
     */
    protected int $maxPerPage = 100;

    /**
     * Get paginated list of job posts with optional search & filters.
     *
     * Supported filters (in $filters):
     *  - search: string (applies to title, description)
     *  - city: string
     *  - category_id: int
     *  - recruiter_id: int
     *  - work_type: string (wfh/wfo/hybrid)
     *  - shift_timing: string (day/night/rotational)
     *  - gender_preference: string (any/male/female/other)
     *  - pay_type: string (daily/monthly)
     *  - is_active: bool|int|string
     *  - from_date: Y-m-d (filter by start_date >=)
     *  - to_date: Y-m-d (filter by start_date <=)
     *  - per_page: int
     *
     * @param  array<string,mixed>  $filters
     * @return LengthAwarePaginator
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $perPage = (int) Arr::get($filters, 'per_page', $this->defaultPerPage);

        $query = JobPost::query()
            ->with(['category', 'recruiter'])
            ->orderBy('id', 'desc');

        // Search across title, description
        if ($search = trim((string) Arr::get($filters, 'search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // City filter
        if ($city = Arr::get($filters, 'city')) {
            $query->where('city', $city);
        }

        // Category filter
        if ($categoryId = Arr::get($filters, 'category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Recruiter filter
        if ($recruiterId = Arr::get($filters, 'recruiter_id')) {
            $query->where('recruiter_id', $recruiterId);
        }

        // Work type (wfh / wfo / hybrid)
        if ($workType = Arr::get($filters, 'work_type')) {
            $query->where('work_type', $workType);
        }

        // Shift timing (day / night / rotational)
        if ($shiftTiming = Arr::get($filters, 'shift_timing')) {
            $query->where('shift_timing', $shiftTiming);
        }

        // Gender preference
        if ($gender = Arr::get($filters, 'gender_preference')) {
            $query->where('gender_preference', $gender);
        }

        // Pay type (daily / monthly)
        if ($payType = Arr::get($filters, 'pay_type')) {
            $query->where('pay_type', $payType);
        }

        // Active / inactive
        if (null !== ($isActive = Arr::get($filters, 'is_active', null))) {
            if (is_string($isActive)) {
                $isActive = in_array(strtolower($isActive), ['1', 'true', 'yes'], true) ? 1 : 0;
            }
            $query->where('is_active', (int) $isActive);
        }

        // Date range filter based on start_date
        if ($from = Arr::get($filters, 'from_date')) {
            $query->whereDate('start_date', '>=', $from);
        }
        if ($to = Arr::get($filters, 'to_date')) {
            $query->whereDate('start_date', '<=', $to);
        }

        return $query->paginate($perPage);
    }

    /**
     * Return a single job post by uuid.
     *
     * @param  string  $uuid
     * @return JobPost
     */
    public function getByUuid(string $uuid): JobPost
    {
        return JobPost::with(['category', 'recruiter'])
            ->where('uuid', $uuid)
            ->firstOrFail();
          
    }

    /**
     * Optionally: get a job post by slug.
     *
     * @param  string  $slug
     * @return JobPost
     */
    public function getBySlug(string $slug): JobPost
    {
        return JobPost::with(['category', 'recruiter'])
            ->where('slug', $slug)
            ->firstOrFail();
    }

    /**
     * Create a new job post.
     *
     * JobPost model will auto-generate uuid & slug from title.
     *
     * @param  array<string,mixed>  $data
     * @return JobPost
     */
    public function create(array $data): JobPost
    {
        return DB::transaction(function () use ($data) {
            $payload = $data;

            // If slug is not provided but title exists, you can let model handle it.
            // If you want to override manually:
            // if (empty($payload['slug']) && !empty($payload['title'])) {
            //     $payload['slug'] = Str::slug($payload['title']);
            // }

            return JobPost::create($payload);
        });
    }

    /**
     * Update an existing job post.
     *
     * @param  JobPost|int  $jobOrId
     * @param  array<string,mixed>  $data
     * @return JobPost
     */
    public function update($jobOrId, array $data): JobPost
    {
        return DB::transaction(function () use ($jobOrId, $data) {
            $job = $jobOrId instanceof JobPost
                ? $jobOrId
                : JobPost::findOrFail($jobOrId);

            $job->fill($data);
            $job->save();

            return $job;
        });
    }

    /**
     * Delete job post (soft or hard depending on model).
     *
     * @param  JobPost|int  $jobOrId
     * @return void
     */
    public function delete($jobOrId): void
    {
        $job = $jobOrId instanceof JobPost
            ? $jobOrId
            : JobPost::findOrFail($jobOrId);

        $job->delete();
    }

    /**
     * Get paginated job posts for a given recruiter.
     *
     * @param  int   $recruiterId
     * @param  array<string,mixed>  $filters
     * @return LengthAwarePaginator
     */
    public function getByRecruiter(int $recruiterId, array $filters = []): LengthAwarePaginator
    {
        $filters['recruiter_id'] = $recruiterId;

        return $this->getAll($filters);
    }

    /**
     * Sanitize and cap per-page value.
     *
     * @param  int  $perPage
     * @return int
     */
    protected function sanitizePerPage(int $perPage): int
    {
        $perPage = max(1, $perPage);
        return $perPage > $this->maxPerPage ? $this->maxPerPage : $perPage;
    }
}
