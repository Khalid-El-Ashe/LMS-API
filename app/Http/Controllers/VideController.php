<?php

namespace App\Http\Controllers;

use App\Http\Resources\Course\VideoResource;
use App\Models\CourseVideo;
use App\Repositories\Course\CourseRepository;
use App\Services\CourseService;
use App\Services\VideoProgressService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class VideController extends Controller
{
    public function __construct(
        private readonly CourseRepository     $courseRepo,
        private readonly CourseService        $courseService,
        private readonly VideoProgressService $videoProgressService,

    )
    {
    }

    public function getAllVideosForCourseMentor()
    {
        $mentor = auth()->guard('mentor')->user();
        try {
            $videos = $this->courseRepo->getAllVideosForCourseMentor($mentor);
            return $this->success(data: $videos, code: ResponseAlias::HTTP_OK);
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function getVideoDetails(CourseVideo $courseVideo)
    {
        try {
            $courseVideo = $this->courseService->getVideoDetails($courseVideo);
            return $this->success($courseVideo, null);
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function getVideoDetailsMentor(CourseVideo $courseVideo)
    {
        try {
            $details = new VideoResource($courseVideo);
            return $this->success($details, null);
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }

    }

    public function createComment(Request $request, CourseVideo $courseVideos)
    {
        try {
            $comment = $this->courseRepo->createComment($request->all(), $courseVideos);
            return $this->success($comment, 'Comment added successfully', 201);
        } catch (ValidationException $th) {
            return $this->error($th->getMessage());
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function updateProgressPosition(Request $request, CourseVideo $video)
    {
        try {
            $request->validate([
                'position' => ['required', 'integer', 'min:0'],
                'watched_seconds' => ['required', 'integer', 'min:0'],
            ]);

            $this->videoProgressService->updateProgressPosition(
                auth()->guard('student')->user()->id,
                $video->id,
                $request->position,
                $request->watched_seconds
            );

            return $this->success(null, 'Progress updated');
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function completeVideo(CourseVideo $video)
    {
        try {
            $completed = $this->videoProgressService->completeVideo(
                auth()->guard('student')->user()->id,
                $video->id
            );

            return $this->success($completed, 'Video completed');
        } catch (Throwable $th) {
            $status = method_exists($th, 'status') ? $th->status() : ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
            return $this->error($th->getMessage(), $status);
        }
    }

    public function resumeVideo(CourseVideo $video)
    {
        $progress = $this->videoProgressService->resumeVideo(
            auth()->guard('student')->id(),
            $video->id
        );


        return $this->success(
            $progress,
            'Resume position retrieved'
        );
    }
}
