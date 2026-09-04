<?php

namespace App\Http\Controllers;

use App\Http\Requests\LandingPageRequestCount;
use App\Http\Requests\LandingPageRequestVideURL;
use App\Http\Requests\SuccessStoryRequest;
use App\Http\Resources\LandingPageCountsResource;
use App\Http\Resources\LandingPageCourseResource;
use App\Http\Resources\SuccessStoryResource;
use App\Models\SuccessStories;
use App\Repositories\LandingPage\LandingPageRepository;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;

class LandingPageController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected readonly LandingPageRepository $landingPageRepository) {}

    public function setLandingPageDataCounts(LandingPageRequestCount $requestCount)
    {
        try {
            $validatedData = $requestCount->validated();
            $this->landingPageRepository->setLandingPageDataCounts($validatedData);

            return $this->success('Landing page data counts retrieved successfully.');
        } catch (Exception $e) {
            return $this->error('Failed to get landing page data counts.' . $e->getMessage(), 500);
        }
    }

    public function getLandingPageDataCounts(): ?JsonResponse
    {
        try {
            $landingPageData = $this->landingPageRepository->getLandingPageCounts();
            return $this->success(new LandingPageCountsResource($landingPageData), 'Landing page data counts retrieved successfully.');
        } catch (Exception $e) {
            return $this->error('Failed to get landing page data counts.' . $e->getMessage(), 500);
        }
    }

    public function setGraduateProgramVideo(LandingPageRequestVideURL $request)
    {
        try {
            $validatedData = $request->validated();
            $this->landingPageRepository->setGraduateProgramVideo($validatedData['graduate_program_video_url']);

            return $this->success('Graduate program video URL updated successfully.');
        } catch (Exception $e) {
            return $this->error('Failed to update graduate program video URL.' . $e->getMessage(), 500);
        }
    }

    public function getGraduateProgramVideo(): ?string
    {
        try {
            $videoUrl = $this->landingPageRepository->getGraduateProgramVideo();
            return $this->success($videoUrl);
        } catch (Exception $e) {
            return $this->error('Failed to get graduate program video URL.' . $e->getMessage(), 500);
        }
    }

    public function getLandingPageCourses()
    {
        try {
            $courses = $this->landingPageRepository
                ->getLandingPageCourses();

            return $this->success(
                LandingPageCourseResource::collection($courses),
                'Landing page courses retrieved successfully.'
            );
        } catch (Exception $e) {
            return $this->error(
                'Failed to get landing page courses.' . $e->getMessage(),
                500
            );
        }
    }

    public function createNewSuccessStory(SuccessStoryRequest $request)
    {
        try {
            $validatedData = $request->validated();
            if ($request->hasFile('image')) {
                $validatedData['image'] = $request->file('image')
                    ->store('success-stories', 'public');
            }

            $successStory = $this->landingPageRepository->newSuccessStory($validatedData);

            return $this->success(new SuccessStoryResource($successStory), 'Success story created successfully.');
        } catch (Exception $e) {
            return $this->error('Failed to create success story.' . $e->getMessage(), 500);
        }
    }

    public function getSuccessStories()
    {
        $successStories = SuccessStories::query()->get();
        return $this->success(SuccessStoryResource::collection($successStories), 'Success stories retrieved successfully.');
    }
}
