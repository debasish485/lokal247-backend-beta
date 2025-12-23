<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
public function toArray($request)
    {
        return [
            'id'       => $this->id,
            'uuid'     => $this->uuid,
            'slug'     => $this->slug,
            'title'    => $this->title,
            'description' => $this->description,

            'location' => [
                'city'     => $this->city,
                'locality' => $this->locality,
            ],

            'salary' => [
                'pay_type'   => $this->pay_type,
                'pay_amount' => $this->pay_amount,
            ],

            // ✅ Recruiter info
            'recruiter' => [
                'id'   => $this->recruiter->id,
                'name' => $this->recruiter->name,
                'company_name' => $this->recruiter->company_name,
            ],

            // ✅ Category info
            'category' => [
                'id'   => $this->category->id,
                'name' => $this->category->name,
            ],

            'created_at' => $this->created_at,
        ];
    }
}
