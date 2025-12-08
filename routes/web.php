<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScreenshotController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file contains all web routes for the Laravel12-Screen-Shot project.
| Routes define which controller methods handle which URLs. 
| Middleware 'web' is applied by default for session, CSRF protection, etc.
|
*/

// Route to display all active screenshots
// URL: /screenshots
// Controller: ScreenshotController@index
// Named route: screenshots.index
Route::get('/screenshots', [ScreenshotController::class, 'index'])->name('screenshots.index');

// Route to show the form for creating a new screenshot
// URL: /screenshots/create
// Controller: ScreenshotController@create
// Named route: screenshots.create
Route::get('/screenshots/create', [ScreenshotController::class, 'create'])->name('screenshots.create');

// Route to handle form submission and store new screenshot in database
// URL: /screenshots (POST)
// Controller: ScreenshotController@store
// Named route: screenshots.store
Route::post('/screenshots', [ScreenshotController::class, 'store'])->name('screenshots.store');

// Route to soft delete a screenshot (change status to 'deleted' and use soft delete)
// URL: /screenshots/{id} (DELETE)
// Controller: ScreenshotController@destroy
// Named route: screenshots.destroy
Route::delete('/screenshots/{id}', [ScreenshotController::class, 'destroy'])->name('screenshots.destroy');

// Default home route for the application
// URL: /
// Returns the default welcome view
Route::get('/', function () {
    return view('welcome');
});
