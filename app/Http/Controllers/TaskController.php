<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewTaskRequest;
use App\Http\Requests\SubmitTaskRequest;
use App\Http\Requests\TaskRequest;
use App\Models\Mentor;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Repositories\Course\Task\TaskRepository;
use App\Services\TaskSubmissionService;
use Illuminate\Http\Request;
use Throwable;

class TaskController extends Controller
{
    # Dependency Injection
    public function __construct(private readonly TaskRepository $taskRepo, private readonly TaskSubmissionService $submissionService)
    {
    }

    public function createTask(TaskRequest $request)
    {
        try {

            $data = $request->validated();
            $mentor = auth()->guard('mentor')->user();
            $task = $this->taskRepo->create(data: $data, mentor: $mentor);

            return $this->success($task, 'Task created successfully', 201);
        } catch (Throwable $th) {
            $statusCode = method_exists($th, 'getStatusCode') ? $th->getStatusCode() : 500;
            return $this->error($th->getMessage(), $statusCode);
        }
    }

    public function submitTask(SubmitTaskRequest $request, Task $task)
    {
        try {
            $submission = $this->submissionService->submit($task->id, auth()->guard('student')->id(), $request->validated());
            return $this->success($submission, 'Task submitted successfully', 200);
        } catch (Throwable $th) {
            $statusCode = method_exists($th, 'getStatusCode') ? $th->getStatusCode() : 500;
            return $this->error($th->getMessage(), $statusCode);
        }
    }

    public function reviewTask(ReviewTaskRequest $request, TaskSubmission $submission)
    {
        $mentor = auth()->guard('mentor')->user();
        try {
            $reviewedSubmission = $this->submissionService->review($submission, $mentor, $request->validated());
            return $this->success($reviewedSubmission, 'Task reviewed successfully', 200);
        } catch (Throwable $th) {
            $statusCode = method_exists($th, 'getStatusCode') ? $th->getStatusCode() : 500;
            return $this->error($th->getMessage(), $statusCode);
        }

    }

    public function getSubmissions(Task $task)
    {
        try {
            $submissions = $this->taskRepo->getSubmissions($task->id);
            return $this->success($submissions, null, 200);
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function getTaskSubmissions()
    {
        try {
            $submissions = $this->taskRepo->getStudentSubmissions(auth()->guard('mentor')->id());
            return $this->success($submissions, null, 200);
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

//    public function approveSubmission(TaskRequest $request, TaskSubmission $submission)
//    {
//        try {
//            $approvedSubmission = $this->submissionService->approve($submission, auth()->guard('mentor')->id(), $request->input('grade'), $request->input('note'));
//            return $this->success($approvedSubmission, 'Submission approved successfully', 200);
//        } catch (Throwable $th) {
//            return $this->error($th->getMessage());
//        }
//    }

//    public function rejectSubmission(Request $request, TaskSubmission $submission)
//    {
//        try {
//            $rejectedSubmission = $this->submissionService->reject($submission, auth()->guard('mentor')->id(), $request->input('notes'));
//            return $this->success($rejectedSubmission, 'Submission rejected successfully', 200);
//        } catch (Throwable $th) {
//            return $this->error($th->getMessage());
//        }
//    }

    public function totalTasks()
    {
        $mentor = auth()->guard('mentor')->user();
        $total = $this->taskRepo->totalTasks($mentor);
        return $this->success(['total' => $total], null, 200);
    }

    public function taskShowInList()
    {
        $mentor = auth()->guard('mentor')->user();
        $tasks = $this->taskRepo->taskShowInList($mentor);
        return $this->success($tasks, null, 200);
    }
}
