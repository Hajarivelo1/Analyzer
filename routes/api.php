<?php

use Illuminate\Support\Facades\Route;
use App\Models\Project;

/// routes/api.php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/projects', [ProjectController::class, 'index']);
});

