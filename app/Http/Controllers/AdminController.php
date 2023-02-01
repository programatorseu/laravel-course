<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    //
    public function index()
    {
        return view('admin.courses.index', [
            'courses' => Course::paginate(50)
        ]);
    }

    public function create()
    {
        return view('admin.courses.create');
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


    public function update(Course $course)
    {
        $attributes = request()->validate([
            'title' => 'required',
            'thumbnail' => 'image',
            'url' => ['required', Rule::unique('courses', 'url')->ignore($course->id)],
            'date' => 'required',
            'body' => 'required',
            'type_id' => ['required', Rule::exists('types', 'id')]
        ]);

        if (isset($attributes['thumbnail'])) {
            $attributes['thumbnail'] = request()->file('thumbnail')->store('thumbnails');
        }

        $course->update($attributes);

        return back()->with('success', 'course Updated!');
    }

    public function destroy(course $course)
    {
        $course->delete();

        return back()->with('success', 'course Deleted!');
    }


    public function edit(Course $course)
    {
     
        return view('admin.courses.edit', ['course' => $course]);
    }
}
