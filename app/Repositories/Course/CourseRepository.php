<?php

namespace App\Repositories\Course;

use App\Http\Requests\CourseRequest;
use App\Models\Comment;
use App\Models\Course;
use App\Models\CourseVideo;

interface CourseRepository
{
    public function getAllCourses();

    public function getCourse(Course $course);
    public function getVideoDetails(CourseVideo $video);
    public function getVideoComments(CourseVideo $courseVideo);
    public function getCourseStudents(Course $course);
    public function getCourseMentors(Course $course);
    public function createCourse(array $data);
    public function updateCourse(CourseRequest $request, Course $course);
    public function deleteCourse(Course $course);
    public function restoreCourse(Course $course);
    public function forceDeleteCourse(Course $course);
    public function createComment(array $data, CourseVideo $courseVideo): array;
}
