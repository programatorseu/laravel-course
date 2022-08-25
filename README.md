# Laravel - course 
Based on laravel framework is a try to adopt fully working course-management system written in PHP 

Big up for Jeffrey Way and laracasts.com
I have started php yourney from some early tutsplus wordpress / php tutorials 


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

route
```php
Route::get('courses/{course}', function (Course $course) {
    return view('course', [
        'course' => $course
    ]);
});
```

### 2.4 Eloquent Relationship
```php
      Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_id');
 ```
            
```php
    public function up()
    {
        Schema::create('types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
        });
    }
```
Every course can be 1 type 

```php
public function type() 
    {
        return $this->belongsTo(Type::class);
    }
```

Type can have many courses
```php
  public function courses() 
    {
        return $this->hasMany(Course::class);
    }
```

show course related with type 

* route inside web.php
```php
Route::get('types/{type:slug}', function(Type $type) {
    return view('courses', [
        'courses' => $type->courses
    ]);
});
```
* updated blade file  - index one 
```php
 <p><a href="/types/{{$course->type->slug}}">{{$course->type->name}}</a></p>
```
load only courses related with type - to minimize queries
```php
Route::get('/', function () {
    return view('courses', [
        'courses' => Course::with('type')->get()
    ]);
});
```

### 2.5 Seeder and factories
 - change in migration - add relation with User table / add unique to Type table (for seeding)
 - setup relationship between User-Course

```php
  public function user() 
    {
        return $this->belongsTo(User::class);
    }
```
- create factories for Course & Types
```php
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'type_id' => Type::factory(),
            'title' => $this->faker->sentence,
            'date'=> $this->faker->date(),
            'body' => $this->faker->sentence,
            'url' => $this->faker->slug,
        ];
    }
```
- create seeder file
```php
  public function run()
    {
        // \App\Models\User::factory(10)->create();
        User::truncate();
        Type::truncate();
        Course::truncate();
        
        $user = User::factory()->create([
            'name'=> 'Piotrek S'
        ]);
        Course::factory(4)->create([
            'user_id' => $user->id
        ]);

    }
```
- run with command
```php
php artisan migrate:fresh --seed
```

### 2.6 Author sorting + eager load 
- add creator to with - to avoid n+1 pro 
```php
   return view('courses', [
        'courses' => Course::latest()->with('type', 'creator')->get()
    ]);
```

```php
  public function creator() 
    {
        return $this->belongsTo(User::class, 'user_id');
    }
```

add route to get creator by username: 

```php
Route::get('creators/{creator:username}', function(User $creator) {
    return view('courses', [
        'courses' => $creator->courses
    ]);
});
```

- addd username into users migration
```php
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username');
```

- update factory (user)

* every time we fetch course 
-> add `$with` inside eloquent model 
```php
    protected $with = ['type', 'creator'];
```