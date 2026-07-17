<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseVideo;
use App\Repositories\Course\CourseRepository;

class VideoSyncService
{

    protected $courseRepo;
    protected $youtubeService;

    public function __construct(CourseRepository $courseRepo, YoutubeService $youtubeService)
    {
        $this->courseRepo = $courseRepo;
        $this->youtubeService = $youtubeService;
    }

    /**
     * @param Course $course
     * @param string $playlistId
     * @return void
     */
    public function syncCourseVideos(Course $course, string $playlistId)
    {
        $videos = $this->youtubeService->getPlaylistVideos($playlistId);

        if ($videos->isEmpty()) {
            return;
        }

        $videoIds = $videos->pluck('youtube_id')->toArray();
        $videoDetails = $this->youtubeService->getVideoDetails($videoIds);

        $data = $videos->map(function ($video) use ($videoDetails, $course) {
            $details = $videoDetails->firstWhere('youtube_id', $video['youtube_id']);

            return [
                'course_id' => $course->id,
                'youtube_id' => $video['youtube_id'],
                'title' => $video['title'],
                'description' => $video['description'] ?? null,
                'duration' => $details['duration'] ?? null,
                'thumbnail' => $video['thumbnail'] ?? null,
                'order' => $video['order'] ?? 0,
            ];
        });

        $course->videos()->upsert(
            $data->toArray(),
            ['course_id', 'youtube_id'],
            ['title', 'description', 'duration', 'thumbnail', 'order']
        );
    }
}
