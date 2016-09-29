# Namespacing in Controllers

Paste the following route in your routes file and visit http://localhost/example.

```php
Route::get('/example', function() {
    return App::environment();
})
```

In this example, we've used Laravel's App facade to print the current environment. (App is one of many Laravel Facades available to you throughout the framework.)

Now try this same statement (`return App::environment();`), adding it temporarily to your BookController's index action:

```php
# /app/Http/Controllers/BookController.php

public function index()
{    
    return App::environment();
    #return 'Here are all the books...';
}
```

When you run this (via http://localhost/books) it'll fail with an error similar to this:

<img src='http://making-the-internet.s3.amazonaws.com/laravel-app-class-not-found-in-controller@2x.png' style='max-width:900px;' alt=''>

The reason this statement works in the `web.php` routes file is because that file is in the __global namespace__.

When you move into the Controller, you're then in that controller's namespace, which is `App\Http\Controllers`.

The error message makes this clear&mdash;

>> `Class 'App\Http\Controllers\App' not found`

To fix this, you can put a backward slash in front of facade invocations so that they'll be located in the global namespace, e.g.

```php
# /app/Http/Controllers/BookController.php

public function index() {
    return \App::environment();
}
```

Alternatively, you could add a `use App` statement at the top of your controller which will make it available from the global namespace:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App;

class BookController extends Controller
{

    public function index()
    {
        return App::environment();
    }
```
