## Reference
+ [Docs: Authentication](http://laravel.com/docs/authentication)
+ [Docs: Middleware](http://laravel.com/docs/middleware)
+ [Docs: Authorization](https://laravel.com/docs/5.3/authorization)


## Different display for User vs. Guest
Adapt the Foobooks navigation menu:

Users should see this:
+ Home
+ Add a book
+ Log out

Guests should see this:
+ Home
+ Register
+ Login

To accomplish this, you can use the __`Auth::check()`__ method which returns `True` if the user is logged in or `False` if they are not.

In `resources/views/layouts/master.blade.php` the `<nav>` will be updated to look like this:

```php
<nav>
    <ul>
        @if(Auth::check())
            <li><a href='/'>Home</a></li>
            <li><a href='/books/create'>Add a book</a></li>
            <li><a href='/logout'>Log out</a></li>
        @else
            <li><a href='/'>Home</a></li>
            <li><a href='/login'>Log in</a></li>
            <li><a href='/register'>Register</a></li>
        @endif
    </ul>
</nav>
```

<img src='http://making-the-internet.s3.amazonaws.com/laravel-menu-for-guest-vs-user@2x.png' style='max-width:1008px; width:100%' alt=''>




## Manually restricting routes
One of the purposes of authentication is to keep non-registered users away from certain routes.

For Foobooks, we want to set it up so that any visitor can view books, but only logged in users can add books.

One way we could approach this is to update the BookController's `create` method so it redirects the user away from the page if they're not logged in like so:

```php
/**
 * GET
 */
public function create() {
    if(!Auth::check() ) {
        Session::flash('flash_message','You have to be logged in to create a new book');
        return redirect('/');
    }
    return view('books.create');
}
```

This works, but can become tedious if you have many actions that need to be restricted. An alternative method is to use Middleware...


## Restricting routes with Middleware
Laravel uses a feature called [Middleware](http://laravel.com/docs/middleware) to filter requests entering your application. When we introduced forms, we saw an example of Middleware with the CSRF checking that happens when forms are submitted.

Open `/app/Http/Kernel.php` and note the available middleware routes:

```php
/**
 * The application's route middleware.
 *
 * These middleware may be assigned to groups or used individually.
 *
 * @var array
 */
protected $routeMiddleware = [
    'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
    'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
    'can' => \Illuminate\Auth\Middleware\Authorize::class,
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
];
}
```

From this list, the `auth` middleware is what we'll use to lock down certain pages.

Open your routes and apply it to the books/create route like so:

```php
Route::get('/books/create', 'BookController@create')->name('books.create')->middleware('auth');
```

Now, when `/books/create` is visited, the `auth` middleware will apply.

__Test it out:__

If you are not logged in and you attempt to visit `http://localhost/books/create` you should be redirected to the login page.

Then, if you successfully login, you'll be sent back to `http://localhost/books/create`.


## Restricting routes to guests only
In the above examples we restricted routes to logged in users.

On the flip side, you may also want to restrict routes so that only guests (i.e. non-logged in users) can see them.

For example, if your user is already logged in, you don't want them to be able to visit `http://localhost/login` or `http://localhost/register`

Some of this functionality is already set up for you with the baked in authentication files Laravel gives you, so let's trace what's going on...

First, look at the `LoginController.php` and note the `__construct()` method invokes a middleware `guest`:

```php
public function __construct()
{
    $this->middleware('guest', ['except' => 'logout']);
}
```

This line is dictating that every method/action in LoginController will use the `guest` middleware, *except* for `logout`. (Note that this approach is applying the middleware via the Controller rather than the Routes as we did earlier.)

So what does the `guest` Middleware do?

Again we can refer to `Kernel.php`:

```php
protected $routeMiddleware = [
    'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
    'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
    'can' => \Illuminate\Auth\Middleware\Authorize::class,
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
];
```

Here we see that the `guest` Middleware is using the `\App\Http\Middleware\RedirectIfAuthenticated` Class and in that Class we see this `handle` method:

```php
public function handle($request, Closure $next)
{
    if ($this->auth->check()) {
        return redirect('/home');
    }

    return $next($request);
}
```

This method is simple enough: If the user is authorized, redirect them to `/home` (you can update this to some other destination if you prefer).

You can test if it's working by logging in (`http://localhost/login`) and then attempting to visit `http://localhost/login` or `http://localhost/register`.
