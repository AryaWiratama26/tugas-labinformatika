<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\LabClassController;

use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\SubmissionController as AdminSubmissionController;
use App\Models\Module;

Route::get('/', [SubmissionController::class, 'create'])->name('submissions.create');
Route::post('/', [SubmissionController::class, 'store'])->name('submissions.store');


Route::get('/api/subjects/{subject}/modules', function (App\Models\Subject $subject) {
    $modules = $subject->modules()->latest()->get()->map(function ($module) {
        return [
            'id' => $module->id,
            'name' => $module->name,
            'description' => $module->description,
            'is_expired' => $module->is_expired,
            'deadline' => $module->deadline?->format('d M Y, H:i'),
            'image_url' => $module->image_path ? asset('storage/' . $module->image_path) : null,
        ];
    });
    return response()->json($modules);
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('subjects', SubjectController::class);
    Route::resource('modules', ModuleController::class);
    Route::resource('classes', LabClassController::class)->parameters(['classes' => 'labClass']);
    Route::get('submissions', [AdminSubmissionController::class, 'index'])->name('submissions.index');
    Route::get('submissions/export', [AdminSubmissionController::class, 'export'])->name('submissions.export');
    Route::delete('submissions/{submission}', [AdminSubmissionController::class, 'destroy'])->name('submissions.destroy');
});
