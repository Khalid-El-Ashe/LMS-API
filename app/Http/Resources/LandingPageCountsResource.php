<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LandingPageCountsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'graduates' => [
                'label' => 'عدد الخريجين',
                'count' => $this->graduates_count,
            ],

            'courses' => [
                'label' => 'عدد الدورات',
                'count' => $this->courses_count,
            ],

            'professional_trainers' => [
                'label' => 'عدد المدربين',
                'count' => $this->professional_trainer_count,
            ],

            'success_stories' => [
                'label' => 'عدد قصص النجاح',
                'count' => $this->success_stories_count,
            ],

            'practical_projects' => [
                'label' => 'عدد المشاريع العملية',
                'count' => $this->practical_projects_count,
            ],
        ];
    }
}
