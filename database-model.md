# Laravel



## 1. DB connection

- rollback hack - change migrations table batch number to the highest in case of tables  ktore chcemy **rollback**


```
php artisan migrate:rollback
```

### 1.1 Active Record Pattern

- approach to access data in DB. Table or view is wrapped into a class 
- interestintg approach in tinker :

```php
> $user->name = 'Piotr Sadowski';
= "Piotr Sadowski"

> $user->email = 'programatorseu@gmail.com';
= "programatorseu@gmail.com"

> $user->password = bcrypt('bleki123');
= "$2y$10$Y6hksnWMjDn.tcPq2iVBrezgNpohsw5Kd2RNqMCCjEbA9oQYX1O4u"

> $user->save();
= true
```

now we have just 1 row in db : 

```php
> User::find(1);
// = App\Models\User {#4531}

> User::find(2);
//= null

> User::findOrFail(2);
 // Illuminate\Database\Eloquent\ModelNotFoundException  No query results for model [App\Models\User] 2.

```

tinker method

2 methods to work with collection 

```php
> User::all();
//  = Illuminate\Database\Eloquent\Collection {#4532
    
> $users = User::all();
// = Illuminate\Database\Eloquent\Collection {#4540
$users->pluck('name');
//= Illuminate\Support\Collection {#4536
  //  all: [
    //  "Piotr Sadowski",
      //"Basia",
  //  ],
  // }

```

`pluck` is an equivalent to : 

```PHP
$users->map(function($user) {return $user->name;});	

> $users[0]
// = App\Models\User {#4538
   // id: 1,
    

```

### 1.2 Make a Post model and migration 

```
php artisan make:migration create_posts_table

```

```php
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('excerpt');
            $table->text('body');
            $table->timestamps();
            $table->timestamp('published_at')->nullable();
        });
    }

```

```
php artisan make:model Post
```

```php
> $post = new App\Models\Post();
//= App\Models\Post {#3581}

> $post->title = 'Pierwszy Post';
//= "Pierwszy Post"

> $post->excerpt = 'pierwszy..';
//= "pierwszy.."

> $post->body = 'Body of first Post';
//= "Body of first Post"

> $post->save()
//= true
> App\Models\Post::count();
//= 1
```

use case: 

```php
use App\Models\Post
Post::all();
// Illuminate\Database\Eloquent\Collection {#3916
  // all: [

```



Routing + layout : 

layout simple at this point

```php
<x-layout>
    @foreach($posts as $post)
     <article>
      <h1>
         <a href="/posts/{{$post->id}}">
            {{$post->title}}
        </a>
      </h1>
      <div>
        {{$post->excerpt}}
      </div>
     
     </article>
    @endforeach
   
   </x-layout>	
```

routing file :

```php
Route::get('/posts/{post}', function(Post $post) {
    return view('posts.show', [
        'post' => $post
    ]);
});
```



show file :

```php
<x-layout>
   <h2>{{$post->title}}</h2>
   <p>{{$post->body}}</p>
   <p><a href="/posts">Go back</a></p>
</x-layout>
```

**html escaping **

> $post->title = '<strong>Third Post</strong>';
> = "<strong>Third Post</strong>"

escape:

```
{!! $post->title !!}
```



### 1.3 Mass Assignment

Mass - in other terms bulk assignment 

when user is trying to assign mallicious code eaxtra as part of form submit with many fields 

**Mass assignment** is a [computer vulnerability](https://en.wikipedia.org/wiki/Vulnerability_(computing)) where an [active record pattern](https://en.wikipedia.org/wiki/Active_record_pattern) in a [web application](https://en.wikipedia.org/wiki/Web_application)

we can set `fillable` fields 

```php
    protected $fillable = ['title', 'body', 'excerpt'];
```

```php
 App\Models\Post::create(['title' => 'My enth post', 'body' => 'this is body of that post', 'excerpt' => 'excerpt..']);

```

we can set that everything could be mass assigned except id : 

```php
 protected $guarded = ['id'];
```



---



### 1.4 Route Model binding : 

```php

Route::get('/posts/{post}', function(Post $post) {
    return view('posts.show', [
        'post' => $post
    ]);
});
```

add slug : 

```php
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
```

then in routing

```php
Route::get('/posts/{post:slug}', function(Post $post) {
```

our index file

```php
         <a href="/posts/{{$post->slug}}">
```

other option with `getRouteKeyName()`

```php
    public function getRouteKeyName()
    {
        return 'slug';
    }
```



### 1.5 First relationship

```
php artisan make:model Category -m
```

```php
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
        });
```

Post table:

```php
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id');
```

populate db 

create relationship method in Post model:

- hasOne / HasMany / belongsTo / belongsToMany

- `belongsTo` 1 category

```php
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
```

`$post->category` to access relationship 

```php
      <p><b>Category</b> <a href="">{{$post->category->name}}</a></p>
```

### 1.6 Show Post related to category : 

1. create route 

```php
Route::get('categories/{category}', function(Category $category) {
    return view('posts.index', [
        'posts' => $category->posts
    ]);
});
```

2. setup relationship in Category :

```php
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
```

or even we can update:

```php
Route::get('categories/{category:slug}', function(Category $category) {
```

### 1.7 Clockwork and N+1 Problem 

loading category is a problem

- we are inside foreach loop in index file 
- we try to load relationship that does not exist yet !  - additional sql query ! 



```
composer require itsgoingd/clockwork
```

avoid problem `with` method:

```php
Route::get('/posts', function () {
    return view('posts.index',[
        'posts' => Post::with('category')->get()
    ]);
});
```



### 1.8 Database seeding :

1. setup relationship between user - post :

inside post table: 

```php
$table->foreignId('user_id');
```

2. methods inside User & Post class : 

```php
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
```



```php
    public function author() 
    {
        return $this->belongsTo(User::class, 'user_id');
    }
```



2. create factory for Post & Category

```php

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => $this->faker->sentence,
            'slug' => $this->faker->slug,
            'excerpt' => $this->faker->sentence,
            'body' => $this->faker->paragraph
        ];
    }
```



```
    public function definition()
    {
        return [
            //
            'name' => $this->faker->word,
            'slug' => $this->faker->slug
        ];
    }
```





```php
 php artisan migrate:fresh --seed
```

Database Seeder : 

```php
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::factory()->create([
            'name' => 'Piotr S'
        ]);
        Post::factory(5)->create([
            'user_id' => $user->id
        ]);
    }
```

### 1.9 View all posts by an author

- sort by date : 

```php
 'posts' => Post::latest()->with('category')->get()
```

- deal with n+1 problem

```php
Route::get('/posts', function () {
    return view('posts.index',[
        'posts' => Post::latest()->with('category', 'author')->get()
    ]);
});

```



route for authors

```php
Route::get('/authors/{author}', function(User $author) {
    return view('posts.index', [
        'posts' => $author->posts
    ]);
});
```

we can change to pass username instead of id 

```php
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
```

add to factory

```php
    return [
            'name' => $this->faker->name(),
            'username' => $this->faker->unique()->userName(),
```

routing : 

```php
Route::get('/authors/{author:username}', function(User $author) {
    return view('posts.index', [
        'posts' => $author->posts
    ]);
});
```



```php
      <p>by <a href="/authors/{{$post->author->username}}">
```

### 1.10 Load Relationship on Existing Model

```php
 return view('posts.index', [
        'posts' => $category->posts->load(['category', 'author'])
    ]);
```

set $with - default relationship inside model : 

```php
    protected $with = ['category', 'author'];

```

disable for query;

```
Post::without('author')->first();
```
