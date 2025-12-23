<?php

namespace App\Services;

use App\Models\Recruiter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use App\Models\JobPost;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;


class RecruiterService 
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
     * Attempt to authenticate recruiter and create a personal access token.
     *
     * @param  string  $email
     * @param  string  $password
     * @param  string|null  $tokenName
     * @return array{token: string, recruiter: Recruiter}
     *
     * @throws ValidationException
     */
    public function login(string $email, string $password, ?string $tokenName = 'recruiter-token'): array
    {
        $recruiter = Recruiter::where('email', $email)->first();

        if (! $recruiter || ! Hash::check($password, $recruiter->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid email or password'],
            ]);
        }

$token = $recruiter->createToken('recruiter-token', ['create-job', 'edit-job'])->plainTextToken;


        return [
            'token'     => $token,
            'recruiter' => $recruiter,
        ];
    }

    /**
     * Get paginated list of recruiters with optional search & filters.
     *
     * Supported filters (in $filters):
     *  - search: string (applies to name, email, company_name)
     *  - city: string
     *  - is_blocked: bool|int
     *  - per_page: int
     *
     * @param  array<string,mixed>  $filters
     * @return LengthAwarePaginator
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $perPage = (int) Arr::get($filters, 'per_page', $this->defaultPerPage);

        $query = Recruiter::query()->orderBy('id', 'desc');

        // Search across name, email, company_name
        if ($search = trim((string) Arr::get($filters, 'search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        // Filter by city
        if ($city = Arr::get($filters, 'city')) {
            $query->where('city', $city);
        }

        // Filter by is_blocked (accepts 0/1, true/false)
        if (null !== ($isBlocked = Arr::get($filters, 'is_blocked', null))) {
            if (is_string($isBlocked)) {
                $isBlocked = in_array(strtolower($isBlocked), ['1', 'true', 'yes'], true) ? 1 : 0;
            }
            $query->where('is_blocked', (int) $isBlocked);
        }

        return $query->paginate($perPage);
    }

    /**
     * Return a single recruiter by uuid.
     *
     * @param  int|string  $uuid
     * @return Recruiter
     */
public function getById($uuid): Recruiter
{
    return Recruiter::where('uuid', $uuid)->firstOrFail();
}





    /**
     * Create a new recruiter record.
     * Accepts $data array and hashes password if provided.
     *
     * @param  array<string,mixed>  $data
     * @return Recruiter
     */
    public function create(array $data): Recruiter
    {
        return DB::transaction(function () use ($data) {
            $payload = $data;

            if (! empty($payload['password'])) {
                $payload['password'] = bcrypt($payload['password']);
            }

            return Recruiter::create($payload);
        });
    }

    /**
     * Update an existing recruiter.
     *
     * @param  Recruiter|int  $recruiterOrId
     * @param  array<string,mixed>  $data
     * @return Recruiter
     */
    public function update($recruiterOrId, array $data): Recruiter
    {
        return DB::transaction(function () use ($recruiterOrId, $data) {
            $recruiter = $recruiterOrId instanceof Recruiter
                ? $recruiterOrId
                : Recruiter::findOrFail($recruiterOrId);

            $payload = $data;

            // If password present and non-empty, hash it
            if (array_key_exists('password', $payload) && $payload['password'] !== null && $payload['password'] !== '') {
                $payload['password'] = bcrypt($payload['password']);
            } else {
                // prevent accidentally nullifying password on mass update
                unset($payload['password']);
            }

            $recruiter->fill($payload);
            $recruiter->save();

            return $recruiter;
        });
    }

    public function getJobs(Recruiter $recruiter,int $perPage): LengthAwarePaginator 
    {
    return $recruiter->jobs()->latest()->paginate($perPage);
    }

    /**
     * Delete recruiter (soft or hard depending on model).
     *
     * @param  Recruiter|int  $recruiterOrId
     * @return void
     */
    public function delete($recruiterOrId): void
    {
        $recruiter = $recruiterOrId instanceof Recruiter
            ? $recruiterOrId
            : Recruiter::findOrFail($recruiterOrId);

        $recruiter->delete();
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

public function getProfile(int $recruiterId): Recruiter
{
    return Recruiter::findOrFail($recruiterId);
}
public function updateProfile(Recruiter $recruiter,array $data,$logo = null): Recruiter {
    if ($logo) {
        $data['logo'] = $logo->store('recruiters/logos', 'public');
    }

    $recruiter->update($data);

    return $recruiter->fresh();
}
    /**
     * Create a new job post.
     *
     * JobPost model will auto-generate uuid & slug from title.
     *
     * @param  array<string,mixed>  $data
     * @return JobPost
     */
    public function createJobPost(array $data): JobPost
    {

            return DB::transaction(function () use ($data) {
                return JobPost::create($data);
            });
 
    }

}
