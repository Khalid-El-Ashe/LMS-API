<?php
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::prefix('tasks')->group(function () {

    // =========================
    // Static Routes
    // =========================

    Route::get('/count', [TaskController::class, 'totalTasks'])->middleware(['auth:mentor']);

    Route::get('/list', [TaskController::class, 'taskShowInList'])->middleware(['auth:mentor']);

    Route::get('/all', [TaskController::class, 'getStudentTasks'])->middleware(['auth:student']);

    Route::get('/submissions', [TaskController::class, 'getTaskSubmissions'])->middleware(['auth:mentor']);


    // =========================
    // Task Details
    // =========================

    Route::get('/{task}', [TaskController::class, 'getDetailsTask']);


    // =========================
    // Task Actions
    // =========================

    Route::post('/new-task', [TaskController::class, 'createTask'])->middleware(['auth:mentor']);

    Route::post('/{task}/submit', [TaskController::class, 'submitTask'])->middleware(['auth:student']);

    Route::get('/{task}/submissions', [TaskController::class, 'getSubmissions']);


    // =========================
    // Submission Actions
    // =========================

    Route::get('/submissions/{submission}', [TaskController::class, 'getDetailsSubmission'])->middleware(['auth:mentor']);

    Route::patch('/submissions/{submission}/review', [TaskController::class, 'reviewTask'])->middleware(['auth:mentor']);
});
