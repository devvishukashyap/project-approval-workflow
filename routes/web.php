<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    // Dashboard main page
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Project creation
    Route::get('/projects/create', [DashboardController::class, 'create'])->name('projects.create');
    Route::post('/projects/store', [DashboardController::class, 'store'])->name('projects.store');

    // DataTables endpoint (returns JSON for table)
    Route::get('/dashboard/projects', [DashboardController::class, 'projects'])->name('dashboard.projects');

    // Get single project details (for View modal)
    Route::get('/dashboard/projects/{id}', [DashboardController::class, 'showProject'])->name('dashboard.project.show');

    // Admin approval actions
    Route::patch('/projects/{id}/approve', [DashboardController::class, 'approve'])->name('projects.approve');
    Route::patch('/projects/{id}/reject', [DashboardController::class, 'reject'])->name('projects.reject');

    // Bulk actions (Admin only)
    Route::post('/projects/bulk-approve', [DashboardController::class, 'bulkApprove'])->name('projects.bulk.approve');
    Route::post('/projects/bulk-reject', [DashboardController::class, 'bulkReject'])->name('projects.bulk.reject');

    // Get status history for a project (Admin only)
    Route::get('/projects/{id}/history', [DashboardController::class, 'history'])->name('projects.history');
});

require __DIR__.'/auth.php';