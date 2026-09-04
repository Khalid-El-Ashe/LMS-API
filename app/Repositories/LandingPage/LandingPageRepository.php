<?php

namespace App\Repositories\LandingPage;

use App\Models\LandingPage;
use App\Models\SuccessStories;
use Illuminate\Support\Collection;

interface LandingPageRepository
{
    public function setLandingPageDataCounts(array $data): LandingPage;

    public function getLandingPageCounts();

    public function setGraduateProgramVideo(string $videoUrl): LandingPage;
    public function getGraduateProgramVideo(): ?string;
    public function getLandingPageCourses(): Collection;

    public function newSuccessStory(array $data): SuccessStories;
}
