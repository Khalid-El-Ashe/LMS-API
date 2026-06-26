<?php

namespace App\Jobs;

use App\Models\StudentVideoProgress;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CompleteVideoJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private int $studentId,
        private int $videoId
    )
    {
    }

    public function handle(): void
    {
        StudentVideoProgress::query()->updateOrCreate(
            [
                'student_id' => $this->studentId,
                'video_id' => $this->videoId,
            ],
            [
                'is_completed' => true,
                'completed_at' => now(),
            ]
        );
    }
}
