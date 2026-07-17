<?php

namespace App\Services;

use App\Http\Resources\Course\CourseVideosResource;
use App\Http\Resources\Course\VideoResource;
use App\Jobs\FetchPlaylistVideosJob;
use App\Models\CourseVideo;
use App\Repositories\Course\CourseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class CourseService
{
    // This service can be used to handle business logic related to courses
    // For example, we can have methods to calculate course ratings, handle course enrollments, etc.

    protected $courseRepo;
    protected $youtubeService;

    public function __construct(CourseRepository $courseRepo, YoutubeService $youtubeService)
    {
        $this->courseRepo = $courseRepo;
        $this->youtubeService = $youtubeService;
    }

    /**
     * @throws Throwable
     */
    public function createCourse(array $data)
    {
        /**
         * note: we need to use transaction here because we need to create a course and its related data in one go, if any of the operation fails, we need to rollback the transaction and return an error message
         */
        return DB::transaction(function () use ($data) {

            if (isset($data['logo'])) {
                $data['logo'] = $data['logo']->store('courses', 'public');
            }

            $data['slug'] = Str::slug($data['name']) . '-' . Str::lower(Str::random(6));

            /**
             *filtering course data from the request data
             *Create a collection from the given value
             */
            $courseData = collect($data)->only([
                'name',
                'slug',
                'description',
                'logo',
                'price',
                'rating',
                'youtube_playlist_url',
            ])->toArray();

            // 1. Create Course
            $course = $this->courseRepo->createCourse($courseData);

            // now we want to use the syncCourseVideos method to sync the videos to the course because we already have the video details and we dont want to fetch them again from the youtube API, so we will use the createVideos method to create the videos directly without fetching the details again
            if (filled($data['youtube_playlist_url'])) {
                $playlistId = $this->extractYoutubePlaylistId(
                    $data['youtube_playlist_url']
                );

//                FetchPlaylistVideosJob::dispatchSync(
//                    $course->id,
//                    $playlistId
//                )->afterCommit();
                FetchPlaylistVideosJob::dispatchSync(
                    $course->id,
                    $playlistId
                ); // Dispatch the job after the transaction is committed
            }

            return $course->load(['videos']);
        });
    }

    protected function extractYoutubePlaylistId(string $url): string
    {
        parse_str(parse_url($url, PHP_URL_QUERY), $params);

        return $params['list'] ?? $url;
    }

    public function getVideoDetails(CourseVideo $courseVideo)
    {
        return new CourseVideosResource(
            $this->courseRepo->getVideoDetails($courseVideo)
        );
    }
}
