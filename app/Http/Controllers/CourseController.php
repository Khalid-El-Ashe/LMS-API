<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseRequest;
use App\Models\Course;
use App\Models\CourseVideo;
use App\Repositories\Course\CourseRepository;
use App\Repositories\Course\Link\LinkRepository;
use App\Services\CourseLinksService;
use App\Services\CourseService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class CourseController extends Controller
{

    public function __construct(
        private readonly CourseRepository   $courseRepo,
        private readonly CourseService      $courseService,
//        private TaskRepository     $taskModelRepository,
        private readonly CourseLinksService $linkService,
        private readonly LinkRepository     $linkRepo,
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

    public function getAllCoursesCount()
    {
        $count = Course::query()->count();
        return $this->success($count, null);
    }
}
