<?php

namespace App\Repositories\Course\Task;

use App\Models\CourseVideo;
use App\Models\Mentor;
use App\Models\Task;
use App\Models\TaskSubmission;

class TaskModelRepository implements TaskRepository
{

    public function create(array $data, Mentor $mentor)
    {
//        $mentor = auth()->guard('mentor')->user();

        $video = CourseVideo::query()->findOrFail(
            $data['video_id']
        );

        // Check ownership
        $mentor->courses()->where('courses.id', $video->course_id)
            ->firstOrFail();

        $data['course_id'] = $video->course_id;

        $data['order'] = Task::query()->where('video_id', $data['video_id'])
                ->max('order') + 1;

        return Task::query()->create($data);
    }

    public function getByCourse(int $courseId)
    {
        return Task::query()
            ->where('course_id', $courseId)
            ->orderBy('order')
            ->get();
    }

    public function getByVideo(int $videoId)
    {
        return Task::query()->where('video_id', $videoId)->orderBy('tasks.order')->get();
    }

    public function totalTasks(Mentor $mentor)
    {

        return Task::query()
            ->whereHas('course.mentors', fn($q) => $q->where('mentors.id', $mentor->id))->count();
    }

    public function taskShowInList(Mentor $mentor)
    {
        return Task::query()->select([
            'tasks.title',
            'tasks.video_id',
            'tasks.dead_line'
        ])
            ->whereHas('course.mentors', fn($q) => $q->where('mentors.id', $mentor->id))
            ->with('video:id,title')->orderBy('tasks.order', 'desc')->get()->map(function ($task) {
                return [
                    'title' => $task->title,
                    'video_title' => $task->video?->title,
                    'dead_line' => $task->dead_line,
                ];
            });
    }

    public function update(int $id, array $data)
    {
        $task = Task::query()->findOrFail($id);
        $task->update($data);
        return $task->fresh();
    }

    public function delete(int $id)
    {
        return Task::query()->findOrFail($id)->delete();
    }

    public function getSubmissions(int $taskId)
    {
        return TaskSubmission::query()->where('task_id', $taskId)->with('student', 'reviewer')->latest()->get();
    }

    public function getStudentSubmissions(int $mentorId)
    {
        return TaskSubmission::query()->whereHas('task.course.mentors', function ($query) use ($mentorId) {
            $query->where('mentor_id', $mentorId);
        })
            ->with('student', 'task', 'reviewer')->where('task_submissions.status', 'pending')
            ->latest()->get();
    }
}
