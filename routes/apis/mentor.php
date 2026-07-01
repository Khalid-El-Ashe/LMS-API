<?php

use App\Http\Controllers\MentorController;
use Illuminate\Support\Facades;

Route::prefix('mentor')->group(function () {

    Route::prefix('/auth')->group(function () {
        Route::post('/register', [MentorController::class, 'register']);
        Route::post('/login', [MentorController::class, 'login'])->middleware('last.active');
        Route::post('/logout', [MentorController::class, 'logout'])->middleware(['auth:sanctum']);
    });

    Route::post('/upload-profile-image', [MentorController::class, 'uploadProfileImage'])->middleware(['auth:mentor']);
    Route::post('/upload-multiple-files', [MentorController::class, 'uploadMultipleFiles'])->middleware(['auth:mentor']);
    Route::patch('update-mentor-information', [MentorController::class, 'updateMentorInformation'])->middleware(['auth:mentor']); #->middleware('can:mentor-information');
    Route::post('/enable-account/{mentor}', [MentorController::class, 'enableAccount'])->middleware(['auth:admin']);
    Route::post('/disable-account/{mentor}', [MentorController::class, 'disableAccount'])->middleware(['auth:admin']);
    Route::get('/dashboard', [MentorController::class, 'mentorDashboard'])->middleware(['auth:mentor']);
});
