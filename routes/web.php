<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\DropdownController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {

    // Role Super Admin
    Route::middleware('role:Super Admin')->group(function () {
        Route::resource('roles', RoleController::class);
        Route::resource('users', UserController::class);
    });

    Route::middleware('role:Super Admin|BPH')->group(function () {
        Route::resource('activities', ActivityController::class);
        Route::resource('achievements', AchievementController::class);
        Route::resource('finances', FinanceController::class);
    });

    Route::prefix('dropdown')->controller(DropdownController::class)->group(function () {
        Route::get('roles', 'getRoles')->name('dropdown.roles');
    });

    Route::post('media', [MediaController::class, 'store'])->name('media');
});
