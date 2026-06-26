<?php

namespace App\Jobs;

use App\Models\Course;
use App\Models\CourseVideo;
use App\Services\VideoSyncService;
use App\Services\YoutubeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;

/**
 * Summary of FetchPlaylistVideosJob
 *
 * this Job is responsible for fetching the videos from the youtube playlist and saving them to the database, this job will be dispatched when a course is created or updated with a youtube playlist url, this way we can fetch the videos in the background and not block the main thread, and also we can retry the job if it fails for any reason, and we can also log the errors if the job fails
 *
 * and this is (Executing a Commands Design Pattern) because we are encapsulating the logic of fetching the videos from the youtube playlist and saving them to the database in a single class, and we are dispatching this class as a job to be executed in the background, this way we can keep our controllers and services clean and focused on their main responsibilities, and we can also reuse this job in other parts of the application if needed
 *
 * That mean this Job is just to get Data
 */
class FetchPlaylistVideosJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;
    protected $courseId;
    protected $playlistId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $courseId, string $playlistId)
    {
        $this->courseId = $courseId;
        $this->playlistId = $playlistId;
    }

    /**
     * Execute the job.
     */
    public function handle(VideoSyncService $videoSyncService): void
    {
        $course = Course::query()->find($this->courseId);
        if (!$course) return;

        // now in here I need to extract a playlistID ok
//        parse_str(parse_url($this->playlistUrl, PHP_URL_QUERY), $params);
//        $playlistId = $params['list'] ?? null;

//        if (!$playlistId) return;

        $videoSyncService->syncCourseVideos($course, $this->playlistId);
    }
}
