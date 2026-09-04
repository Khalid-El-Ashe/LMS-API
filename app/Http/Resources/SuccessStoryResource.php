<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuccessStoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $successStory = $this;


        return [
            'student_name' => $successStory->name,
            'story' => $successStory->story,
            'image' => $successStory->image ? asset('storage/' . $successStory->image) : null,
            'path' => $successStory->path,
        ];
    }
}
