<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Type;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    //

    public function index() 
    {
        return view('courses', [
            'courses' => Course::latest()->filter(request(['search']))->get(),
            'types' => Type::all()
        ]);
    }

    public function show(Course $course) 
    {
        return view('course', [
            'course' => $course
        ]);
    }
}
