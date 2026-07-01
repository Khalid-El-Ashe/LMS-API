<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseRequest;
use App\Models\Course;
use App\Models\CourseVideo;
use App\Repositories\Course\CourseRepository;
use App\Repositories\Course\Link\LinkRepository;
use App\Services\CourseLinksService;
use App\Services\CourseService;
use App\Services\VideoProgressService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class CourseController extends Controller
{

    public function __construct(
        private readonly CourseRepository     $courseRepo,
        private readonly CourseService        $courseService,
//        private TaskRepository     $taskModelRepository,
        private readonly CourseLinksService   $linkService,
        private readonly LinkRepository       $linkRepo,
        private readonly VideoProgressService $videoProgressService
    )
    {
    }


    public function createCourse(CourseRequest $request)
    {
        try {
            $this->courseService->createCourse($request->validated());
            return $this->success(null, 'Successfully Add new Course', 201);
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function getAllCourses()
    {
        try {
            $courses = $this->courseRepo->getAllCourses();
            return $this->success($courses);
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function getCourse(Course $course)
    {
        try {
            $course = $this->courseRepo->getCourse($course);
            return $this->success($course, null);
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
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

    public function updateCourse(CourseRequest $request, Course $course)
    {
        try {
            $course = $this->courseRepo->updateCourse($request->validated(), $course);
            return $this->success($course, null);
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function deleteCourse(Course $course)
    {
        try {
            $this->courseRepo->deleteCourse($course);
            return $this->success(null, 'Course deleted successfully');
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function restoreCourse(Course $course)
    {
        try {
            $this->courseRepo->restoreCourse($course);
            return $this->success(null, 'Course restored successfully');
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function forceDeleteCourse(Course $course)
    {
        try {
            $this->courseRepo->forceDeleteCourse($course);
            return $this->success(null, 'Course permanently deleted successfully');
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function newLinks($courseId, Request $request)
    {
        try {
            $links = $this->linkService->createLink($courseId, $request->all());
            return $this->success($links, null);
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function getLinksByCourse(Course $course)
    {
        try {
            $links = $this->linkRepo->getByCourse($course);
            return $this->success($links, null);
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function getCourseMentors($id)
    {
        try {
            $mentors = $this->courseRepo->getCourseMentors($id);
            return $this->success($mentors, null);
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function getCourseStudents($id)
    {
        try {
            $students = $this->courseRepo->getCourseStudents($id);
            return $this->success($students, null);
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

    /**
     * Video Progress Tracking
     */
    ####################################################################################################################
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
    ####################################################################################################################

}
