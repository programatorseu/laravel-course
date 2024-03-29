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

## 7. Admin section 
- create controller to get and post method 
- form(@csrf remember)
- turn off fillable to avoid  SQLSTATE[HY000]: General error: 1364

### 7.1 Password hashing with mutator 

- to check password in Tinker use:
```
Illuminate\Support\Facades\Hash::check('password', $annia->password);
```

Model
```php
    public function setPasswordAttribute($password) 
    {
        $this->attributes['password'] = bcrypt($password);
    }
```

### 7.2 Failed message + old input 

@error directive: 
```php
          @error('name')
                        <p class="text-red-500 text-xs mt-1">{{$message}}</p>
                    @enderror
```

or we can show all error messages 
```php
    @foreach($errors->all() as $error)
        <li>{{$error}}</li>
    @endforeach
```

```php
    value="{{old('name')}}"
```

- update validation rules for unique values : 
```php
    'username' => 'required|min:3|max:255|unique:users, username',
    'email' => 'required|email|max:255|unique:users, email',
```


### 7.3 Success flash message
- create component for flashing that message (flash message for few seconds)
 * alpine.js library 
 * declared as alpine components 
 * init with timeout -> set show to false after 4 seconds 

 ```php
@if (session()->has('success'))
    <div x-data="{ show: true }"
         x-init="setTimeout(() => show = false, 4000)"
         x-show="show"
         class="fixed bg-blue-500 text-white py-2 px-4 rounded-xl bottom-3 right-3 text-sm"
    >
        <p>{{ session('success') }}</p>
    </div>
@endif
 ```

`</section><x-flash/>` - layout.blade

Register@store:
```php
      User::create($attributes);
        return redirect('/')->with('success', 'Your account has been created')
```

### 7.4 auth-Login / Logout
1.add to @store method
2.set middleware 
3.change in RouteServiceProvider  PATH  - > Auth Middleware uses to redirect 
4.Add logout to web route
5 create SessionController@destroy
```php
 $user = User::create($attributes);
        auth()->login($user);        
```

```php
Route::get('register', [RegisterController::class, 'create'])->middleware('guest');
Route::post('register', [RegisterController::class, 'store'])->middleware('guest');
```

```php
    public const HOME = '/';
```

```php
Route::post('logout', [SessionController::class, 'destroy']);
```
```php

 public function destroy() 
    {
        auth()->logout();
        return redirect('/')->with('success', 'Goodbye');
    }
```




### 7.5 Manual login : 
1. proper routing for login - get & post 
2. Corresponding methods inside Controller
3. create view
4. Security measure :
    - Session Fixation is an attack that permits an attacker to hijack a valid user session
 * so after logged in we need to regenerate id 



 ```php
 // store: 
         $attributes = request()->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);  
        if(! auth()->attempt($attributes)) {
            return back()
            ->withInput()
            ->withErrors(['email' => 'Your provided cred could not be verified']);
        }
        session()->regenerate();
        return redirect('/')->with('success', 'Welcome Back');
 ```

 ---
 ## 8. Comments
 1. Make migration + controller factory 
 There is a class ```ForeignColumnDefinition```
- constrained() method with reference on specific column to add 

2. setup relationship between eloquent models 
```php
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
        });
    }
```
factory
```php
class CommentFactory extends Factory
{
    public function definition()
    {
        return [
            'course_id' => Course::factory(),
            'user_id' => User::factory(),
            'body' => $this->faker->paragraph()  
        ];
    }
}
```
Course:
```php
   public function comments() 
    {
        return $this->hasMany(Comment::class);
    }
```
Comment: 
```php
class Comment extends Model
{
    use HasFactory;
    public function course() 
    {
        return $this->belongsTo(Course::class);
    }
    public function creator() 
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
```

- comment form inside course - item page
 - comment extracted into component

 route file : we need to tell which course is it  

 ```php
  <form action="/courses/{{$course->url}}/comments" class="border border-gray-200 p-6 rounded-xl">
 ```

 ```php
 Route::post('courses/{course}/comments', [CourseCommentController::class, 'store']);
 ```
 
 @store method 
 ```php
     public function store(Course $course) 
    {
        request()->validate([
            'body' => 'required'
        ]);
        $course->comments()->create([
            'user_id' => request()->user()->id,
            'body' => request('body')
        ]);
        return back();
    }
 ```

 ---
 ## 8. Admin section

 - CourseController
 ```php
     public function create() 
    {
        if(auth()->guest()) {
            abort(Response::HTTP_FORBIDDEN);
        }
        if(auth()->user()->username !== 'piotrek') {
            abort(Response::HTTP_FORBIDDEN);
        }
        return view('courses.create');
    }
 ```

 web route file  :
 ```php
 Route::get('admin/course/create', [CourseController::class, 'create']);
 ```

**Create middleware**
`php artisan make:middleware MustBeAdmin`

update Kernel file
```php
   protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'admin' => MustBeAdmin::class,
   ]
```

route file
`Route::get('admin/course/create', [CourseController::class, 'create'])->middleware('admin');`


### 8.1 Publish form
1. create route 
2. create form with @error
3. create @store  - use `Illuminate\Validation\Rule` facade
```php
Route::post('admin/courses', [CourseController::class, 'store'])->middleware('admin');
```



