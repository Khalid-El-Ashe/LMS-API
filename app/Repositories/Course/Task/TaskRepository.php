<?php

namespace App\Repositories\Course\Task;

use App\Models\Mentor;
use App\Models\Student;
use App\Models\TaskSubmission;

interface TaskRepository
{
    public function create(array $data, Mentor $mentor);

    public function getByCourse(int $courseId);

    public function getByVideo(int $videoId);

    public function update(int $id, array $data);

    public function delete(int $id);

    public function totalTasks(Mentor $mentor);

    public function taskShowInList(Mentor $mentor);

//    public function taskShowInListToStudent(Student $student);
    public function getStudentTasks(Student $student);

    public function getAnsweredTasks(Student $student);

    public function getUnansweredTasks(Student $student);

    public function getSubmissions(int $taskId);
    public function getDetailsSubmission(TaskSubmission $submissionId);

    public function getStudentSubmissions(int $mentorId);

}
