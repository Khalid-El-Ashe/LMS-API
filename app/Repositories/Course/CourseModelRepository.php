<?php

namespace App\Repositories\Course;

use App\Http\Requests\CourseRequest;
use App\Http\Resources\Course\AllCourseResource;
use App\Http\Resources\Course\CourseCommentsResource;
use App\Http\Resources\Course\CourseResource;
use App\Models\Course;
use App\Models\CourseVideo;
use App\Models\Mentor;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

class CourseModelRepository implements CourseRepository
{
    /**
     * @return AnonymousResourceCollection
     * This function is get All Course Public Data (info of the Course)
     */
    public function getAllCourses(): AnonymousResourceCollection
    {
        //     ->with(['user:id,name,email', 'category:id,name', 'tags:id,name'])

        $page = request('page', 1);
        # I need using the Cache to make a Performance get Data
        /**
         * (::remember)
         * Parameters: string|UnitEnum $key
         * Closure|DateInterval|DateTimeInterface|int|null $ttl
         * Closure():|TCacheValue $callback
         * Returns: TCacheValu
         */
        $courses = Cache::tags(['courses'])->remember(
            "courses_page_{$page}",
            3600,
            fn() => Course::latest()->paginate(10)
        );
        return AllCourseResource::collection($courses);
    }

    /**
     * @return CourseResource
     * In the First I was Make a Problem I Get The Course by All Data, That is Reflect
     * So i need to Cut the Endpoints
     */
    # 1- Get Course
    public function getCourse(Course $course)
    {
        $page = request('page', 1);
        $key = "course_{$course->id}_page_" . $page;
        return Cache::tags(['courses'])->remember($key, 3600, function () use ($course) {
            //            return Course::with('videos')->with('links')->with('comments')->findOrFail($course->id);
            //            return $course->load(['videos', 'links'])->append(['students_count', 'mentors_count']);
            //            return $course->load(['videos.comments.user', 'links']);
            $course->load(
                [
                    'videos' => function ($query) {
                        $query->orderBy('order');
                    },
                    'links'
                ]
            );
            return new CourseResource($course);
        });
        //        return Course::with('videos')->with('links')->with('comments')->findOrFail($course->id);
    }

    public function getAllVideosForCourseMentor(Mentor $mentor)
    {
        # get All videos for this course is mentor have
        return CourseVideo::query()
            ->select([
                'id',
                'title'
            ])
            ->whereHas('course.mentors', function ($query) use ($mentor) {
                $query->where('mentors.id', $mentor->id);
            })->orderBy('order', 'asc')->get()->map(function ($video) {
                return [
                    'id' => $video->id,
                    'video_title' => $video->title,
                ];
            });
    }

    public function getVideoDetails(CourseVideo $video): CourseVideo
    {
        $video->load([
            'comments.user',
            'tasks'
        ]);

        $video->setAttribute(
            'previous_video',
            $video::previousVideo()
        );

        $video->setAttribute(
            'next_video',
            $video::nextVideo()
        );

        return $video;
    }

    # 4- Get Course Comments
    public function getVideoComments(CourseVideo $courseVideo): AnonymousResourceCollection
    {
        $page = request('page', 1);
        $key = "courseVideo_{$courseVideo->id}_comments_page_{$page}";
        return Cache::tags(['courses:comments'])->remember($key, 3600, function () use ($courseVideo) {
            $comments = $courseVideo->comments()->with('user')->first()->paginate(10);
            return CourseCommentsResource::collection($comments);
        });
    }

    public function getCourseStudents(Course $course)
    {
        $page = request()->get('page', 1);
        $key = "course_{$course->id}_students_page_{$page}";
        return Cache::tags(['courses'])->remember($key, 3600, function () use ($course) {
            return $course->students()->paginate(10);
        });

        //        return $this->getCourse($course->id)->students()->paginate(10);
    }

    public function getCourseMentors(Course $course)
    {
        $page = request()->get('page', 1);
        $key = "course_{$course->id}_mentors_page_{$page}";
        return Cache::tags(['courses'])->remember($key, 3600, function () use ($course) {
            return $course->mentors()->paginate(10);
        });

        //        return $this->getCourse($course->id)->mentors()->paginate(10);
    }

    public function createCourse(array $data)
    {
        Cache::tags(['courses'])->flush();
        return Course::query()->create($data);
    }


    public function updateCourse(CourseRequest $request, Course $course)
    {
        // now I need to update the course
        Cache::tags(['courses'])->flush();
        return $course->update($request->validated());
    }

    public function deleteCourse(Course $course)
    {
        Cache::tags(['courses'])->flush();
        $course = Course::query()->findOrFail($course->id);
        return $course->delete();
    }

    public function restoreCourse(Course $course)
    {
        Cache::tags(['courses'])->flush();
        $course = Course::onlyTrashed()->findOrFail($course->id);
        return $course->restore();
    }

    public function forceDeleteCourse(Course $course)
    {
        Cache::tags(['courses'])->flush();
        $course = Course::withTrashed()->find($course->id);
        return $course->forceDelete();
    }

    public function createComment(array $data, CourseVideo $courseVideo): array
    {
        Cache::tags(['courses:comments'])->flush();
        $user = request()->user();
        if (!$user) {
            throw new InvalidArgumentException('User must be authenticated to create a comment');
        }


        $comment = $courseVideo->comments()->createOrFirst([
            'body' => $data['body'],
            'user_id' => $user->id,
            'user_type' => get_class($user),
            'course_video_id' => $courseVideo->id,
        ]);

        return [
            'comment' => $comment,
            'course_id' => $courseVideo->course_id,
        ];
    }
}
