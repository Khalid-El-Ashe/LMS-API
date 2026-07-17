<?php

namespace App\Http\Resources\Student;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentTaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $submission = $this->submissions->first();
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'deadline' => $this->dead_line,

            'is_submitted' => !is_null($submission),

            'submission' => $submission ? [
                'id' => $submission->id,
                'answer' => $submission->answer,
                'file' => $submission->file
                    ? asset('storage/' . $submission->file)
                    : null,

                'grade' => $submission->grade,
                'review_notes' => $submission->review_notes,
                'reviewed_at' => $submission->reviewed_at,
                'submitted_at' => $submission->created_at,
            ] : 'no submission found',
        ];
    }
}
