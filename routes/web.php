<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\LabClassController;

use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\ApiKeyController;
use App\Http\Controllers\Admin\SubmissionController as AdminSubmissionController;
use App\Models\Module;

Route::post('/lang', function (\Illuminate\Http\Request $request) {
    $locale = $request->input('locale', 'id');
    if (!in_array($locale, ['id', 'en'])) {
        $locale = 'id';
    }
    session(['locale' => $locale]);
    return redirect()->back();
})->name('lang.switch');

Route::get('/', [SubmissionController::class, 'create'])->name('submissions.create');
Route::post('/', [SubmissionController::class, 'store'])->name('submissions.store');


Route::get('/api/subjects/{subject}/modules', function (App\Models\Subject $subject) {
    if ($subject->is_hidden) {
        return response()->json([]);
    }
    $modules = $subject->modules()->visible()->latest()->get()->map(function ($module) {
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
    Route::post('subjects/bulk', [SubjectController::class, 'bulkDestroy'])->name('subjects.bulk');
    Route::resource('subjects', SubjectController::class);
    Route::post('modules/bulk', [ModuleController::class, 'bulkDestroy'])->name('modules.bulk');
    Route::resource('modules', ModuleController::class);
    Route::post('classes/bulk', [LabClassController::class, 'bulkDestroy'])->name('classes.bulk');
    Route::resource('classes', LabClassController::class)->parameters(['classes' => 'labClass']);
    Route::get('submissions', [AdminSubmissionController::class, 'index'])->name('submissions.index');
    Route::get('submissions/export', [AdminSubmissionController::class, 'export'])->name('submissions.export');
    Route::post('submissions/bulk', [AdminSubmissionController::class, 'bulk'])->name('submissions.bulk');
    Route::get('submissions/{submission}/grade', [AdminSubmissionController::class, 'edit'])->name('submissions.edit');
    Route::put('submissions/{submission}/grade', [AdminSubmissionController::class, 'update'])->name('submissions.update');
    Route::delete('submissions/{submission}', [AdminSubmissionController::class, 'destroy'])->name('submissions.destroy');
    Route::get('api-keys', [ApiKeyController::class, 'index'])->name('api-keys.index');
    Route::post('api-keys', [ApiKeyController::class, 'store'])->name('api-keys.store');
    Route::post('api-keys/{apiKey}/revoke', [ApiKeyController::class, 'revoke'])->name('api-keys.revoke');
    Route::delete('api-keys/{apiKey}', [ApiKeyController::class, 'destroy'])->name('api-keys.destroy');
});
