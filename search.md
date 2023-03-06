## Prosty input text + query filter
### 1. Messy
1. start from from (add `request('search')`)

```php
 <form method="GET" action="#">
                <input type="text" 
                    name="search" 
                    placeholder="Find something"
                    value="{{request('search')}}"
                    class="bg-transparent placeholder-black font-semibold text-sm">
```

```php
Route::get('/posts', function () {
    $posts = Post::latest();
    if(request('search')) {
        $posts
            ->where('title', 'like', '%' . request('search') . '%')
            ->orWhere('body', 'like', '%' . request('search') . '%');
    }
    return view('posts.index',[
        'posts' => $posts->get(),
        'categories' => Category::all()
    ]);
});
```



### 2 Query Scope

1. extract to controller 

```php
Route::get('/posts', [PostController::class, 'index']);
```

2. query scope 

`filter` method - we can create own query scope on eloquent model 

we start from `scope<Name of method>` pass `query` 



we had: 

```php
    public function index()
    {
        $posts = Post::latest();
        if(request('search')) {
            $posts
                ->where('title', 'like', '%' . request('search') . '%')
                ->orWhere('body', 'like', '%' . request('search') . '%');
        }
        return view('posts.index',[
            'posts' => $posts->get(),
            'categories' => Category::all()
        ]);
    }
```

now :

```php
      return view('posts.index',[
            'posts' => Post::latest()->filter()->get(),
            'categories' => Category::all()
        ]);
```



`scopeFilter` method:

```php
    public function scopeFilter($query) 
    {
        if(request('search')) {
            $query
                ->where('title', 'like', '%' . request('search') . '%')
                ->orWhere('body', 'like', '%' . request('search') . '%');
        }
    }
```

must accept `$query`

- 1 problem is that we use `request` from model 

```php
    return view('posts.index',[
            'posts' => Post::latest()->filter(request(['search']))->get(),
            'categories' => Category::all()
        ]);
```

```php
    public function scopeFilter($query, array $filters) 
    {
        if($filters['search'] ?? false) {
            $query
                ->where('title', 'like', '%' . request('search') . '%')
                ->orWhere('body', 'like', '%' . request('search') . '%');
        }
    }
```
