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

---

### 3. Layout - component

images - public/images

refer 
```html
   <img src="./images/logo.svg"
```
component blade used 

#### 3.1 dropdown
- import alpine library 

 x-data component -> show to false and bind it with bottom links 
- then we add `x-show` into inner div 
button @click event - set to the opposite what show currently is 

Basically 3 components file 
- dropdown
- dropdown-item
- icon for arrows 
`component/dropdown.blade.php`
```php
@props(['trigger'])
<div x-data="{ show:false }" @click.away="show = false">
    <div @click="show = ! show">
        {{ $trigger }}
    </div>
    <div x-show="show" class="py-2 absolute bg-gray-100 w-full mt-2 rounded-xl z-50 max-h-52" style="display: none">
        {{$slot}}
    </div>
</div>
```
item:
```php
@props(['active' => false])
@php
    $classes = 'block text-left px-3 text-sm leading-6 hover:bg-blue-500 focus:bg-blue-500 hover:text-white focus:text-white';
    if ($active) $classes .= ' bg-blue-500 text-white';
@endphp
<a {{ $attributes(['class' => $classes]) }}>
{{ $slot }}</a>

```

icon

```php
@props(['name'])
@if($name === 'down-arrow')
<svg {{ $attributes(['class' => 'transform -rotate-90']) }}style="right: 12px;" width="22" height="22" viewBox="0 0 22 22">
    <g fill="none" fill-rule="evenodd">
    <path stroke="#000" stroke-opacity=".012" stroke-width=".5" d="M21 1v20.16H.84V1z">
</path>
    <path fill="#222"
      d="M13.854 7.224l-3.847 3.856 3.847 3.856-1.184 1.184-5.04-5.04 5.04-5.04z"></path>
    </g>
</svg>
@endif
```

add named route  to help set up active based on uri 

```php
Route::get('/', function () {
    return view('courses', [
        'courses' => Course::latest()->with('type', 'creator')->get(),
        'types' => Type::all()
    ]);
})->name('home');
```

**header file with dropdow applied**


```php
  <x-dropdown>
                    <x-slot name="trigger">
                        <button
                         class="py-2 pl-3 pr-9 text-sm font-semibold w-full lg:w-32 text-left flex lg:inline-flex">Types
                    <x-icon name="down-arrow" class="absolute pointer-events-none" style="right:12px;" />
                    </button>
                    </x-slot>
                    <x-dropdown-item href="/" :active="request()->routeIs('home')">All</x-dropdown-item>
                    @foreach($types as $type)
                    <x-dropdown-item 
                        href="/types/{{$type->slug}}"
                        :active="request()->is('types/' . $type->slug)"
                        >{{ucwords($type->name) }}</x-dropdown-item>
                    @endforeach
                </x-dropdown> 
```

## 4. Controller - search feature 
 1. controller with index (search from <scopeFilter>) and show 
 2. update web route file
 3. create query `scopeFilter` inside Eloquent model 


```php
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
```

2. Route file 
```php
Route::get('/', [CourseController::class, 'index'])->name('home');
Route::get('courses/{course}', [CourseController::class, 'show']);
```

3.
```php
    // 1st param passed by laravel
    public function scopeFilter($query, array $args)  
    {  
        if($args['search'] ?? false) {
            $query
                ->where('title', 'like', '%' . request('search') . '%')
                ->orWhere('body', 'like', '%' . request('search') . '%');
        }
    }
```

---
### 5. Filtering 
#### 5.1 Eloquent Query Contraints

Sql function `EXISTS` allow to pass `SELECT stmt` and check truthness of clause 

We want to achieve: 
```bash
?types=esse-nemo-temporibus
```
then we are going to use combine - search & type 

**steps**
1. pass conditional to check type to our filter -> inside @index method 
2. reproduce with eloquent sql which looks like (exist sql function)


```php
    'courses' => Course::latest()->filter(request(['search', 'type']))->get(),
```


Scope Filter method
* whereHas - eloquent relationship 

```php
    public function scopeFilter($query, array $args)  
    {  
        $query->when($args['search'] ?? false, fn($query, $search) => 
                $query
                    ->where('title', 'like', '%' . request('search') . '%')
                    ->orWhere('body', 'like', '%' . request('search') . '%'));
         
        $query->when($args['type'] ?? false, fn($query, $type) =>
                $query->whereHas('type', fn ($query) =>
                    $query->where('slug', $type)
                )
                );
                                
    }
```

* make type comoopnent
```bash
php artisan make:component TypeDropdown
```
- creates view/components 
- creates app/view/Components/TypeDropdown class 

Inide header file
```php
   <!--  type -->
    <div class="relative flex lg:inline-flex items-center bg-gray-100 rounded-xl">
         <x-type-dropdown/>
    </div>
```

reder method of that class
```php
    public function render()
    {
        return view('components.type-dropdown',[
            'types' => Type::all(),
            'currentType' => Type::where('slug', request('type'))->first()

        ]);
    }
```

### 5.2 merge Type and Search 
-> we want to search within selected type 
 -> add hidden input 
```php
         <form method="GET" action="/">
                @if(request('type'))
                    <input type="hidden" name="type" value="{{request('type') }}" />
                @endif
```
 -> reverse approach build href inside dropdown 
```php
 <x-dropdown-item 
        href="?type={{$type->slug}} & {{ http_build_query(request()->except('type')) }}"

```

 fix:
 ```php
 
           $query->when($args['search'] ?? false, fn($query, $search) =>
            $query->where(fn($query) =>
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('body', 'like', '%' . $search . '%')
            )
        );
        ..
 ```

 ## 6. Pagination

 paginate() return all data + information necessary for paginating 

- current page we are on
- number of links 
- per_page 

`/?page=2`
```php
            'courses' => Course::latest()->filter(request(['search', 'type']))->paginate(2)
```

add to index `{{$courses->lins()}}`

- exclude from query in dropdown : 
```php
    <x-dropdown-item 
        href="?type={{$type->slug}} & {{ http_build_query(request()->except('type', 'page')) }}"
        :active="request()->is('types/' . $type->slug)"
        >{{ucwords($type->name) }}</x-dropdown-item>
```
-> include existing query string in pagination : 
```php
paginate(2)->withQueryString()
```