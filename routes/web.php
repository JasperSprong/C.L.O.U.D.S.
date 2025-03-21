<?php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ChartController;

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
// Routes the welcome view form the page welcome.blade.php
Route::get('/', function () {
    return view('welcome');
});
// Routes the dashboard page/main landing page fomr dashboard.blade.php
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// Connects the program to the profile that is automaticly generated 
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// Connects the filecontroller and the uploadcontroller classes to the dashboard and upload.store function
Route::get('/dashboard', [FileController::class, 'index'])->name('dashboard');
Route::post('/upload', [UploadController::class, 'store'])->name('upload.store');


Route::get('/user', [UserController::class, 'index'])->name('users.index');
Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('users.destroy');

// Connects the dashboard with the uploadcontroller class within that class the function destroy
Route::delete('/upload/delete', [UploadController::class, 'destroy'])->name('upload.destroy');

// Conects the dashboard with the filecontroller and witnin that class the function share
Route::post('/files/share', [FileController::class, 'share'])->name('file.share');


Route::get('/dashboard', [FileController::class, 'index'])->name('dashboard');


require __DIR__.'/auth.php';