```php
    public function store()
    {
        $attributes = request()->validate([
            'title' => 'required',
            'url' => ['required', Rule::unique('courses', 'url')],
            'date' => 'required',
            'body' => 'required',
            'type_id' => ['required', Rule::exists('types', 'id')]
        ]);

        $attributes['user_id'] = auth()->id();

        Course::create($attributes);

        return redirect('/');
    }
```

### 8.2 Thumnail - validate store
-> enctype change to signify we are going to upload file 
-> add input type file to form 
```php
    <form enctype="multipart/form-data">
    ..
    <div class="mb-6">
        <label class="block mb-2 uppercase font-bold text-xs text-gray-700" for="thumbnail">
            Thumbnail
        </label>
        <input class="border border-gray-400 p-2 w-full"
            type="file"
            name="thumbnail"
            id="thumbnail"
            required
        >
        @error('thumbnail')
        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
        @enderror
    </div>
            
```
Controller@store 

`request()->file('image')->store('img');`
- image name of file image1.jpg
- img name of folder `storage/app/img/`


```php
// @store
    $attributes = request()->validate([  
     'thumbnail' => 'required|image',
    ...
    ]);
...
    $attributes['thumbnail'] = request()->file('thumbnail')->store('img');
```

update : 
`app/config/filesystems.php` -> storage_path change to public 
```php
    'default' => env('FILESYSTEM_DRIVER', 'public'),
```
now 
`storage/app/public/`

-> make storage link (symlink)
```bash
php artisan storage:link
```

-> add in migration (Course)
```php
$table->string('thumbnail')->nullable();
```

update course.blade.php to show image: 
 with `asset()` method

 ```php
     <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="Course image" class="rounded-xl">
 ```

 ---
 ### 8.3 Extract Form component 
 componets/form/input.blade.php
 `<x-form.input name="Title" />` -- to refer that file 

so our create will look like 
```php
<x-layout>
    <section class="px-6 py-8">
        <main class="max-w-lg mx-auto mt-10 bg-gray-100 border border-gray-200 p-6 rounded-xl">
            <form method="POST" action="/admin/courses" enctype="multipart/form-data">
                @csrf
                <x-form.input name="title" />
                <x-form.input name="slug" />
                <x-form.input name="thumbnail" type="file" />
                <x-form.textarea name="excerpt" />
                <x-form.textarea name="body" />
                <x-form.field>
                    <x-form.label name="type" />

                    <select name="type_id" id="type_id">
                        @foreach (\App\Models\Type::all() as $type)
                            <option
                                value="{{ $type->id }}"
                                {{ old('type_id') == $type->id ? 'selected' : '' }}
                            >{{ ucwords($type->name) }}</option>
                        @endforeach
                    </select>

                    <x-form.error name="type" />
                </x-form.field>
                <x-form.button>Publish</x-form.button>
            </form>
        </main>
    </section>
</x-layout>

```

### 8.4 Extend Admin layout 

-> use dropdown component 

```php
    <div class="mt-8 md:mt-0 flex items-center">
                @auth
                <x-dropdown>
                    <x-slot name="trigger">
                        <button class="text-xs font-bold uppercase">Welcome, {{ auth()->user()->name }}!</button>
                    </x-slot>

                    <x-dropdown-item href="/admin/dashboard">Dashboard</x-dropdown-item>
                    <x-dropdown-item href="/admin/courses/create" :active="request()->is('admin/courses/create')">New Post</x-dropdown-item>
                    <x-dropdown-item href="#" x-data="{}" @click.prevent="document.querySelector('#logout-form').submit()">Log Out</x-dropdown-item>

                    <form id="logout-form" method="POST" action="/logout" class="hidden">
                        @csrf
                    </form>
                </x-dropdown>
            @else
                <a href="/register" class="text-xs font-bold uppercase {{ request()->is('register') ? 'text-blue-500' : '' }}">Register</a>
                <a href="/login" class="ml-6 text-xs font-bold uppercase {{ request()->is('login') ? 'text-blue-500' : '' }}">Log In</a>
            @endauth
          
            </div>
```

### 8.5 Admin - update / delete / create seciton
1. update route inside web.php : 
```php
Route::post('admin/courses', [AdminController::class, 'store'])->middleware('admin');
Route::get('admin/courses/create', [AdminController::class, 'create'])->middleware('admin');
Route::get('admin/courses', [AdminController::class, 'index'])->middleware('admin');
Route::get('admin/courses/{course}/edit', [AdminController::class, 'edit'])->middleware('admin');
Route::patch('admin/courses/{course}', [AdminController::class, 'update'])->middleware('admin');
Route::delete('admin/courses/{course}', [AdminController::class, 'destroy'])->middleware('admin');
```
2. create controller and populate with all necessary methods 
- create component/setting.blade.php 
- create admin/courses/index.blade.php

AppServiceProvider
-> use Gate facade
```php
  public function boot()
    {
        Model::unguard();

        Gate::define('admin', function (User $user) {
            return $user->username === 'Piotrek S';
        });

        Blade::if('admin', function () {
            return request()->user()?->can('admin');
        });
    }
```
-> layout File
```php
                    @admin
                    <x-dropdown-item href="/admin/dashboard">Dashboard</x-dropdown-item>
                    <x-dropdown-item href="/admin/courses/create" :active="request()->is('admin/courses/create')">New Post</x-dropdown-item>
                    @endadmin
```