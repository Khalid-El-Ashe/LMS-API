<?php

namespace App\Http\Resources\Course;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'youtube_id' => $this->youtube_id,

//            'embed_url' => $this->embed_url,
            'video_url' => $this->video_url,
            'thumbnail' => $this->thumbnail,
            'description' => $this->description,
            'duration' => gmdate('i:s', $this->duration),
            'created_at' => $this->created_at->format('Y-m-d'),

//            'comments' => CourseCommentsResource::collection($this->whenLoaded('comments')),
//            'tasks' => new TaskResource(
//                $this->whenLoaded('tasks')
//            ),
//            'previous_video' => $this->previous_video,
//            'next_video' => $this->next_video,
        ];
    }
}
