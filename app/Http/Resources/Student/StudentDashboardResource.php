<?php

namespace App\Http\Resources\Student;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentDashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $student = $this;
        $course = $student->courses->first();

        if (!$course) {
            return [
                'student' => [
                    'name' => $student->full_name,
                    'slug' => $student->slug,
                ],
                'course' => null,
                'progress' => null,
                'current_lesson' => null,
                'next_task' => null,
                'mentor' => null,
                'certificate' => null,
                'announcements' => [],
            ];
        }

        // -------------------------
        // Progress calculation
        // -------------------------
        $totalVideos = $course->videos->count();

        $completedVideos = $student->videoProgress
            ->where('is_completed', true)
            ->whereIn('video_id', $course->videos->pluck('id'))
            ->count();

        $percentage = $totalVideos > 0
            ? round(($completedVideos / $totalVideos) * 100)
            : 0;

        // -------------------------
        // Current lesson
        // -------------------------
        $watchedVideoIds = $student->videoProgress
            ->where('is_completed', true)
            ->pluck('video_id');

        $currentLesson = $course->videos
            ->whereNotIn('id', $watchedVideoIds)
            ->sortBy('order')
            ->first();

        $currentProgress = null;

        if ($currentLesson) {
            $currentProgress = $student->videoProgress
                ->where('video_id', $currentLesson->id)
                ->first();
        }

        // -------------------------
        // Next task
        // -------------------------
        $course->load('tasks.video');
        $nextTask = null;

        foreach ($course->tasks as $task) {
            if (!$task->isCompletedByStudent($student->id)) {
                $nextTask = $task;
                break;
            }
        }

        // -------------------------
        // Mentor
        // -------------------------
        $mentor = $course->mentors->first();

        return [
            'student' => [
                'name' => $student->full_name,
                'username' => $student->username,
                'email' => $student->email,
                'mobile_number' => $student->code_mobile . $student->mobile_number,
                'gender' => $student->gender,
                'profile_photo' => asset('storage/' . $student->profile_image) ?? null,
                'university_name' => $student->university_name,
                'university_major' => $student->university_major
            ],

            'course' => [
                'id' => $course->id,
                'name' => $course->name,
                'description' => $course->description,
            ],

            'progress' => [
                'completed_lessons' => $completedVideos,
                'total_lessons' => $totalVideos,
                'percentage' => $percentage,
            ],

            'current_lesson' => $currentLesson ? [
                'id' => $currentLesson->id,
                'title' => $currentLesson->title,
                'youtube_id' => $currentLesson->youtube_id,
                'embed_url' => $currentLesson->embed_url,
                'last_position' => $currentProgress?->last_position ?? 0,
                'watched_seconds' => $currentProgress?->watched_seconds ?? 0,
            ] : null,

            'next_task' => $nextTask ? [
                'id' => $nextTask->id,
                'title' => $nextTask->title,
                'video_name' => $nextTask->video?->title,
                'description' => $nextTask->description,
//                'is_required' => $nextTask->is_required,
            ] : null,

            'mentor' => $mentor ? [
                'id' => $mentor->id,
                'name' => $mentor->name,
                'email' => $mentor->email,
                'mobile' => $mentor->code_mobile . $mentor->mobile_number,
                'info' => 'حنين علاء سالم شيخة',
                'profile_image' => $mentor->profile_image,
            ] : null,

            'certificate' => null,

            'announcements' => $course->announcements ?? [],
        ];
    }
}
