## What are Views?

> &ldquo;*Views contain the HTML served by your application and separate your controller/application logic from your presentation logic.*&rdquo; -[ref](https://laravel.com/docs/views)

+ Laravel uses the [Blade templating language](http://laravel.com/docs/blade).
+ View files are stored in in `/resources/views/`.
+ Files end with the `.blade.php` extension.

This note set will cover the fundamentals of Views and Blades. For comprehensive documentation, refer to the main Laravel docs: <http://laravel.com/docs/blade>.




## Basic example
Let's create a view for the book's show route.

If the following route does not exist in your foobooks example so far, go ahead and create it:

```php
Route::get('/books/{title}', 'BookController@show')->name('books.show');
```

Also make sure you have the corresponding controller action in `BookController.php`, which might look like this:

```php
    /**
     * Display the specified resource.
     *
     * @param  int  $title
     * @return \Illuminate\Http\Response
     */
    public function show($title)
    {
        return 'Show book '.$title;
    }
```

Our goal is to replace that `return 'Show book '.$title;` with a View file.



## Create your first view file
Start by creating a new, blank file in `resources/views/book/show.blade.php`.

(The `book` directory does not yet exist in `resources/views/`... you should create this when creating this file.)

Note how the file name `show.blade.php` ends in `.blade.php`. This is required in order to use the Blade templating engine.

__Organizing View files:__ How you organize your view files is up to you, but one suggested approach is to create a subfolder for each Controller. I.e. in this case we have a subfolder `/resources/views/book/` for the `BookController.php`.

Another suggested convention is to make the Controller's action name (`show()`) correspond to the file name (`show.blade.php`).

The benefit of these latter two conventions is it makes it easy to correlate controller actions to view files.



## Build your first View
With your new view file created, fill it with the following content:

```html
<!DOCTYPE html>
<html>
<head>
	<title>Show Book</title>
	<meta charset='utf-8'>
    <link href="/css/foobooks.css" type='text/css' rel='stylesheet'>
</head>
<body>

	<header>
		<img
        src='http://making-the-internet.s3.amazonaws.com/laravel-foobooks-logo@2x.png'
        style='width:300px'
        alt='Foobooks Logo'>
	</header>

	<section>
	    <h1>Show book: {{ $title }}</h1>
	</section>

	<footer>
		&copy; {{ date('Y') }}
	</footer>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

</body>
</html>
```

Note that most of the above file looks like regular HTML. The exception is the content in the `<section>` and `<footer>` where you see the double brackets being used (`{{ }}`). Those brackets are Blade syntax, and they are short for &ldquo;echo this to the page&rdquo;.

So when you see something like this...
```php
{{ $title }}
```

...it's just a Blade shortcut for this PHP code:
```php
<?php echo $title; ?>
```



## Use your first View
With your first View file built, the next step is to have the Controller load this View:

```php
    /**
     * Display the specified resource.
     *
     * @param  int  $title
     * @return \Illuminate\Http\Response
     */
    public function show($title)
    {
        return view('book.show')->with('title', $title);
    }
```

Observations about the above code:
+ Views can be loaded with the global helper `view()`.
+ Omit the `blade.php` extension since it's assumed. I.e. `show.blade.php` becomes just `show`.
+ The view file name is specified using dot notation. I.e. instead of writing `books/show` you write `books.show`.
+ You don't have to include `resources/views/` as part of the path to your view&mdash; it's assumed.
+ The `with()` method is used to pass data to the view.
+ That data is echo'd out in the view using this Blade syntax: `{{ $title }}`.

With the above code in place, test out your new view by visiting `http://foobooks.loc/books/war-and-peace` in your browser.


<img src='http://making-the-internet.s3.amazonaws.com/laravel-first-view-loaded@2x.png' style='max-width:508px; width:100%' alt=''>



## Template inheritance
If we continued down the path outlined above, we'd create individual Views for all of our Book routes. The problem with this is you'd end up with repeat code as each view would include your doctype, the `<head>`, the `<header>` with the logo, the `<footer>`, etc.

A better solution is to divide your views into layout templates and child views.

This is called __template inheritance__ and a simple example is conveyed in this graphic:

<img src='http://making-the-internet.s3.amazonaws.com/laravel-view-inheritance@2x.png' class='' style='max-width:773px; width:100%' alt=''>

The idea behind template inheritance is you create a __master layout__ view (blue diagram above) that contains common, shared parts that similar HTML files/views will need. For example, the doctype, the `<head>`, CSS includes, JS includes, menus, footers, etc. In your master layout, you'll define sections where content that's specific to different pages will go.

Then, you'll create __child__ views for individual pages (red diagrams above) that __inherit__ the master template and define specific content.




## Template inheritance example
Let's apply template inheritance to our book example thus far and start by creating a new layout view at `/resources/views/layouts/master.blade.php` (note `layouts` is a new subdirectory you'll use to organize all your layout Views.)

In this new file, add these contents:

```html
<!DOCTYPE html>
<html>
<head>
	<title>
        {{-- Yield the title if it exists, otherwise default to 'Foobooks' --}}
        @yield('title','Foobooks')
    </title>

	<meta charset='utf-8'>
    <link href="/css/foobooks.css" type='text/css' rel='stylesheet'>

    {{-- Yield any page specific CSS files or anything else you might want in the <head> --}}
    @yield('head')

</head>
<body>

	<header>
		<img
        src='http://making-the-internet.s3.amazonaws.com/laravel-foobooks-logo@2x.png'
        style='width:300px'
        alt='Foobooks Logo'>
	</header>

	<section>
        {{-- Main page content will be yielded here --}}
		@yield('content')
	</section>

	<footer>
		&copy; {{ date('Y') }}
	</footer>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

    {{-- Yield any page specific JS files or anything else you might want at the end of the body --}}
    @yield('body')

</body>
</html>
```

Note how everything in this master layout is pretty generic&mdash; it could be applied to any page of our Foobooks app thus far.

Next, *replace* the content in `/resources/views/book/show.blade.php` so it contains just this:

```html
@extends('layouts.master')


@section('title')
    Show book
@stop


{{--
This `head` section will be yielded right before the closing </head> tag.
Use it to add specific things that *this* View needs in the head,
such as a page specific stylesheets.
--}}
@section('head')
    <link href="/css/books/show.css" type='text/css' rel='stylesheet'>
@stop


@section('content')
    @if($title)
        <h1>Show book: {{ $title }}</h1>
    @else
        <h1>No book chosen</h1>
    @endif
@stop


{{--
This `body` section will be yielded right before the closing </body> tag.
Use it to add specific things that *this* View needs at the end of the body,
such as a page specific JavaScript files.
--}}
@section('body')
    <script src="/js/books/show.js"></script>
@stop

```

Run `books/show/war-and-peace` in your browser and test everything is working.

It should look exactly as it did before:

<img src='http://making-the-internet.s3.amazonaws.com/laravel-first-view-loaded@2x.png' style='max-width:508px; width:100%' alt=''>



## Practice

Following the above patterns, implement views for the remaining 3 book GET routes:

```
Route::get('/books', 'BookController@index')->name('books.index');
Route::get('/books/create', 'BookController@create')->name('books.create');
Route::get('/books/{title}/edit', 'BookController@edit')->name('books.edit');
```



## Read More
+ <https://laravel.com/docs/views>
+ <https://laravel.com/docs/blade>
