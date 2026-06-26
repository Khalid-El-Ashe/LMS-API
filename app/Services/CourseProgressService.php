<?php

namespace App\Services;

use App\Models\CourseVideo;
use App\Models\StudentVideoProgress;

class CourseProgressService
{

    /**
     * this function is calculate the count of videos is completed and count of course videos
     * and return count completed by % (20 Videos and Completed = 8 => 8/20 * 100 = 40%)
     */
    public function courseProgress(
        int $courseId,
        int $studentId
    ): float
    {

        $totalVideos = CourseVideo::query()
            ->where('course_id', $courseId)
            ->count();

        if ($totalVideos === 0) {
            return 0;
        }

        $completedVideos = StudentVideoProgress::query()
            ->where('student_id', $studentId)
            ->where('is_completed', true)
            ->join('course_videos', 'student_video_progress.video_id', '=', 'course_videos.id')
            ->where('course_videos.course_id', $courseId)
            ->count();

        return round(
            ($completedVideos / $totalVideos) * 100,
            2
        );
    }
}
