<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware('last.active')->group(function () {

    Route::post('create-admin', [AdminController::class, 'createAdmin'])->middleware(['auth:admin', 'can:create-admin']);
    Route::post('login', [AdminController::class, 'loginAdmin']);
    Route::post('logout', [AdminController::class, 'logoutAdmin'])->middleware(['auth:admin']);
    Route::delete('delete/{admin}', [AdminController::class, 'deleteAdmin'])->middleware(['auth:admin', 'can:delete-admin']);

    Route::post('send-email-to-student/{student}', [AdminController::class, 'sendEmailToStudent'])->middleware(['auth:admin', 'can:send-email-to-student']);
    Route::post('send-email-to-all-students', [AdminController::class, 'sendEmailsToAllStudents'])->middleware(['auth:admin', 'can:send-email-to-all-students']);

    Route::post('send-email-to-mentor/{mentor}', [AdminController::class, 'sendEmailToMentor'])->middleware(['auth:admin', 'can:send-email-to-teacher']);
    Route::post('send-email-to-all-mentors', [AdminController::class, 'sendEmailsToAllMentors'])->middleware(['auth:admin', 'can:send-email-to-all-teachers']);
});
