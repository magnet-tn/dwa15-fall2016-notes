## What are Controllers?
+ Controller classes make up the **C** of the **MVC** structure; they act as the glue between the request, the Model and the resulting View (or data).

+ For tiny applications it's convenient to throw all your logic into closures in your routes. As things grow, however, this becomes messy and hard to test.

+ Controllers offer a way to organize your routing logic.

+ Controller classes are stored in `/app/Http/Controllers/`

## Structure
Example:
```php
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class BookController extends Controller
{

	/**
	* Responds to requests to GET /books
	*/
	public function index()
	{
        return 'Display all the books';
	}

    /**
     * Responds to requests to GET /books/{id}
     */
    public function show($id)
	{
        return 'Display book: '.$id;
    }

    /**
     * Responds to requests to GET /books/create
     */
    public function create()
	{
        return 'Display form to create a new book';
    }

    /**
     * Responds to requests to PUT /books
     */
    public function store()
	{
        return 'Process adding new book';
    }

} # end of class
```

+ Put Controller class files `/app/Http/Controllers/`. This directory is psr-4 loaded in `composer.json`, so anything you put here will be readily available using the appropriate namespace.

+ Naming
	+ You can name a Controller class file anything you want, but it's a convention to suffix it with `Controller` and use [upper CamelCase style](https://en.wikipedia.org/wiki/CamelCase#Variations_and_synonyms) (ex: `BookController`).
	+ It's also a convention that your controller names are singular (e.g. `BookController`, not `BooksController`).

+ Your controller class should extend Laravel's `Controller` class, which also exists in `/app/Http/Controllers/`.
	+ This base class is where you can put common logic shared by all your controllers.
	+ It also imports several Laravel convenience methods which we'll be taking advantage of.

+ Within your controller class you'll have public methods which represent the **actions** of your Controller.


## Connecting Routes to Controllers
Once you have Controllers, you can set up your routes so that they invoke specific methods in controllers. This means you don't have to embed so much logic in your routes file as we've been doing this far.

So, this...

```php
Route::get('/books', function() {
    return 'Here are all the books...';
})->name('books.index');
```

Will become this:

```
Route::get('/books', 'BookController@index')->name('books.index');
```

Note how we replaced the closure with `BookController@index` where `BookController` is the name of the Controller to use and `index` is the name of the method within that Controller to use.

You'll also want to move the logic into the `index` method in `BookController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class BookController extends Controller {

	public function index()
	{
        return 'Display all the books';
	}

	[...]
```

After making these changes, visit `http://localhost/books` to make sure it's still working.

If we create a route for each of the book actions, we'd end up with something like this:

```php
Route::get('/books', 'BookController@index')->name('books.index');
Route::get('/books/create', 'BookController@create')->name('books.create');
Route::post('/books', 'BookController@store')->name('books.store');
Route::get('/books/{book}', 'BookController@show')->name('books.show');
Route::get('/books/{book}/edit', 'BookController@edit')->name('books.edit');
Route::put('/books/{book}', 'BookController@update')->name('books.update');
Route::delete('/books/{book}', 'BookController@destroy')->name('books.destroy');
```



## Use Artisan to make Controllers
One of the things you'll discover as you learn more about Laravel is that there are helpful Artisan commands for generating common types of files. There is one such command for Controllers; try it out by navigating to your foobooks project directory and run this command:

```
$ php artisan make:controller AuthorController
```

This should generate a new file at `/app/Controllers/AuthorController.php`; open it up to examine the results.

Let's try that command again, but this time we'll make a controller for Tags, and append the `--resource` flag:

```
$ php artisan make:controller TagController --resource
```

This should generate a new file at `/app/Controllers/TagController.php`; open it up to examine the results and compare/contrast it with the AuthorController, then continue reading...



## Resource Controllers
You'll notice that the TagController is more fleshed out; it contains the methods `index`, `create`, `store`, `show`, `edit`, `update`, `destroy` while the AuthorController contained no methods.

