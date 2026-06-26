<?php

use App\Http\Controllers\MentorController;
use Illuminate\Support\Facades;

Route::prefix('mentor')->group(function () {

    Route::prefix('/auth')->group(function () {
        Route::post('/register', [MentorController::class, 'register']);
        Route::post('/login', [MentorController::class, 'login']);
        Route::post('/logout', [MentorController::class, 'logout'])->middleware(['auth:sanctum']);
    });

    Route::middleware(['auth:sanctum', 'last.active'])->group(function () {
        Route::post('/upload-profile-image/{mentor}', [MentorController::class, 'uploadProfileImage'])->middleware('can:uppload-profile-image');
        Route::post('/upload-multiple-files/{mentor}', [MentorController::class, 'uploadMultipleFiles'])->middleware('can:upload-files');
        Route::post('mentor-information/{mentor}', [MentorController::class, 'mentorInformation']); #->middleware('can:mentor-information');
        Route::post('/enable-account/{mentor}', [MentorController::class, 'enableAccount'])->middleware('can:enable-mentor');
        Route::post('/disable-account/{mentor}', [MentorController::class, 'disableAccount'])->middleware('can:disable-mentor');
    });
});
