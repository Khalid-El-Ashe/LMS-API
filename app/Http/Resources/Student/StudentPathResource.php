<?php

namespace App\Http\Resources\Student;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentPathResource extends JsonResource
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
                'path' => null,
                'videos' => [],
                'progress' => null,
                'current_video' => null,
            ];
        }

        /*
|--------------------------------------------------------------------------
| Progress
|--------------------------------------------------------------------------
*/
        $videos = $course->videos
            ->sortBy('order')
            ->values();


        $completedIds = $student
            ->videoProgress
            ->where('is_completed', true)
            ->pluck('video_id');


        $completedCount = $completedIds
            ->intersect($videos->pluck('id'))
            ->count();


        $totalVideos = $videos->count();


        $percentage = $totalVideos > 0 ? round(($completedCount / $totalVideos) * 100) : 0;

        /*
|--------------------------------------------------------------------------
| Current Video
|--------------------------------------------------------------------------
*/
        $currentVideo = $videos
            ->first(function ($video) use ($completedIds) {

                return !$completedIds->contains($video->id);
            });

        /*
|--------------------------------------------------------------------------
| Videos List
|--------------------------------------------------------------------------
*/


        $videoList = $videos->map(function ($video) use ($completedIds) {

            return [

                'index' => $video->order,
                'id' => $video->id,
                'title' => $video->title,
                'duration' => $video->duration,
                'youtube_id' => $video->youtube_id,
//                'embed_url' => $video->embed_url,
                'video_url' => $video->video_url,
                'thumbnail_url' => $video->thumbnail,
                'completed' => $completedIds->contains($video->id)

            ];

        });


        return [

            'path' => [
                'id' => $course->id,
                'name' => $course->name,
                'description' => $course->description,
            ],

            'progress' => [
                'completed' => $completedCount,
                'total' => $totalVideos,
                'percentage' => $percentage,
            ],

            'current_video' => $currentVideo ? [
                'index' => $currentVideo->order,
                'id' => $currentVideo->id,
                'title' => $currentVideo->title,
            ] : null,

            'videos' =>
                $videoList,
        ];
    }
}
