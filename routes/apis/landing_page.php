<?php

use App\Http\Controllers\LandingPageController;
use Illuminate\Support\Facades\Route;

Route::prefix('landing-page')->middleware(['auth:admin'])->group(function () {

    Route::get('/get-counts', [LandingPageController::class, 'getLandingPageDataCounts']);
    Route::post('/counts', [LandingPageController::class, 'setLandingPageDataCounts']);

    /***************************************************** --- ******************************************************/

    Route::get('/graduate-program-video', [LandingPageController::class, 'getGraduateProgramVideo']);
    Route::post('/graduate-program-video', [LandingPageController::class, 'setGraduateProgramVideo']);

    /***************************************************** --- ******************************************************/

    Route::get('/courses', [LandingPageController::class, 'getLandingPageCourses']);

    /***************************************************** --- ******************************************************/

    Route::get('success-stories', [LandingPageController::class, 'getSuccessStories']);
    Route::post('success-stories', [LandingPageController::class, 'createNewSuccessStory']);
});
