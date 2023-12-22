<?php

use App\Http\Controllers\FileStoreController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VideoController;
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

Route::get('/', HomeController::class);
Route::get('/view/{video:uuid}', [VideoController::class, 'view'])->name('videos.view');

Route::middleware('auth')->group(function () {
    Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
    Route::get('/videos/create', [VideoController::class, 'create'])->name('videos.create');
    Route::post('/videos', [VideoController::class, 'store'])->name('videos.store');
    Route::get('/videos/{video:uuid}', [VideoController::class, 'show'])->name('videos.show');
    Route::patch('/videos/{video:uuid}', [VideoController::class, 'update'])->name('videos.update');
    Route::delete('/videos/{video:uuid}', [VideoController::class, 'destroy'])->name('videos.destroy');

    Route::post('/files', FileStoreController::class)->name('files.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
