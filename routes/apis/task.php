<?php

// ✅ Mentor Routes
use App\Http\Controllers\TaskController;

#,
Route::prefix('tasks')->group(function () {
    Route::post('/new-task', [TaskController::class, 'createTask'])->middleware(['auth:mentor']);    // ينشئ Task
    Route::post('/{task}/submit', [TaskController::class, 'submitTask'])->middleware(['auth:student']); // يجاوب
    Route::patch('/submissions/{submission}/review', [TaskController::class, 'reviewTask'])->middleware(['auth:mentor']);

    Route::get('/submissions/pending', [TaskController::class, 'getPendingSubmissions']); #->middleware('can:get-task-pending');         // إجابات طلابه
    Route::get('/{task}/submissions', [TaskController::class, 'getSubmissions']); #->middleware('can:get-task-submissions');         // إجابات Task معين

//    Route::patch('/submissions/{submission}/approve', [TaskController::class, 'approveSubmission'])->middleware('can:task-approve'); // يعتمد
//    Route::post('/submissions/{submission}/reject', [TaskController::class, 'rejectSubmission'])->middleware('can:task-reject');   // يرفض

    Route::get('/count', [TaskController::class, 'totalTasks'])->middleware(['auth:mentor']); // إجمالي المهام
    Route::get('/list', [TaskController::class, 'taskShowInList'])->middleware(['auth:mentor']);

})->middleware(['last.active']);
