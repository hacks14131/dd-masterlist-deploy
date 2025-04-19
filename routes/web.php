<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Login;
use App\Http\Controllers\Home;
use App\Http\Controllers\Error_page;
use App\Http\Controllers\MasterlistController;

/*
|--------------------------------------------------------------------------
| Public (guests‑only) route
|--------------------------------------------------------------------------
|
| If the visitor is already authenticated, Laravel’s
| RedirectIfAuthenticated middleware will push them straight to /home.
*/
Route::get('/', [Login::class, 'index'])
     ->middleware('guest')
     ->name('login');

/*
|--------------------------------------------------------------------------
| Protected routes
|--------------------------------------------------------------------------
|
| Anyone hitting these URLs must be logged in.  If they aren’t,
| the Authenticate middleware fires and sends them back to “/”.
*/
Route::middleware('auth')->group(function () {
    Route::get('/home',  [Home::class,  'index'])->name('home');

    // Route::get('/masterlists/find-duplicates', 'MasterlistController@findDuplicates')->name('masterlists.find-duplicates');
    // Route::get('/masterlists/scan-duplicates', 'MasterlistController@scanForDuplicates')->name('masterlists.scan-duplicates');
    // Route::get('/masterlists/find-duplicates', [MasterlistController::class, 'findDuplicates'])->name('masterlists.find-duplicates');
    // Route::get('/masterlists/scan-duplicates', [MasterlistController::class, 'scanForDuplicates'])->name('masterlists.scan-duplicates');
    
    Route::get('/error', [Error_page::class, 'index'])->name('error');
});

/*
|--------------------------------------------------------------------------
| Optional custom fallback
|--------------------------------------------------------------------------
|
| Uncomment this if you want to handle 404s yourself.
| Otherwise, let Laravel show its default 404 page.
*/
Route::fallback(function () {
    return auth()->check()
        ? redirect()->route('error')
        : redirect()->route('login');
});
