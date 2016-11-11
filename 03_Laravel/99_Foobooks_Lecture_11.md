The following is a rough outline of the modifications I'll make to foobooks during Lecture 11.

This should not be considered a stand-alone document; for full details please refer to the lecture video and the foobooks code source.




## Pre-lecture setup
Update `books/create.blade.php` to include `published`, `cover`, `purchase_link` fields.

Purposefully omitting Author, because we'll be restructuring that later in the lecture.


## List all the books
`books/index.blade.php` is created, just needs to be filled in.

Use the `Book` model to get all the books.
Pass this data to the View.

```php
public function index() {
    $books = Book::all();
    return view('books.index')->with('books',$books);
}
```

Display the data in the View by looping through it.
```php
@extends('layouts.master')

@section('title')
    All the books
@stop

@section('content')
    <div class='book'>
        @foreach($books as $book)
            <h2>{{ $book->title }}</h2>
            <img src='{{ $book->cover }}'>
        @endforeach
    </div>
@stop
```




## Make /books the homepage as well
```php
Route::get('/', 'BookController@index');
```




## Add a book
`books/create.blade.php` has been updated to include all the fields:

+ published
+ cover
+ purchase_link

Note the use of `old()` for field value.

Update this so we have some default values to rapidly test with, e.g.

```html
<div class='form-group'>
   <label>* Title:</label>
    <input
        type='text'
        id='title'
        name='title'
        value='{{ old('title','Green Eggs and Ham') }}'
    >
   <div class='error'>{{ $errors->first('title') }}</div>
</div>
```

Data to use:
+ Title: `Green Eggs & Ham`
+ Published date: `1960`
+ Image URL: `http://prodimage.images-bn.com/pimages/9780394800165_p0_v4_s192x300.jpg`
+ Purchase URL: `http://www.barnesandnoble.com/w/green-eggs-and-ham-dr-seuss/1100170349`

In BookController.php `store()` validate the additional fields (add these after the title validation)

```php
'published'     => 'required|min:4|numeric',
'cover'         => 'required|url',
'purchase_link' => 'required|url',
```

Then save the book:
```php
$book = new Book();
$book->title = $request->title;
$book->published = $request->published;
$book->cover = $request->cover;
$book->purchase_link = $request->purchase_link;
$book->save();
```

If we keep refreshing the post page, it keeps adding new books. Solve with a redirect.

So far most of our controller methods have been returning Strings or Views.
You can also return a [redirect](http://laravel.com/docs/responses#redirects).

Redirect to a confirmation page...

Or some other logical place like `/books` to view all books.

```php
return redirect('/books');
```

If you do the latter, you will want to [Flash](http://laravel.com/docs/session#flash-data) a confirmation message.

Before redirecting:
```
Session::flash('flash_message','Your book was added');
```

Explanation of a __flash message__

Explanation of a __Session__

(More on the topic of Sessions when we get to user authentication...)

Then in `master.blade.php`:

```php
@if(Session::get('flash_message') != null))
    <div class='flash_message'>{{ Session::get('flash_message') }}</div>
@endif
```

Which can be styled however you want:

```
.flash_message {
    width:100%;
    text-align:center;
    padding:5px;
    position:fixed;
    top:0;
    left:0;
    background-color:yellow;
    font-weight:bold;
}
```




## Edit a book

Start `books/edit.blade.php` by copying `books/create.blade.php`.

In order to edit a book, we need to know *which* book to edit.

Update edit route to use `{id}` rather than `{title}`

Hide the id in the form as a hidden field.

```php
<input type='hidden' name='id' value='{{ $book->id }}'>
```

That way when the form is submitted, the controller action can retrieve the book id to know which book to edit:

```php
$book = Book::find($id);
return view('book.edit')->with('book', $book);
```

Test it with ids 1,2,3, etc.

Add links from individual book page.

Handle a bad id:
```php
if(is_null($book)) {
    Session::flash('flash_message', 'Book not found');
    return redirect('/books');
}
```

Do the edit:
```php
$book = Book::find($request->id);

$book->title = $request->title;
$book->cover = $request->cover;
$book->published = $request->published;
$book->purchase_link = $request->purchase_link;

$book->save();

return 'Book was saved'.
```


Better return:
```php
Session::flash('flash_message','Your changes were saved');
return redirect('/books/edit/'.$request->id);
```


## Delete a book
Try this one on your own.
