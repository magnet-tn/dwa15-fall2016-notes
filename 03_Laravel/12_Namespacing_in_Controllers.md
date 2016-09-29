# Namespacing in Controllers

When we first introduced routes, we saw examples of Laravel facades in the `web.php` routes file like this:

```php
# Print out the current environment
echo App::environment();
```

If you try this same invocations in a Controller action...

```php
public function index()
{
    echo App::environment();
}
```

...it'll fail with an error similar to this:

```txt
FatalErrorException in BookController.php line 16:
Class 'App\Http\Controllers\App' not found
```

The reason this statement works in the `web.php` file is because that file is in the __global namespace__.

When you move into the controller, you're then in that controller's namespace.

The error message points this out&mdash; it's trying to find `App` in the `App\Http\Controllers\` namespace, where it does not exist.

To fix this, you can put a backward slash in front of the facade invocations, forcing it to look for `App` in the global namespace:

```php
public function getIndex() {
    echo \App::environment();
}
```

Alternatively, you could add a `use` statement at the top of your controller:

```php
use App;
```

The same rule applies when using the Debugbar alias we'll set up when we get to discussing Packages in Lecture 6.

```php
public function getIndex() {
    echo \Debugbar::info('Testing...');
}
```

You could prefix all your Debugbar calls with `\`, or, add the following `use` statement at the top of your controller:

```
use Debugbar;
````

(Note that the namespace is not `Barryvdh\Debugbar` because of the alias setup for `Debugbar` in `config/app.php`.)
