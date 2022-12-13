<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('posts', PostController::class);
    Route::get('/myposts', [PostController::class, 'myPosts'])->name('posts.myPosts');

    Route::get('/posts2', [PostController::class, 'index2'])->name('posts.index2');
    Route::get('/myposts2', [PostController::class, 'myPosts2'])->name('posts.myPosts2');
});

require __DIR__ . '/auth.php';