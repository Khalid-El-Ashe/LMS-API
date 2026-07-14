<?php

namespace App\Http\Resources\Student;

use App\Http\Resources\Course\CourseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'mobile_number' => $this->code_mobile . '-' . $this->mobile_number,
            'profile_photo' => asset('storage/' . $student->profile_image) ?? null,
            'gender' => $this->gender,
//            'username' => $this->username,
            'mentor' => $this->courses()->with('mentors')->get()
                ->flatMap(fn($course) => $course->mentors)
                ->unique('id')
                ->values()
                ->map(fn($mentor) => $mentor->name),
            'courses' => CourseResource::collection($this->whenLoaded('courses')),
        ];
    }
}
