<?php

namespace App\Repositories\LandingPage;

use App\Models\Course;
use App\Models\LandingPage;
use App\Models\SuccessStories;
use Illuminate\Support\Collection;

class LandingPageModelRepository implements LandingPageRepository
{
    public function setLandingPageDataCounts(array $data): LandingPage
    {
        // Implementation for fetching landing page data counts
        return LandingPage::updateOrCreate([
            'id' => 1
        ], $data);
    }

    public function getLandingPageCounts()
    {
        return LandingPage::query()
            ->select([
                'graduates_count',
                'courses_count',
                'professional_trainer_count',
                'success_stories_count',
                'practical_projects_count',
            ])
            ->whereKey(1)
            ->firstOrFail();
    }

    public function setGraduateProgramVideo(string $videoUrl): LandingPage
    {
        return LandingPage::updateOrCreate(
            ['id' => 1],
            [
                'graduate_program_video_url' => $videoUrl,
            ]
        );
    }

    public function getGraduateProgramVideo(): ?string
    {
        return LandingPage::query()
            ->whereKey(1)
            ->value('graduate_program_video_url');
    }

    public function getLandingPageCourses(): Collection
    {
        return Course::query()
            ->select([
                'id',
                'name',
                'description',
            ])
            ->with([
                'videos:id,course_id,youtube_id',
            ])
            ->latest()
            ->limit(6)
            ->get();
    }

    public function newSuccessStory(array $data): SuccessStories
    {
        return SuccessStories::query()->create($data);
    }
}
