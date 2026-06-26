<?php

namespace App\Services;

use App\Models\TaskSubmission;
use App\Repositories\Course\Task\TaskRepository;
use Exception;

/**
 * Submit Task
 * Approve Task
 * Reject Task
 * Grade Task
 */
class TaskSubmissionService
{
    protected $taskRepo;

    public function __construct(TaskRepository $taskRepo)
    {
        $this->taskRepo = $taskRepo;
    }

    # student submit
    public function submit(int $taskId, int $studentId, array $data)
    {

        return TaskSubmission::query()->updateOrCreate(
            [
                'task_id' => $taskId,
                'student_id' => $studentId,
            ],
            [
                'answer' => $data['answer'] ?? null,
                'file' => $data['file'] ?? null,
                'status' => 'pending',
            ]
        );
    }

    # اعتماد الحل من المدرب
    public function approve(TaskSubmission $submission, int $mentorId, ?int $grade = null, ?string $note = null)
    {
        $task = $submission->task;

        if ($task->passing_grade && $grade !== null && $grade < $task->passing_grade) {
            throw new Exception("Grade {$grade} is below passing grade {$task->passing_grade}");
        }

        $submission->update([
            'status' => 'approved',
            'grade' => $grade,
            'reviewed_by' => $mentorId,
            'reviewed_at' => now(),
            'review_notes' => $note,
        ]);

        return $submission->fresh();
    }

    # المدرب يرفض الطلب
    public function reject(TaskSubmission $submission, int $mentorId, string $notes)
    {

        $submission->update([
            'status' => 'rejected',
            'review_notes' => $notes,
            'reviewed_by' => $mentorId,
            'reviewed_at' => now(),
        ]);

        return $submission->fresh();
    }
}
