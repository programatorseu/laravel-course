<?php

use App\Models\Course;
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
        'courses' => Course::findAll()
    ]);
});

Route::get('courses/{course}', function($title) {
    return view('course', [
        'course' => Course::find($title)
    ]);
})->whereAlpha('course');
