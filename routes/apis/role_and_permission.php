<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;

Route::middleware(['auth:sanctum'])->group(function () {

    # Just the administrator is control

    // Roles
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('/accounts/{type}/{id}/assign', [RoleController::class, 'assignRole'])->middleware('can:assign-role');
        Route::delete('/accounts/{type}/{id}/revoke', [RoleController::class, 'revokeRole'])->middleware('can:revoke-role');
    });

    // Permissions
    Route::prefix('permissions')->group(function () {
        Route::get('/', [PermissionController::class, 'index']);
        Route::get('/accounts/{type}/{id}', [PermissionController::class, 'accountPermissions']);
        Route::post('/accounts/{type}/{id}/assign', [PermissionController::class, 'assignPermission'])->middleware('can:assign-permission');
        Route::delete('/accounts/{type}/{id}/revoke', [PermissionController::class, 'revokePermission'])->middleware('can:revoke-permission');
    });

});
