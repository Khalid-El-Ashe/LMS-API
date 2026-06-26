<?php

namespace App\Http\Resources;

use App\Http\Resources\Course\CourseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MentorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'full_name' => $this->full_name,
            'university_name' => $this->university_name,
            'university_major' => $this->university_major,
            'mobile_number' => $this->mobile_number,
            'teacher_collage' => $this->teacher_collage,
            'gender' => $this->gender,
            'username' => $this->username,
            'courses' => CourseResource::collection($this->whenLoaded('courses')),
        ];

    }
}
