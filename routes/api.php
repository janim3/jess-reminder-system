<?php

use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::apiResource('contacts', \App\Http\Controllers\Api\ContactController::class);
    Route::apiResource('templates', \App\Http\Controllers\Api\TemplateController::class);
    Route::apiResource('assignments', \App\Http\Controllers\Api\AssignmentController::class);
});
