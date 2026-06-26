<?php

namespace App\Services;


use App\Jobs\CompleteVideoJob;
use App\Jobs\UpdateVideoProgressJob;
use App\Models\StudentVideoProgress;
use Illuminate\Support\Facades\Cache;

/**
 * Save position
 * Complete video
 * Resume video
 */
#todo This Service is Progress Tracking
class VideoProgressService
{
    /**
     * This function is call each time when the Student is Watching the video
     * And make update for the position in the cache and dispatch a job to update the position in the database
     */
    public function updateProgressPosition(int $studentId, int $videoId, int $position, int $watchedSeconds = 0)
    {
        # 1) Save temporary in Redis
        Cache::put(
            "video_progress_{$studentId}_{$videoId}",
            [
                'last_position' => $position,
                'watched_seconds' => $watchedSeconds,
                'updated_at' => now(),
            ],
            now()->addHours(2)
        );

        # 2) Send database update to queue
        UpdateVideoProgressJob::dispatch(
            $studentId,
            $videoId,
            $position,
            $watchedSeconds
        )->onQueue('progress');
    }

    public function completeVideo(int $studentId, int $videoId)
    {
//        $progress = StudentVideoProgress::query()->where('student_id', $studentId)->where('video_id', $videoId)->first();
//        if (!$progress) {
//            return false;
//        }
//
//        if ($progress->isCompleted()) {
//            return true;
//        }

        CompleteVideoJob::dispatch(
            $studentId,
            $videoId
        )->onQueue('progress');

        Cache::forget(
            "video_progress_{$studentId}_{$videoId}"
        );
        return true;
    }

    /**
     * This function is return the last position for Video is Stopped
     */
    public function resumeVideo(int $studentId, int $videoId)
    {
        # 1) Try Redis first
        $cached = Cache::get(
            "video_progress_{$studentId}_{$videoId}"
        );

        if ($cached) {
            return $cached['last_position'];
        }

        # 2) Redis empty -> get from Database
        $progress = StudentVideoProgress::query()
            ->where('student_id', $studentId)
            ->where('video_id', $videoId)
            ->first();

        $data = [
            'last_position' => $progress?->last_position ?? 0,
            'watched_seconds' => $progress?->watched_seconds ?? 0,
        ];

        # 3) Restore Cache
        Cache::put(
            "video_progress_{$studentId}_{$videoId}",
            $data + [
                'updated_at' => now()
            ],
            now()->addHours(2)
        );

        return $data;
    }
}
