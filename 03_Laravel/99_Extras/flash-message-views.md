## Summary
In Lecture 11, the topic came up of including HTML markup in flash messages. One way to do this is to display flash messages as views, rather than just strings.

Goals:
+ No markup in the controller
+ Any content coming from the database should be displayed using `{{ }}`, not `{{!! !!}}`.

Code from the following procedure is demonstrated on a branch of the foobooks code called [flash-message-views](https://github.com/susanBuck/foobooks/tree/flash-message-views).



## Procedure
In the controller, right before the redirect, flash a variable called `flash_view` that is set to an array.

```php
Session::flash('flash_view', ['updated-book', ['title' => $book->title]]);
return redirect('/books');
```

+ The 0th element in the array is the name of the view to used (`book_edits_saved`)
+ The 1th element in the array is an array of data that should be available to that view.

[See code...](https://github.com/susanBuck/foobooks/blob/flash-message-views/app/Http/Controllers/BookController.php#L103)

Then in the master layout use Blade's `@include` method to include the flash_view, passing it the necessary data.
```php
@if(Session::get('flash_view') != null)
    <div class='flash_message'>
        @include('messages.'.Session::get('flash_view')[0], Session::get('flash_view')[1])
    </div>
@endif
```
[See code...](https://github.com/susanBuck/foobooks/blob/flash-message-views/resources/views/layouts/master.blade.php#L25)

The view at `/resources/views/messages/updated-book.blade.php` contains:

```php
Your book <b>{{ $title }}</b> was added.
```

[See code...](https://github.com/susanBuck/foobooks/blob/flash-message-views/resources/views/messages/updated-book.blade.php)
