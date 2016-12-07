*Note: This bonus topic was last checked in Spring 2016, and has not been reviewed for Fall 2016; some edits to procedures may be needed.*

## Validating for author not selected

Add a default option to `authorsForDropdown` method in `Author.php`:

```php
public static function authorsForDropdown() {

    [...]
    $authors_for_dropdown = [];
    $authors_for_dropdown[0] = 'Choose an author...';
    [...]
```

Update `BookController::postCreate()` to use the `not_in:0` validation for `author_id`; also add a custom message.

```php
$messages = [
    'not_in' => 'You have to choose an author.',
];

$this->validate($request,[
    'title' => 'required|min:3',
    'published' => 'required|min:4',
    'cover' => 'required|url',
    'purchase_link' => 'required|url',
    'author_id' => 'not_in:0'
],$messages);
```

Make sure the author dropdown has the appropriate error output:

```html
<div class='error'>{{ $errors->first('author_id') }}</div>
```



## Handling bad ids
```php
if(is_null($book)) {
    \Session::flash('message','Book not found');
    return redirect('/books');
}
```



## Making data available to all views with Composers
Goal: Make `user` information available on all views.

Can be done with View Composers: <http://laravel.com/docs/views#view-composers>

First, make the composer:
```bash
$ php artisan make:provider ComposerServiceProvider
```

Then update the resulting composer:
```php
# /app/Providers/ComposerServiceProvider.php
public function boot()
{
    # Make the variable "user" available to all views
    \View::composer('*', function($view) {
        $view->with('user', \Auth::user());
    });
}
```

Register this Service Provider in `config/app.php`:
```php
'providers' => [
    // [...]
    'App\Providers\ComposerServiceProvider'
]
```

Then in any view you can use `$user`, for example:
```html
<li><a href='/logout'>Log out {{ $user->name }}</a></li>
```