This happened because the addition of the `--resource` flag, which told Artisan to make a special kind of controller called a [Resource Controller](https://laravel.com/docs/5.3/controllers#resource-controllers).

You'll note that each of these methods corresponds to the routes we created for books; already we're seeing an advantage of following conventions!

When working with Resource Controllers, you don't have to explicitly set each route, as we saw above:

```php
Route::get('/books', 'BookController@index')->name('books.index');
Route::get('/books/create', 'BookController@create')->name('books.create');
Route::post('/books', 'BookController@store')->name('books.store');
Route::get('/books/{book}', 'BookController@show')->name('books.show');
Route::get('/books/{book}/edit', 'BookController@edit')->name('books.edit');
Route::put('/books/{book}', 'BookController@update')->name('books.update');
Route::delete('/books/{book}', 'BookController@destroy')->name('books.destroy');
```

...Instead you can replace the above 7 lines with this single line:

```php
Route::resource('books', 'BookController');
```

Laravel will take care of the rest, and internally define all 7 routes based on the conventions/expectations of Resourceful Controllers.

Whether you choose to define all 7 routes, or use the short-cut is up to you.


## Aside: REST
The idea of Resource Controllers comes from a design pattern called REST (**REpresentational State Transfer (REST)**), which is common to web applications.

There are lots of fancy explanations for REST, but it can be boiled down to this:

Every application has certain *resources* that you'll want to perform CRUD operations on. For example, in Foobooks, one of our resources is a Book. Within our application we'll want to *Create books*, *Update books*, *Read books* and *Delete books*. In other words, we'll want to provide all four CRUD operations on books.

Other resources our app will eventually have include *Authors*, *Users*, and *Tags*.

The REST design pattern defines a set of conventions for how these actions will be accessed.

An advantage of the REST design pattern is consistency. You'll often see APIs designed around REST, so that anyone who writes code to interact with that API can operate under a set of assumptions about how to work with the resources the API provides.



## HTTP method spoofing for methods other than GET and POST
HTML Forms only support GET and POST methods, excluding methods like PUT and DELETE.

To get around this limitation, Laravel provides a way to &ldquo;spoof&rdquo; unsupported methods. This is done by injecting a hidden field `_method` with your forms:

```html
<form method='POST'>
	<input name='_method' type='hidden' value='PUT'>
	<input type='text' name='title'>
</form>
```

Note how the form method is technically `POST`, but the hidden field will tell Laravel to treat the request as if it were `PUT`.

More on this when we get to form processing.



## Destroy
Accessing the destroy action via the `DELETE` method is a little weird, because in most cases you don't need a form for deletion, you just need a button or link that will invoke the delation. For example:

```html
<a href='/tag/1'>Delete</a>
```

Links only ever use the GET method though.

To get around this, you can create a form that looks something like this:

```html
<form method='POST' action=''>
	<input name='_method' type='hidden' value='DELETE'>
	<input name='id' value='1'>
	<a href='javascript:void(0)' onClick='parentNode.submit();return false;'>Delete</a>
</form>
```

Even though this is structured as a form, it will not look like a form. You will just see a link that says <u>Delete</u>.

This form contains the following key elements:
+ The hidden _method field to spoof the `DELETE` method
+ A hidden field with the id of the resource you want to delete, so that the id is passed with the request
+ A button or link to submit the form. We used some inline JavaScript to show how a link can submit a form. (Inline JS is not ideal, but we're using it here for simplicity's sake)








## Namespacing in Controllers

Early on, we saw examples of Laravel facades in the `web.php` routes file like this:

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

...it'll fail with the following error:

```txt
FatalErrorException in BookController.php line 16:
Class 'App\Http\Controllers\App' not found
```

The reason it works in the `web.php` file is because that file is in the __global namespace__.

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

The same rule applies when using the Debugbar alias we'll set up when discussing Packages:

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




## Read More
+ <https://laravel.com/docs/controllers>
