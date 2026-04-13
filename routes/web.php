<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('contacts.index');
});

Route::resource('contacts', \App\Http\Controllers\ContactController::class);
Route::resource('templates', \App\Http\Controllers\TemplateController::class);
Route::resource('assignments', \App\Http\Controllers\AssignmentController::class);
