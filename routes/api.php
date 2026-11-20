<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\SubjectController;
use App\Http\Controllers\Api\V1\ModuleController;
use App\Http\Controllers\Api\V1\LabClassController;
use App\Http\Controllers\Api\V1\SubmissionController;

Route::get('/v1/health', fn () => response()->json([
    'status' => 'ok',
    'app' => config('app.name'),
    'time' => now()->toIso8601String(),
]));

Route::prefix('v1')->middleware(['throttle:api', 'api.key'])->group(function () {
    Route::apiResource('subjects', SubjectController::class);
    Route::apiResource('modules', ModuleController::class);
    Route::apiResource('classes', LabClassController::class)->parameters(['classes' => 'labClass']);

    Route::get('subjects/{subject}/modules', [SubjectController::class, 'modules']);

    Route::post('submissions/bulk', [SubmissionController::class, 'bulk']);
    Route::get('submissions', [SubmissionController::class, 'index']);
    Route::get('submissions/{submission}', [SubmissionController::class, 'show']);
    Route::match(['put', 'patch'], 'submissions/{submission}', [SubmissionController::class, 'update']);
    Route::delete('submissions/{submission}', [SubmissionController::class, 'destroy']);
});
