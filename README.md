# Laravel - course 
Based on laravel framework is a try to adopt fully working course-management system written in PHP 

## 1. Routing 
wildcard
```php
Route::get('courses/{course}', function($title) {
    $path  = __DIR__ . "/../resources/courses/{$title}.html";
    if(!file_exists($path)) {
        return redirect('/');
    }
    $course = file_get_contents($path);
    return view('course', [
        'course' => $course
    ]);
```

> res/courses/
>            podstawy-programowania
>            awaryjne-otwieranie           

**containt added to the end**
```php
})->whereAlpha('course');
```

### 1.1 Caching 
if 10k users hit page 
`file_get_contents` will be called 10k times 
instead cache it 

```php
    $course = cache()->remember("courses.{$title}", 3600, function() use($path) {
        return file_get_contents($path);
    });

```

### 1.2 Read directory with File System
`use Illuminate\Support\Facades\File;`

```php
  public static function findAll() 
    {
        $files = File::files(resource_path("posts/"));
        return array_map(function($file){
            return $file->getContents();
        }, $files);
    }
```
### 1.3 Medata - yaml -fetcher
`composer require spatie/yaml-front-matter`

inside Course model create constructor
```php
    public function __construct($title, $excerpt, $date, $body, $url)
    {
        $this->title = $title;
        $this->excerpt = $excerpt;
        $this->date = $date;
        $this->body = $body;
        $this->url = $url;
    }
```

example of metadata format
```
---
title: Awaryjne otwieranie
url: awaryjne-otwieranie
excerpt: Lorem ipsum ...
date: 2020-04-24
---
```


## 2. Working with DB
### 2.1 Eloquent and Active Record Pattern
Models/ - > eloquent models (way to interact with our database tables)
each table has corresponding model 
users -> User model - > **active Record Pattern** ('object instance tied to single row')
```php 
php artisan tinker
$user = new App\Models\User;
$user = new User; # tinker will figure out

$user->name = "Piotr";
=> "Piotr"
$user->email = "<email>"
$user->password = bcrypt("bleki123");
$user->save();
User::find(1);
User::findOrFail(2);

$users = User::all();
$users->pluck('name');

```

### 2.2 Make Course model + migration

now is only empty mode with extended feature from Eloquent.
- create migration 

to avoid mass asignment vulnerabilities:
```php
    protected $fillable = ['id', 'title', 'body', 'date'];
```

### 2.3 Route Model Binding 

- change in migration & migrate fresh
```php
    $table->string('url')->unique();
```

- inside our Model
```php
  public function getRouteKeyName() 
    {
        return 'slug';
    }
```

layout 
```php
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
```