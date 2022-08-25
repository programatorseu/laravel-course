<?php foreach($courses as $course): ?>
    
    <article>
       <h1>
           <a href="/courses/<?=$course->url;?>"> <?= $course->title; ?></h1>
           <p>
            By <a href="/creators/{{$course->creator->username}}">{{$course->creator->name}}</a> in
            <a href="/types/{{$course->type->slug}}">{{$course->type->name}}</a></p>
           
            </a>
       <div>
           <?= $course->body; ?>
       </div>
    </article>
<?php endforeach; ?>  