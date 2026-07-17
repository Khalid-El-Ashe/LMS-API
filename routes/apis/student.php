<?php

use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::prefix('students')->group(function () {

    /**
     * AUTH ROUTES
     */
    Route::prefix('auth')->group(function () {
        Route::post('/register', [StudentController::class, 'register']);
        Route::post('/login', [StudentController::class, 'login']); # 'check.token.auth:student',
        Route::post('/logout', [StudentController::class, 'logout'])->middleware('auth:student'); # 'check.token.redis'
    });

    /**
     * STUDENT SELF ROUTES
     */
    Route::middleware(['auth:student'])->group(function () {
        Route::get('/', [StudentController::class, 'getAllStudents'])->middleware('auth:admin');

        Route::get('/student-profile', [StudentController::class, 'getStudentProfile']);
        Route::get('/student-path', [StudentController::class, 'path']);
        Route::patch('/update-student-data', [StudentController::class, 'updateStudentData']);
//        Route::put('/update', [StudentController::class, 'updateStudentInfo']);

        Route::get('/search', [StudentController::class, 'searchStudents']);

        Route::post('/upload-profile-image', [StudentController::class, 'uploadProfileImage']);
        Route::post('/upload-multiple-files', [StudentController::class, 'uploadMultipleFiles']);

        Route::post('/enableAccount/{student}', [StudentController::class, 'enableStudent'])->middleware('can:enable-student');
        Route::post('/disableAccount/{student}', [StudentController::class, 'disableStudent'])->middleware('can:disable-student');

        Route::delete('/delete/{student}', [StudentController::class, 'deleteStudent'])->middleware('can:delete-student');
        Route::post('/restore/{student}', [StudentController::class, 'restoreStudent'])->middleware('can:restore-student');
        Route::delete('/forceDelete/{student}', [StudentController::class, 'forceDeleteStudent'])->middleware('can:force-delete-student');
        Route::get('/all_trashed', [StudentController::class, 'getAllStudentsIsTrashed'])->middleware('can:show-trashed-students');

        Route::get('/filter', [StudentController::class, 'filterStudents']);

    });
});
Route::get('/mentor/students', [StudentController::class, 'getAllStudentsForMentor'])->middleware('auth:mentor');
Route::get('/mentor/students/{student}', [StudentController::class, 'getStudentProfileForMentor'])->middleware('auth:mentor');
