<?php
//
//use App\Http\Controllers\TaskController;
//
//Route::prefix('tasks')->group(function () {
//
//
//
//    Route::get('/all', [TaskController::class, 'getStudentTasks']);
//    Route::get('{task}', [TaskController::class, 'getTaskDetails']);
//
//    Route::middleware('auth:student')->group(function () {
//        Route::post('/{task}/submit', [TaskController::class, 'submitTask']); // يجاوب
//    });
//
//    Route::middleware('auth:mentor')->group(function () {
//        Route::post('/new-task', [TaskController::class, 'createTask']);    // ينشئ Task
//
//        Route::get('/submissions', [TaskController::class, 'getTaskSubmissions']); #->middleware('can:get-task-pending');         // إجابات طلابه
//        Route::patch('/submissions/{submission}/review', [TaskController::class, 'reviewTask']);
//
//        Route::get('submissions/{submission}', [TaskController::class, 'getDetailsSubmission']);
//
//
//        Route::get('/count', [TaskController::class, 'totalTasks']); // إجمالي المهام
//        Route::get('/list', [TaskController::class, 'taskShowInList']);
//
//    });
//    Route::get('/{task}/submissions', [TaskController::class, 'getSubmissions']); #->middleware('can:get-task-submissions');         // إجابات Task معين
//
////    Route::patch('/submissions/{submission}/approve', [TaskController::class, 'approveSubmission'])->middleware('can:task-approve'); // يعتمد
////    Route::post('/submissions/{submission}/reject', [TaskController::class, 'rejectSubmission'])->middleware('can:task-reject');   // يرفض
//});
use App\Http\Controllers\TaskController;

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
