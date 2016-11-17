The following is a very rough outline of the modifications I'll make to foobooks during Lecture 12.

This should not be considered a stand-alone document; for full details please refer to the lecture video and the foobooks code source.



## Book index clean-up
Updated the `book/index.blade.php` view, putting each book in a section so it can be styled.

Also added *Edit* and *View* links.

```php
<h1>All the books</h1>

@if(sizeof($books) == 0)
    You have not added any books, you can <a href='/book/create'>add a book now to get started</a>.
@else
    <div id='books' class='cf'>
        @foreach($books as $book)

            <section class='book'>
                <a href='/books/{{ $book->id }}'><h2 class='truncate'>{{ $book->title }}</h2></a>

                <img class='cover' src='{{ $book->cover }}' alt='Cover for {{ $book->title }}'>

                <br>
                <a href='/books/{{ $book->id }}/edit'><i class='fa fa-pencil'></i> Edit</a><br>
                <a href='/books/{{ $book->id }}'><i class='fa fa-eye'></i> View</a><br>
            </section>

        @endforeach
    </div>
@endif
```

Added `/public/css/book.css`:
```css
.book {
    border:1px solid #ccc;
    margin-right:10px;
    margin-bottom:10px;
    min-width:300px;
    width:25%;
    float:left;
    padding:10px;
}

.book h2 {
    font-size:1.2em;
    margin:1px;
}

.book h3 {
    font-size:1em;
    margin:1px;
}
```

Linked it at the top of `books/index.blade.php`:

```php
@section('head')
    <link href='/css/book.css' rel='stylesheet'>
@stop
```



## Author dropdown
To associate Authors with Books we'll use a dropdown filled with authors.

To make this happen, we first need an __array of authors__ where the key is the author `id` and the value is the author `name`.

```php
# BookController.php
public function edit($id) {

    $book = Book::find($id);

    # Get all the authors
    $authors = Author::orderBy('last_name', 'ASC')->get();

    # Organize the authors into an array where the key = author id and value = author name
    $authors_for_dropdown = [];
    foreach($authors as $author) {
        $authors_for_dropdown[$author->id] = $author->last_name.', '.$author->first_name;
    }

    [...]

    # Make sure $authors_for_dropdown is passed to the view
    return view('book.edit')->with([
        'book' => $book,
        'authors_for_dropdown' => $authors_for_dropdown
    ]);
```

Then, we can construct the dropdown (`<select>`) using this data:

```html
<label for='author_id'>* Author:</label>
<select id='author_id' name='author_id'>
    @foreach($authors_for_dropdown as $author_id => $author_name)
         <option value='{{ $author_id }}' {{ ($book->author_id == $author_id) ? 'SELECTED' : '' }}>
             {{$author_name}}
         </option>
     @endforeach
</select>
```


Now in `update()` set the `author_id` using the data from the dropdown in `$request`:
```php
$book->author_id = $request->author_id;
```

Test it out to make sure it's working.



## Custom Model Methods
We'll want to do the same thing on the *Add a Book* page.

Rather than duplicate the &ldquo;get authors for dropdown&rdquo; code, we should extract it out of the controller and add it as a method to the `Author` model:

```php
# app/Author.php
public static function authorsForDropdown() {

    $authors = Author::orderBy('last_name', 'ASC')->get();
    $authors_for_dropdown = [];
    foreach($authors as $author) {
        $authors_for_dropdown[$author->id] = $author->last_name.', '.$author->first_name;
    }

    return $authors_for_dropdown;
}
```

Then in `edit()`:
```
$authors_for_dropdown = Author::authorsForDropdown();
```

__Discussion:__ &ldquo;Skinny controllers, fat models&rdquo;

Other frequently used queries can also be abstracted, for example the following method could be added to the Book model:

```php
public static function getAllBooksWithAuthors() {
    return Book::with('author')->orderBy('id','desc')->get();
}
```

## On your own
Update the *Add a Book* page to also have an author dropdown.


## Using Tags/Many to Many
We have everything set up for a tags feature&mdash; migrations, seeders, models&mdash; now let's look at how we'd implement tags.

We need a way to associate tags with books (either from the *Edit Book* or *Create Book* page)

For authors, this was done with a dropdown which worked because each book can have only *one* author.

Each book can have *many* tags, though, so a dropdown won't do. Instead, lets show all possible tags with checkboxes.

<img src='http://making-the-internet.s3.amazonaws.com/laravel-foobooks-tag-checkboxes@2x.png' style='max-width:357px; width:100%' alt='Tags checkboxes'>

To accomplish this, we'll need to gather the following data:

1. All the possible tags
2. All the tags associated with the book we're looking at.


First, a `getTagsForCheckboxes()` method in the Tag model:

```php
# app/Tag.php
public static function getTagsForCheckboxes() {

    $tags = Tag::orderBy('name','ASC')->get();

    $tagsForCheckboxes = [];

    foreach($tags as $tag) {
        $tagsForCheckboxes[$tag['id']] = $tag->name;
    }

    return $tagsForCheckboxes;

}
```

Then update the `edit` method in `BookController`:

```php
# BookController.php
public function edit($id = null) {

    # Get this book and eager load its tags
    $book = Book::with('tags')->find($id);

    # Get all the possible tags so we can include them with checkboxes in the view
    $tags_for_checkbox = Tag::getTagsForCheckboxes();

    # Create a simple array of just the tag names for tags associated with this book;
    # will be used in the view to decide which tags should be checked off
    $tags_for_this_book = [];
    foreach($book->tags as $tag) {
        $tags_for_this_book[] = $tag->name;
    }
    # Results in an array like this: $tags_for_this_book['novel','fiction','classic'];

    return view('book.edit')
        ->with([
            'book' => $book,
            'authors_for_dropdown' => $authors_for_dropdown,
            'tags_for_checkbox' => $tags_for_checkbox,
            'tags_for_this_book' => $tags_for_this_book,
        ]);

}
```

```php
# /resources/views/book/edit.blade.php

[...]

@foreach($tags_for_checkbox as $tag_id => $tag_name)
    <input
        type='checkbox'
        value='{{ $tag_id }}'
        name='tags[]'
        {{ (in_array($tag_name, $tags_for_this_book)) ? 'CHECKED' : '' }}
    >
    {{ $tag_name }} <br>
@endforeach

[...]
```


```php
# BookController.php
public function update(Request $request, $id) {

    # [...Validation removed for brevity...]

    # Find and update book
    $book = Book::find($request->id);
    $book->title = $request->title;
    $book->cover = $request->cover;
    $book->published = $request->published;
    $book->purchase_link = $request->purchase_link;
    $book->save();

    # If there were tags selected...
    if($request->tags) {
        $tags = $request->tags;
    }
    # If there were no tags selected (i.e. no tags in the request)
    # default to an empty array of tags
    else {
        $tags = [];
    }

    # Above if/else could be condensed down to this: $tags = ($request->tags) ?: [];

    # Sync tags
    $book->tags()->sync($tags);
    $book->save();

    # [... Finish removed for brevity ..]

}
```

Test it out by removing and adding tags.
