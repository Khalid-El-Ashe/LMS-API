<?php

namespace App\Http\Resources\Mentor;

use App\Http\Resources\Course\CourseVideosResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MentorDashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $mentor = $this;

        $course = $mentor->courses->first();

        return [
            'mentor' => [
                'name' => $mentor->name,
                'email' => $mentor->email,
                'profile_image' => asset('storage/' . $mentor->profile_image) ?? null,
            ],

            'course' => [
                'id' => $course->id,
                'name' => $course->name,
                'description' => $course->description,
            ],

            'course_video' => $mentor['course_video_count'], # CourseVideosResource::collection($this->whenLoaded('videos')),

            'students_training_count' => $mentor['student_count'],

            'last_submissions' => $mentor['last_submissions']->map(fn($submission) => [
                'student_name' => $submission->student->full_name,
                'task_title' => $submission->task->title,
                'submitted_at' => $submission->created_at->format('Y-m-d H:i:s'),
            ]),
        ];
    }
}
