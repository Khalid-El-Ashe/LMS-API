<?php

namespace App\Services;

use App\Models\Mentor;
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
        $filePath = null;

        if (isset($data['file'])) {
            $filePath = $data['file']->store('tasks/submissions', 'public');
        }

        return TaskSubmission::query()->updateOrCreate(
            [
                'task_id' => $taskId,
                'student_id' => $studentId,
            ],
            [
                'answer' => $data['answer'] ?? null,
                'file' => $filePath,
//                'status' => 'pending',
                'grade' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_notes' => null,
            ]
        );
    }

    public function review(TaskSubmission $submission, Mentor $mentor, array $data)
    {
        $submission->update([
            'grade' => $data['grade'],
            'reviewed_by' => $mentor->id,
            'reviewed_at' => now(),
            'review_notes' => $data['review_notes'] ?? null,
        ]);

        $submission->refresh();

        return $submission->load([
            'student',
            'reviewer'
        ]);
    }

    # اعتماد الحل من المدرب
//    public function approve(TaskSubmission $submission, int $mentorId, ?int $grade = null, ?string $note = null)
//    {
//        $task = $submission->task;
//
//        if ($task->passing_grade && $grade !== null && $grade < $task->passing_grade) {
//            throw new Exception("Grade {$grade} is below passing grade {$task->passing_grade}");
//        }
//
//        $submission->update([
////            'status' => 'approved',
//            'grade' => $grade,
//            'reviewed_by' => $mentorId,
//            'reviewed_at' => now(),
//            'review_notes' => $note,
//        ]);
//
//        return $submission->fresh();
//    }

    # المدرب يرفض الطلب
//    public function reject(TaskSubmission $submission, int $mentorId, string $notes)
//    {
//
//        $submission->update([
////            'status' => 'rejected',
//            'review_notes' => $notes,
//            'reviewed_by' => $mentorId,
//            'reviewed_at' => now(),
//        ]);
//
//        return $submission->fresh();
//    }
}
