<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScreenshotController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file contains all web routes for the Laravel12-Screen-Shot project.
|
*/

// Display all active screenshots (Gallery)
Route::get('/screenshots', [ScreenshotController::class, 'index'])->name('screenshots.index');

// Show form to create new screenshot
Route::get('/screenshots/create', [ScreenshotController::class, 'create'])->name('screenshots.create');

// Store new screenshot in database
Route::post('/screenshots', [ScreenshotController::class, 'store'])->name('screenshots.store');

// Soft delete a screenshot
Route::delete('/screenshots/{id}', [ScreenshotController::class, 'destroy'])->name('screenshots.destroy');

// **New Route: Download screenshot and increment download count**
Route::get('/screenshots/download/{id}', [ScreenshotController::class, 'download'])->name('screenshots.download');

// **New Route: Generate PDF report**
Route::get('/screenshots/pdf', [ScreenshotController::class, 'generatePdf'])->name('screenshots.pdf');

// Default home route
Route::get('/', function () {
    return view('welcome');
});