Routes are declared using the `Route` facade in `routes/web.php`

Among other things, `Route` gives you access to these [HTTP methods](https://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.4):

1. GET
2. POST
3. PUT
4. DELETE

Here's an example GET route for the Foobooks app:

```php
Route::get('/books', function()
{
    return 'Here are all the books...';
});
```

The **first parameter** is a URI String that will trigger this route; in this case that's `/books`.

I.e. if a user goes to `http://localhost/books` it will trigger this route.

The **second parameter** can be a *Closure* or a *Controller action*. We'll get to Controllers later, so for now we're passing a Closure (aka an Anonymous function).

>> &ldquo;*Anonymous functions, also known as closures, allow the creation of functions which have no specified name. They are most useful as the value of callback parameters, but they have many other uses.*&rdquo; ([src](http://us2.php.net/manual/en/functions.anonymous.php))

Whatever response is returned from the second parameter will be displayed in the browser.

<img src='http://making-the-internet.s3.amazonaws.com/laravel-route-breakdown@2x.png' class='' style='max-width:861px; width:100%' alt='Route breakdown'>



## POST example
You can have two routes with the same URI that react to different http methods, e.g.:

```php
Route::get('/books/create', function() {

	$view  = '<form method="POST" action="/books/create">';
    $view .= csrf_field(); # This will be explained more later
	$view .= 'Title: <input type="text" name="title">';
	$view .= '<input type="submit">';
	$view .= '</form>';
	return $view;

});


Route::post('/books/create', function() {

	dd(Request::all());

});
```

Aside: Ultimately we never want to see HTML output directly from routes as we see in the GET route above; we're taking some liberties here to make this example simple.



## Route parameters
You can make route URI's more flexible with route parameters. These parameters are indicated with curly brackets `{ }` and act as placeholders that will detect patterns in the URL.

For example:

```php
Route::get('/books/show/{title}', function($title) {
	return 'Results for the book: '.$title;
});
```

Test the above route by visiting `http://localhost/books/show/war-and-peace`

Then test the above route, purposefully excluding the book title, `http://localhost/books/show`; this should throw a `NotFoundHttpException` error.

Rather than throw an error, you can make the title route parameter *optional* by adding a `?`, and setting a default value of `''` (empty string). Then, you can create a more useful response for the user as to why their request did not work.

To demonstrate this, replace the above /books/show route with this updated version:
```php
Route::get('/books/show/{title?}', function($title = '') {

    if($title == '') {
        return 'Your request did not include a title.'
    }

	return 'Results for the book: '.$title;
});
```

Again visit the URL excluding the title, `http://localhost/books/show` and note the difference.

Aside: In this example, we choose `title` as the identifier to load a specific book; how might this cause problems as this application grows?



## Naming routes
The `name` method can be added to routes to name a route.

```php
Route::get('/books/show/{title?}', function($title = '') {

    [...]

})->name('books.show');
```

This will make it convenient to refer or link to that route later using the global route function, e.g.

```html
<a href="<?php echo route('books.show','war-and-peace') ?>">Click here to learn more about War and Peace...</a>
```


## Conventions
Ultimately, it's up to you to design your route URIs and names.

Instead of the `/books/show/{title?}` URI you might have chosen any of the following alternative designs:

+ `/books/{title}`
+ `/books/{title}`
+ `/book/{title}/show`
+ `/book/{title}/view`

However, it can be useful to have conventions to follow, as conventions can lead to more consistent code and fewer mistakes.

Thus, in this course, we will follow the following conventions when designing routes for resources in our application, for example, books:

| Method  | URI  | Action   | Name  |
|---|---|---|---|---|
| GET  | /books | index  | books.index  |
| GET  | /books/create  | create  | books.create  |
| POST  | /books  | store  | books.store   |
| GET | /books/{book}  | show  | books.show   |
| GET | /books/{book}/edit  | edit  | books.edit  |
| PUT | /books/{book}  | update  | books.update  |
| DELETE | /books/{book}   | destroy   | books.destroy  |

Some notes about the conventions/patterns we see in this table:

+ The resource name (`books`) is plural
+ Some of the URIs are the same, but the Methods are distinct so there won't be a conflict
+ Each name is prefixed with the name of the resource, e.g. `books.create`, `books.store`
+ You can ignore the Action column for now; we'll come back to that when we discuss Controllers

These conventions are taken from the patterns seen with [RESTful design](https://en.wikipedia.org/wiki/Representational_state_transfer), and [Laravel Resource Controllers](https://laravel.com/docs/5.3/controllers#resource-controllers). (More on this in the next note set on Controllers)

Of course, there will be routes in our application not based upon specific resources. For example, you might have a *Contact Us* page, or a *Help & Support* page. For cases like these, you can come up with your own, user-friendly URIs, for example `/contact` and `/help`.




## Set up a practice route
In `routes/web.php` define a practice route:

```php
Route::get('/practice', function() {

	echo 'Hello from the practice route...';

})->name('practice');
```

Load this route ala `http://localhost/practice` and make sure you see *Hello from the practice route...* on the screen.

For now on, whenever these notes say *"try this in your practice route"* we mean running the code in this route closure.

For example, if we say:

>> Run `echo App::environment();` in your practice route to see how you can get Laravel to tell you what environment you're on.

...we're expecting you to do this:

```php
Route::get('/practice', function() {
	echo App::environment();
});
```




## Artisan routes
Artisan is a PHP command line tool that ships with Laravel and provides many shortcuts and utilities for working with your application.

For our first example of what Artisan can do, have it tell you what routes are set up in your application:

```bash
$ php artisan route:list
```

Example output:

```xml
+--------+----------+---------------------+------+---------+--------------+
| Domain | Method   | URI                 | Name | Action  | Middleware   |
+--------+----------+---------------------+------+---------+--------------+
|        | GET|HEAD | /                   |      | Closure | web          |
|        | GET|HEAD | api/user            |      | Closure | api,auth:api |
|        | POST     | books/create        |      | Closure | web          |
|        | GET|HEAD | books/create        |      | Closure | web          |
|        | GET|HEAD | books/show/{title?} |      | Closure | web          |
|        | GET|HEAD | practice            |      | Closure | web          |
+--------+----------+---------------------+------+---------+--------------+
```

Note: The `php artisan` command must always be run from *within* your application directory.




## Read More
+ https://laravel.com/docs/routing
