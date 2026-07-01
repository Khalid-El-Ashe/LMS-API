<?php

use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;


//Route::middleware('auth:sanctum')->group(function () {

Route::prefix('course')->group(function () {
    Route::get('list', [CourseController::class, 'getAllCourses']);
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
    Route::post('/{video}/progress', [CourseController::class, 'updateProgressPosition']);
    Route::post('/{video}/complete', [CourseController::class, 'completeVideo']);
    Route::get('/{video}/resume', [CourseController::class, 'resumeVideo']);
    Route::get('/mentor/course', [CourseController::class, 'getAllVideosForCourseMentor'])->middleware(['auth:mentor']);
});
Route::post('/video/{courseVideos}/comment', [CourseController::class, 'createComment']);
Route::get('/video/{courseVideo}', [CourseController::class, 'getVideoDetails']);
//});
