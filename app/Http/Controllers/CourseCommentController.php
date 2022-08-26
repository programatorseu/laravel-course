<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseCommentController extends Controller
{
    //

    public function store(Course $course) 
    {
        // var_dump(request()->all());

        request()->validate([
            'body' => 'required'
        ]);
        $course->comments()->create([
            'user_id' => request()->user()->id,
            'body' => request('body')
        ]);
        return back();
    }

}
