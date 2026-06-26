<?php

namespace App\Repositories\Course\Task;

use App\Models\Mentor;

interface TaskRepository
{
    public function create(array $data, Mentor $mentor);
    public function getByCourse(int $courseId);
    public function getByVideo(int $videoId);
    public function update(int $id, array $data);
    public function delete(int $id);

    public function getSubmissions(int $taskId);
    public function getStudentSubmissions(int $mentorId);

}
