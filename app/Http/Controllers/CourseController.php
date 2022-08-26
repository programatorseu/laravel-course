<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class CourseController extends Controller
{
    //

    public function index() 
    {
        return view('courses', [

            'courses' => Course::latest()->filter(request(['search', 'type']))->paginate(2)->withQueryString()
        ]);
    }
    

    public function show(Course $course) 
    {
        return view('course', [
            'course' => $course
        ]);
    }

    public function create() 
    {
        return view('courses.create');
    }

    public function store()
    {
        $attributes = request()->validate([
            'title' => 'required',
            'thumbnail' => 'required|image',
            'url' => ['required', Rule::unique('courses', 'url')],
            'date' => 'required',
            'body' => 'required',
            'type_id' => ['required', Rule::exists('types', 'id')]
        ]);

        $attributes['user_id'] = auth()->id();
        $attributes['thumbnail'] = request()->file('thumbnail')->store('img');
        Course::create($attributes);
        return redirect('/');
    }
}
