<?php
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

Route::get('/', function () {
    return view('courses', [
        'courses' => Course::latest()->with('type', 'creator')->get(),
        'types' => Type::all()
    ]);
})->name('home');



Route::get('courses/{course}', function (Course $course) {
    return view('course', [
        'course' => $course
    ]);
});

Route::get('types/{type:slug}', function(Type $type) {
    return view('courses', [
        'courses' => $type->courses,
        'types' => Type::all(),
        'currentType' => $type
    ]);
});

Route::get('creators/{creator:username}', function(User $creator) {

    return view('courses', [
        'courses' => $creator->courses
    ]);
});