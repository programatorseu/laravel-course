AppServiceProvider
-> use Gate facade
```php
  public function boot()
    {
        Model::unguard();

        Gate::define('admin', function (User $user) {
            return $user->username === '<hash>';
        });

        Blade::if('admin', function () {
            return request()->user()?->can('admin');
        });
    }
```