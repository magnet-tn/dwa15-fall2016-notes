## What are Controllers?
+ Controller classes make up the **C** of the **MVC** structure; they act as the glue between the request, the Model and the resulting View (or data).

+ For tiny applications it's convenient to throw all your logic into closures in your routes. As things grow, however, this becomes messy and hard to test.

+ Controllers offer a way to organize your routing logic.

+ Controller classes are stored in `/app/Http/Controllers/`

Aside: Controllers are Classes which are a fundamental part of Object Oriented Programming. If OOP is new to you, and you haven't already, be sure to review the [PHP OOP Primer](https://github.com/susanBuck/dwa15-fall2016-notes/blob/master/03_Laravel/00_OOP_Primer.md).

## Structure
Create a new file in `/app/Http/Controllers/` called `BookController.php` and paste in the following code:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

} # end of class
```

Notes:

+ Controller class files are stored in `/app/Http/Controllers/`. This directory is [psr-4](http://www.php-fig.org/psr/psr-4/) loaded in `/composer.json`, so anything you put here will be readily available using the appropriate namespace.

+ Naming
	+ You can name a Controller class file anything you want, but it's a convention to suffix it with `Controller` and use [upper CamelCase style](https://en.wikipedia.org/wiki/CamelCase#Variations_and_synonyms) (ex: `BookController`).
	+ It's also a convention that your controller names are singular (e.g. `BookController`, not `BooksController`).

+ Your controller class should extend Laravel's `Controller` class, which also exists in `/app/Http/Controllers/`.
	+ This base class is where you can put common logic shared by all your controllers.
	+ It also imports several Laravel convenience methods which we'll be taking advantage of.

+ Within your controller class you'll have public methods which represent the **actions** of your Controller.


## Connecting Routes to Controllers
Once you have Controllers, you can set up your routes so that they invoke specific methods in controllers. This means you don't have to embed logic in your routes file as we've been doing this far.

So, this...

```php
Route::get('/books', function() {
    return 'Here are all the books...';
})->name('books.index');
```

Can become this:

```php
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

If we create a route -> controller connection for each of the book actions, we'd end up with something like this:

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

```xml
$ php artisan make:controller AuthorController
```

This should generate a new file at `/app/Http/Controllers/AuthorController.php`; open it up to examine the results.

Let's try that command again, but this time we'll make a controller for Tags, and append the `--resource` flag:

```xml
$ php artisan make:controller TagController --resource
```

This should generate a new file at `/app/Http/Controllers/TagController.php`; open it up to examine the results and compare/contrast it with the AuthorController, then continue reading...



## Resource Controllers
You'll notice that the TagController is more fleshed out; it contains the 7 methods `index`, `create`, `store`, `show`, `edit`, `update`, `destroy` while the AuthorController contained no methods.

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
The idea of Resource Controllers comes from a design pattern called REST (**REpresentational State Transfer**), which is common to web applications.

There are lots of fancy explanations for REST, but it can be boiled down to this:

Every application has certain *resources* that you'll want to perform CRUD operations on. For example, in Foobooks, one of our resources is a Book. Within our application we'll want to *Create books*, *Update books*, *Read books* and *Delete books*. In other words, we'll want to provide all four CRUD operations on books.

Other resources our app will eventually have include *Authors*, *Users*, and *Tags*.

The REST design pattern defines a set of conventions for how these actions will be accessed.

An advantage of the REST design pattern is consistency. You'll often see APIs designed around REST, so that anyone who writes code to interact with that API can operate under a set of assumptions about how to work with the resources the API provides.

Here's some teaching team contributed mnemonics to help remember the 7 REST actions:

`ICSSEUD` (Index, Create, Store, Show, Edit, Update, Destroy)

+ Susan: *I Can See Sun Enveloped Under Dusk*
+ Jenni: *I Can Sense Silver Eggs Under Duress*
+ Katy: *I Can Sleep Soundly Even Upside Down*


## Non-resource specific controllers
Of course, not everything in your application will revolve around a well defined resource like Book, Author, Tag.

As mentioned in the Routes notes, you will also have miscellaneous pages like a *Contact Us* page, or a *Help & Support* page.

How you decide to organize these types of pages is up to you. You could create **Single Action Controllers** that are designed with a single action method called `__invoke()`

+ `HelpController.php`
	+ Action method: `public function __invoke()`
	+ Route: `Route::get('help', 'HelpController');`

+ `ContactController.php`
	+ Action method: `public function __invoke()`
	+ Route: `Route::get('contact', 'ContactController');`

Or maybe you create one controller devoted to misc/static pages:

+ `PageController.php`
	+ Action method: `public function help()`
	+ Route: `Route::get('/help', 'PageController@help');`
	+ Action method: `public function contact()`
	+ Route: `Route::get('/contact', 'PageController@contact');`


## HTTP method spoofing for methods other than GET and POST
HTML forms only support GET and POST methods, excluding methods like PUT and DELETE.

To get around this limitation, Laravel provides a way to &ldquo;spoof&rdquo; unsupported methods. This is done by injecting a hidden field `_method` with your forms:

```html
<form method='POST'>
	<input name='_method' type='hidden' value='PUT'>
	<label>Title <input type='text' name='title'></label>
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
<form method='POST'>
	<input name='_method' type='hidden' value='DELETE'>
	<input name='id' type='hidden' value='1'>
	<a href='javascript:void(0)' onClick='parentNode.submit();return false;'>Delete</a>
</form>
```

Even though this is structured as a form, it will not look like a form. You will just see a link that says <u>Delete</u>.

This form contains the following key elements:
+ The hidden _method field to spoof the `DELETE` method.
+ A hidden field with the id of the resource you want to delete, so that the id is passed with the request.
+ A button or link to submit the form. We used some inline JavaScript to show how a link can submit a form. Inline JS is not ideal, but we're using it here for simplicity's sake.


## Read More
+ <https://laravel.com/docs/controllers>
