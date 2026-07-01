<?php

namespace App\Http\Resources\Mentor;

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
        $mentor = $this;

        return [
            'name' => $mentor->name,
            'email' => $mentor->email,
            'username' => $mentor->username,
            'courses' => CourseResource::collection($mentor->whenLoaded('courses')),
        ];

    }
}
