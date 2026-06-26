<?php

// ✅ Mentor Routes
use App\Http\Controllers\TaskController;

#,
Route::middleware(['last.active'])->group(function () {
    Route::prefix('tasks')->group(function () {
        Route::post('/new-task', [TaskController::class, 'createTask'])->middleware('auth:sanctum', 'can:create-task');                              // ينشئ Task
        Route::get('/submissions/pending', [TaskController::class, 'getPendingSubmissions'])->middleware('can:get-task-pending');         // إجابات طلابه
        Route::get('/{task}/submissions', [TaskController::class, 'getSubmissions'])->middleware('can:get-task-submissions');         // إجابات Task معين
        Route::patch('/submissions/{submission}/approve', [TaskController::class, 'approveSubmission'])->middleware('can:task-approve'); // يعتمد
        Route::post('/submissions/{submission}/reject', [TaskController::class, 'rejectSubmission'])->middleware('can:task-reject');   // يرفض
    });
});

// ✅ Student Routes
Route::middleware(['auth:student', 'last.active'])->group(function () {
    Route::prefix('tasks')->group(function () {
        Route::post('/{task}/submit', [TaskController::class, 'submitTask']); // يجاوب
    });
});
