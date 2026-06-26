<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware('last.active')->group(function () {

    Route::post('create-admin', [AdminController::class, 'createAdmin'])->middleware(['auth:sanctum', 'can:create-admin']);
    Route::post('login', [AdminController::class, 'loginAdmin'])->middleware('check.token.auth:admin');
    Route::post('logout', [AdminController::class, 'logoutAdmin'])->middleware(['auth:sanctum', 'check.token.redis']);
    Route::delete('delete/{admin}', [AdminController::class, 'deleteAdmin'])->middleware(['auth:sanctum', 'can:delete-admin', 'check.token.redis']);

    Route::post('send-email-to-student/{student}', [AdminController::class, 'sendEmailToStudent'])->middleware(['auth:sanctum', 'can:send-email-to-student']);
    Route::post('send-email-to-all-students', [AdminController::class, 'sendEmailsToAllStudents'])->middleware(['auth:sanctum', 'can:send-email-to-all-students']);

    Route::post('send-email-to-mentor/{mentor}', [AdminController::class, 'sendEmailToMentor'])->middleware(['auth:sanctum', 'can:send-email-to-teacher']);
    Route::post('send-email-to-all-mentors', [AdminController::class, 'sendEmailsToAllMentors'])->middleware(['auth:sanctum', 'can:send-email-to-all-teachers']);
});
