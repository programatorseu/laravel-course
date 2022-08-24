<?php foreach($courses as $course): ?>
    
    <article>
       <h1>
           <a href="/courses/<?=$course->url;?>">
            <?= $course->title; ?></h1>
            </a>
       <div>
           <?= $course->body; ?>
       </div>
    </article>
<?php endforeach; ?>  