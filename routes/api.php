<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\CountryCodeController;

require __DIR__ . '/apis/mentor.php';
require __DIR__ . '/apis/student.php';
require __DIR__ . '/apis/course.php';
require __DIR__ . '/apis/admin.php';
require __DIR__ . '/apis/role_and_permission.php';
require __DIR__ . '/apis/task.php';

Route::get('/code-number', [CountryCodeController::class, 'index']);
Route::get('/university-list', [Controller::class, 'universityList']);
Route::get('/major-list', [Controller::class, 'majorList']);
Route::get('/states-list', [CountryCodeController::class, 'statesList']);
