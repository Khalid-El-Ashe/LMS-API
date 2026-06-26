<?php

namespace App\Http\Resources\Course;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseCommentsResource extends JsonResource
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
            'body' => $this->body,

            'user_type' => class_basename($this->user_type),

            'user' => [
                'id' => optional($this->user)->id,
                'name' => optional($this->user)->name ?? optional($this->user)->full_name,
            ],

            'created_at' => $this->created_at->diffForHumans()
        ];
    }
}
