<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SessionController;

use App\Models\Course;
use App\Models\Type;
use App\Models\User;
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
Route::get('/', [CourseController::class, 'index'])->name('home');
Route::get('courses/{course}', [CourseController::class, 'show']);

Route::get('register', [RegisterController::class, 'create'])->middleware('guest');
Route::post('register', [RegisterController::class, 'store'])->middleware('guest');

Route::get('login', [SessionController::class, 'create'])->middleware('guest');
Route::post('login', [SessionController::class, 'store'])->middleware('guest');

Route::post('logout', [SessionController::class, 'destroy'])->middleware('auth');


// Route::get('types/{type:slug}', function(Type $type) {
//     return view('courses', [
//         'courses' => $type->courses,
//         'types' => Type::all(),
//         'currentType' => $type
//     ]);
// })->name('type');

Route::get('creators/{creator:username}', function(User $creator) {
    return view('courses', [
        'courses' => $creator->courses,
        'types' => Type::all()
    ]);
});