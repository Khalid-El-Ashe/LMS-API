<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LandingPageCourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $video = $this->videos->first();

        return [
            'name' => $this->name,
            'description' => $this->description,

            'thumbnail' => $video
                ? "https://i.ytimg.com/vi/{$video->youtube_id}/hqdefault.jpg"
                : null,
        ];
    }
}
