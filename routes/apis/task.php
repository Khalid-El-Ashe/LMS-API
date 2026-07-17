<?php

use App\Http\Controllers\TaskController;

Route::prefix('tasks')->group(function () {
    Route::post('/new-task', [TaskController::class, 'createTask'])->middleware(['auth:mentor']);    // ينشئ Task
    Route::post('/{task}/submit', [TaskController::class, 'submitTask'])->middleware(['auth:student']); // يجاوب

    Route::get('/submissions', [TaskController::class, 'getTaskSubmissions'])->middleware(['auth:mentor']); #->middleware('can:get-task-pending');         // إجابات طلابه
    Route::patch('/submissions/{submission}/review', [TaskController::class, 'reviewTask'])->middleware(['auth:mentor']);

    Route::get('submissions/{submission}', [TaskController::class, 'getDetailsSubmission'])->middleware(['auth:mentor']);
    Route::get('/{task}/submissions', [TaskController::class, 'getSubmissions']); #->middleware('can:get-task-submissions');         // إجابات Task معين

//    Route::patch('/submissions/{submission}/approve', [TaskController::class, 'approveSubmission'])->middleware('can:task-approve'); // يعتمد
//    Route::post('/submissions/{submission}/reject', [TaskController::class, 'rejectSubmission'])->middleware('can:task-reject');   // يرفض

    Route::get('/count', [TaskController::class, 'totalTasks'])->middleware(['auth:mentor']); // إجمالي المهام
    Route::get('/list', [TaskController::class, 'taskShowInList'])->middleware(['auth:mentor']);
    Route::get('/all', [TaskController::class, 'getStudentTasks'])->middleware(['auth:student']);
});
