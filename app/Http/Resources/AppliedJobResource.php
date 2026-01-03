<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Recruiter;

class AppliedJobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
return [
    'id' => $this->id,
    'uuid' => $this->uuid,
    'slug' => $this->slug,
    'recruiter_name' => $this->recruiter->name,
    'category_name' => $this->category->name,
    'title' => $this->title,
    'description' => $this->description,
    'work_type' => $this->work_type,
    'city' => $this->city,
    'locality' => $this->locality,
    'shift_timing' => $this->shift_timing,
    'pay_type' => $this->pay_type,
    'pay_amount' =>$this->pay_amount,
    'number_of_workers' => $this->number_of_workers,
    'gender_preference' => $this->gender_preference,
    'start_date' => $this->start_date

];

    }
}
