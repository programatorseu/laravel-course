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


## 2. Blade
```php
@foreach($courses as $course) 
@dd($loop)
@endforeach 
```

