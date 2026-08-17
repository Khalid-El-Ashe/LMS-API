<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\VideController;
use Illuminate\Support\Facades\Route;


//Route::middleware('auth:sanctum')->group(function () {

Route::prefix('course')->group(function () {
    Route::get('all-course', [CourseController::class, 'getAllCourses']);
    Route::get('count', [CourseController::class, 'getAllCoursesCount']);

    Route::get('/{course}', [CourseController::class, 'getCourse']);
    Route::post('/create', [CourseController::class, 'createCourse'])->middleware(['auth:admin', 'can:create-course']);

    Route::get('/{course}/links/', [CourseController::class, 'getLinksByCourse']);
    Route::post('/{course}/new-links/', [CourseController::class, 'newLinks'])->middleware(['auth:mentor', 'can:create-link']);

    Route::get('/students', [CourseController::class, 'getCourseStudents']);
    Route::get('/{course}/mentors', [CourseController::class, 'getCourseMentors']);

    Route::put('/{course}', [CourseController::class, 'updateCourse'])->middleware(['can:update-course']);
    Route::delete('/{course}', [CourseController::class, 'deleteCourse'])->middleware(['can:delete-course']);
    Route::post('/restore/{course}', [CourseController::class, 'restoreCourse'])->middleware(['can:restore-course']);
    Route::delete('/force-delete/{course}', [CourseController::class, 'forceDeleteCourse'])->middleware(['can:force-delete-course']);
});

Route::prefix('videos')->group(function () {
    Route::post('/{video}/progress', [VideController::class, 'updateProgressPosition']);
    Route::post('/{video}/complete', [VideController::class, 'completeVideo']);
    Route::get('/{video}/resume', [VideController::class, 'resumeVideo']);
    Route::get('/mentor/course', [VideController::class, 'getAllVideosForCourseMentor'])->middleware(['auth:mentor']);
    Route::get('/details/{courseVideo}', [VideController::class, 'getVideoDetailsMentor']);

});

Route::post('/video/{courseVideos}/comment', [VideController::class, 'createComment']);
Route::get('/video/{courseVideo}', [VideController::class, 'getVideoDetails']);
//});
