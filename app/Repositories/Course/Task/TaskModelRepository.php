<?php

namespace App\Repositories\Course\Task;

use App\Models\CourseVideo;
use App\Models\Mentor;
use App\Models\Student;
use App\Models\Task;
use App\Models\TaskSubmission;
use Illuminate\Database\Eloquent\Collection;

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

//    public function taskShowInListToStudent(Student $student)
//    {
//        return Task::query()->select([
//            'tasks.title',
//            'tasks.video_id',
//            'tasks.dead_line'
//        ])
//            ->whereHas('course.students', fn($q) => $q->where('students.id', $student->id))
//            ->with('video:id,title')->orderBy('tasks.order', 'desc')->get()->map(function ($task) {
//                return [
//                    'title' => $task->title,
//                    'video_title' => $task->video?->title,
//                    'dead_line' => $task->dead_line,
//                ];
//            });
//    }

    /**
     * @param Student $student
     * @return Collection
     *
     * In this function need return all tasks by answers if found
     */
    public function getStudentTasks(Student $student)
    {
        $tasks = Task::query()->select([
            'tasks.title',
            'tasks.course_id',
            'tasks.video_id',
            'tasks.description',
            'tasks.dead_line',
            'tasks.created_at'
        ])
            ->whereIn(
                'course_id',
                $student->courses()->pluck('courses.id')
            )
            ->with([
//                'course:id,name',
                'video:id,title',
                'submissions' => function ($query) use ($student) {
                    $query->where('student_id', $student->id)
                        ->select([
                            'id',
                            'task_id',
//                            'answer',
                            'file',
                            'grade',
                            'reviewed_at',
                            'review_notes',
                            'created_at'
                        ]);
                }
            ])
            ->orderBy('order')
            ->latest()
            ->get();


        return $tasks;
    }

    /**
     * @param Student $student
     * @return Collection
     *
     * in here I need to return all tasks that answered by the student
     */
    public function getAnsweredTasks(Student $student)
    {
        return Task::query()
            ->whereIn(
                'course_id',
                $student->courses()->pluck('courses.id')
            )
            ->whereHas('submissions', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->with([
                'submissions' => function ($query) use ($student) {
                    $query->where('student_id', $student->id)
                        ->select([
                            'id',
                            'task_id',
//                            'answer',
                            'file',
                            'grade',
                            'reviewed_at',
                            'review_notes',
                            'created_at'
                        ]);
                },
                'video:id,title'
            ])
            ->orderBy('order')
            ->latest()
            ->get();
    }

    /**
     * @param Student $student
     * @return Collection
     *
     * in here I need to return all tasks that not answered
     */
    public function getUnansweredTasks(Student $student)
    {
        return Task::query()
            ->whereIn(
                'course_id',
                $student->courses()->pluck('courses.id')
            )
            ->whereDoesntHave('submissions', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->with([
                'video:id,title'
            ])
            ->orderBy('order')
            ->latest()
            ->get();
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
        return TaskSubmission::query()
            ->select([
                'id',
                'student_id',
//                'answer',
                'file',
                'grade',
                'reviewed_by',
                'reviewed_at',
                'created_at',
            ])
            ->where('task_id', $taskId)->with('student:id,full_name', 'reviewer:id,name')
            ->latest()
            ->get();
    }

    public function getDetailsSubmission(TaskSubmission $submissionId)
    {
        return TaskSubmission::query()->select([
            'task_submissions.id',
            'task_submissions.student_id',
//            'task_submissions.answer',
            'task_submissions.file',
            'task_submissions.grade',
            'task_submissions.review_notes',

        ])
            ->with([
                'student:id,full_name'
            ])
            ->findOrFail($submissionId->id);
    }

    public function getStudentSubmissions(int $mentorId)
    {
        return TaskSubmission::query()
            ->select([
                'id',
                'task_id',
                'student_id',
                'grade',
                'reviewed_by',
                'created_at'
            ])
            ->whereHas('task.course.mentors', function ($query) use ($mentorId) {
                $query->where('mentor_id', $mentorId);
            })
            ->with('student', 'task', 'reviewer')
//            ->where('task_submissions.status', 'pending')
            ->with([
                'student:id,full_name',
                'task:id,title',
                'reviewer:id,name'
            ])
            ->latest()->get();
    }
}
