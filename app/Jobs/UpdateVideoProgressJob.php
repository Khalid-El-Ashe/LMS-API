<?php

namespace App\Jobs;

use App\Models\CourseVideo;
use App\Models\StudentVideoProgress;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Log;

class UpdateVideoProgressJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $studentId,
        public int $videoId,
        public int $position,
        public int $watchedSeconds
    )
    {
    }

    public function handle(): void
    {

        $video = CourseVideo::query()->find($this->videoId);
        if (!$video) return;

        $duration = $video->duration ?? 0;

//        $progress = StudentVideoProgress::query()->updateOrCreate(
//            [
//                'student_id' => $this->studentId,
//                'video_id' => $this->videoId,
//            ],
//            [
//                'last_position' => $this->position,
//                'watched_seconds' => $this->watchedSeconds,
//            ]
//        );
//
//        if ($duration > 0 && ($this->watchedSeconds >= ($duration * 0.9) || $this->position >= ($duration * 0.9))) {
//            $progress->update(['is_completed' => true, 'completed_at' => now()]);
//        }

        $progress = StudentVideoProgress::query()->updateOrCreate(
            [
                'student_id' => $this->studentId,
                'video_id' => $this->videoId,
            ],
            [
                'last_position' => $this->position,
                'watched_seconds' => $this->watchedSeconds,
            ]
        );

        // Prevent queue race condition
        $progress->last_position = max(
            $progress->last_position ?? 0,
            $this->position
        );

        $progress->watched_seconds = max(
            $progress->watched_seconds ?? 0,
            $this->watchedSeconds
        );

        // Auto complete
        if (
            $duration > 0 &&
            (
                $progress->watched_seconds >= ($duration * 0.9)
                ||
                $progress->last_position >= ($duration * 0.9)
            )
        ) {

            $progress->is_completed = true;
            $progress->completed_at = now();
        }

        $progress->save();
    }
}
