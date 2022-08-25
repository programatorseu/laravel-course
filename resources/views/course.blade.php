<article>
    <h2><?= $course->title; ?></h2>
    <p>By <a href="#">{{ $course->creator->name}}</a> in 
    <p>in <a href="/types/{{$course->type->slug}}">{{$course->type->name}}</a></p>
    <p><?= $course->date; ?></p>
    <p><?= $course->body; ?></p>
</article>