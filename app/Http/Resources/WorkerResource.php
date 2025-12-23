<?php

namespace App\Http\Resources;

use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Http\Request;
use App\Models\User as Worker;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name'  => $this->name,
            'email' => $this->email,

            'mobile_number' => $this->mobile_number,

            'gender' => $this->gender,
            'age'    => $this->age,

            'work' => [
                'preference'     => $this->work_preference,
                'duration_type' => $this->work_duration_type,
            ],

            'profile_photo' => $this->profile_photo
                ? asset('storage/' . $this->profile_photo)
                : null,

        ];
    }


}
