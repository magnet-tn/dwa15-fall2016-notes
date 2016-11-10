With all the database and table setup work behind us, it's time to turn our attention to working with data in our tables
from our applications. Specifically, we want to cover methods for __Creating__, __Reading__, __Updating__, and __Deleting__ data in tables.

In Laravel, there are two approaches to interacting with the database:

1. Using the __[QueryBuilder](http://laravel.com/docs/queries)__
2. Using __Models__ and __[Eloquent ORM](http://laravel.com/docs/eloquent)__

These notes will briefly discuss the QueryBuilder then focus mostly on Models.




## Laravel's Query Builder
Laravel's QueryBuilder is a Laravel component (accessed by the [DB Facade](https://laravel.com/api/5.3/Illuminate/Support/Facades/DB.html)) which makes it easy to create and run database queries.

Reminder: Add `use DB;` at the top of Controller files that will use the DB Facade. Alternatively, you can prefix any `DB` invocation with a slash, e.g. `\DB::`. The reason you need to do this is covered in [Namespacing in Controllers](https://github.com/susanBuck/dwa15-fall2016-notes/blob/master/03_Laravel/12_Namespacing_in_Controllers.md).

### Read
For example, here's how you would retrieve all the rows from the `books` table using the DB `get()` method, demonstrated in the foobook's PracticeController:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB; # <--- NEW

class PracticeController extends Controller
{

    public function example() {
        # Use the QueryBuilder to get all the books
        $books = DB::table('books')->get();

        # Output the results
        foreach ($books as $book) {
            echo $book->title;
        }
    }

# [...]
```

Note the addition of `use DB;` at the top of the Controller so the DB Class is accessible in the Controller's namespace.

Below is another example, with the addition of the `where()` method to filter the books that are retrieved (showing just the contents of the action method in the interest of brevity):

```php
# Use the QueryBuilder to get all the books where author is like "%Scott%"
$books = DB::table('books')->where('author', 'LIKE', '%Scott%')->get();

# Output the results
foreach($books as $book) {
    echo $book->title.'<br>';
}
```


### Create
The QueryBuilder can also be used to create new data. For example:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon; # <--- NEW

class PracticeController extends Controller
{

    public function example() {
        # Use the QueryBuilder to insert a new row into the books table
        # i.e. create a new book
        DB::table('books')->insert([
            'created_at' => Carbon\Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon\Carbon::now()->toDateTimeString(),
            'title' => 'The Great Gatsby',
            'author' => 'F. Scott Fitzgerald',
            'published' => 1925,
            'cover' => 'http://img2.imagesbn.com/p/9780743273565_p0_v4_s114x166.JPG',
            'purchase_link' => 'http://www.barnesandnoble.com/w/the-great-gatsby-francis-scott-fitzgerald/1116668135?ean=9780743273565',
        ]);

# [...]
```

The above code should look familiar because we used this QueryBuilder functionality in our seeders.

Note the addition of `use Carbon;` at the top of the Controller so the Carbon class is accessible in the Controller's namespace.



### Run your own SQL commands
Using the QueryBuilder, you can also execute your own SQL statements, for example:

```php
# Use the QueryBuilder to run a raw SQL select command
$books = DB::select("SELECT * FROM books WHERE author LIKE '%Scott%'");

# Output the results
foreach($books as $book) {
    echo $book->title.'<br>';
}
```

Take some time to skim through the Laravel documentation on QueryBuilder to get a sense of all the methods available to you: <http://laravel.com/docs/queries>. You'll see that much of what you can do in SQL is abstracted in the QueryBuilder:

+ Selects
+ Joins
+ Unions
+ Where clauses
+ Ordering
+ Grouping
+ Limiting
+ Offsetting
+ Inserting
+ Updating
+ Deleting

With practice, we will explore these different methods.

But first, we're going to switch gears now to Eloquent Models, which is a more &ldquo;sophisticated&rdquo; way of interfacing with the database. However, Eloquent Models have access to all of the methods provided by the QueryBuilder, so it's useful to be aware of the QueryBuilder documentation and functionality.




## Eloquent ORM
ORM (__Object-relational Mapping__) is the software practice of using Objects to represent data entities in your application.

In Laravel, the ORM system is called __Eloquent__.

Eloquent ORM powers the Models aspect of our MVC applications...




## Models
Models are the &ldquo;M&rdquo; of the MVC acronym. __A Model is a class that interfaces with a data entity in your application__.

For example...

+ We're going to create a Book Model that will interface with the `books` table.
+ We'll also have a User Model to interface with the `users` table.
+ And a Author Model to interface with a yet-to-be-created `authors` table.
+ Etc.

(In all of our examples, our data entities are database tables, but they can come from other sources, like a JSON API.)

For each of the core tables in the database, we're going to create a Model class that will interface with that table.




## Create an Model
Our first Model will be the Book Model, which will interface with the *books* table.

There's an Artisan command to generate a new Model for you:

```xml
php artisan make:model Book
```

This command will result in a new file, `app/Book.php` that has this boilerplate code:

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    //
}
```

Notice how this new class, Book, uses `Illuminate\Database\Eloquent\Model` and extends the `Model` class. This is where Eloquent comes in. By extending this class, we're saying our model (`Book`) wants to use all the functionality that comes with Eloquent ORM, which includes a ton of features for interacting with database tables.

__Convention over Configuration:__ In order for Eloquent to work its magic, there are a 3 conventions we should follow:

__1) Model's class name__

A Model's class name should be the singular version of the corresponding table name, and it should be capitalized.

So...

+ The `Book` model corresponds with the `books` table.
+ An `Author` model would interface with a `authors` table.
+ A `User` model would interface with a `users` table.


__2) Id field__

Tables should have a __Auto Incremented__, __Primary Key__ field named `id`. We discussed the special `id` field when we set up our migrations, so your `books` table should meet this criteria.

__3) Timestamps__

Tables should have `created_at` and `updated_at` fields. Again, this was something we covered during migrations, so your `books` table should be all set.

With this infrastructure in place, you can start to put your Model to work with some queries...




## CRUD - Creating
There's not much going on in our Book Model so far, but that bare minimum is all you need to start interfacing with the database. Just by extending Eloquent's `Model` class, you can start taking advantage of the methods provided by Eloquent.

The first method we want to look at is `save()` which will allow you to __create a new row in a table__.

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon;
use App\Book; # <--- NEW

class PracticeController extends Controller
{

    public function example() {

        # Instantiate a new Book Model object
        $book = new Book();

        # Set the parameters
        # Note how each parameter corresponds to a field in the table
        $book->title = 'Harry Potter';
        $book->author = 'J.K. Rowling';
        $book->published = 1997;
        $book->cover = 'http://prodimage.images-bn.com/pimages/9780590353427_p0_v1_s484x700.jpg';
        $book->purchase_link = 'http://www.barnesandnoble.com/w/harry-potter-and-the-sorcerers-stone-j-k-rowling/1100036321?ean=9780590353427';

        # Invoke the Eloquent save() method
        # This will generate a new row in the `books` table, with the above data
        $book->save();

        echo 'Added: '.$book->title;
    }

# [...]
```

Note the addition of `use App\Book;` at the top of the Controller so the Book class is accessible in the Controller's namespace.


## CRUD - Reading
Next, let's look at the Eloquent `all()` method to *read* data from a table.

```php
$books = Book::all();

# Make sure we have results before trying to print them...
if(!$books->isEmpty()) {

    # Output the books
    foreach($books as $book) {
        echo $book->title.'<br>';
    }
}
else {
    echo 'No books found';
}
```

You'll note in the first example of creating a new book we instantiated a Book object:

```php
$book = new Book();
```

In this above read example, though, we didn't instantiate an object but instead used the Book as a facade:

```php
$books = Book::all();
```

This could have also been written like so:
```php
$book  = new Book();
$books = $book->all();
```

Either style is fine.




## Query Structure
An Eloquent query may be made up of two methods:

1. The __constraint method__ (optional). Lets you specify only the data you need.
2. The __fetch method__. Runs the query and determines what results to get back.

In the above example, we didn't have a __constraint__, and we used the `all()` __fetch__ method:

```php
$books = Book::all();
```

This is one of the simplest kinds of queries you can do.

Most of your queries, however, will be more complex as you'll have certain constraints you'll want to employ.

For example...
+ Maybe you want only books published after 1950.
+ Or you want only books that have a cover image.
+ Or you want only books that were published after 1950 *and* have a cover image.
+ Etc.

Here's another example using a constraint and fetch method:

```php
# where() is the constraint method
# first() is the fetch method
$book = Book::where('author', 'LIKE', '%Scott%')->first();

if($book) {
    return $book->title;
}
else {
    return 'Book not found.';
}
```

There are many different kinds of constraints and fetch methods you can use. The following diagram outlines some of the most common ones.

<img src='http://making-the-internet.s3.amazonaws.com/laravel-eloquent-query-structure@2x.png' class='' style='max-width:1335px; width:100%' alt=''>

FYI: The fetch method always returns something called a Collection object, which is covered in more detail later.

With more information about query structure behind us, let's move on to Updating and Deleting.




## CRUD - Updating
To update a row in a table, you first have to select which row you wish to edit. Then, similar to the "Create" example above, you can alter the properties of the row and then save those changes using the `save()` method.

```php
# First get a book to update
$book = Book::where('author', 'LIKE', '%Scott%')->first();

# If we found the book, update it
if($book) {

    # Give it a different title
    $book->title = 'The Really Great Gatsby';

    # Save the changes
    $book->save();

    echo "Update complete; check the database to see if your update worked...";
}
else {
    echo "Book not found, can't update.";
}
```



## CRUD - Deleting
Similar to updating, when deleting a book, you have to first find the book you want to delete.

```php
# First get a book to delete
$book = Book::where('author', 'LIKE', '%Scott%')->first();

# If we found the book, delete it
if($book) {

    # Goodbye!
    $book->delete();

    return "Deletion complete; check the database to see if it worked...";

}
else {
    return "Can't delete - Book not found.";
}
```




## Another look at ORM
Now that we've seen Eloquent Models in action, let's revisit the definition of ORM.

As stated above, ORM (__Object-relational Mapping__) is the software practice of using objects to represent data entities in your application.

In our setup, our objects are called Models.

The Models represent data entities in our application (Book, Author, User, etc.)

There is a mapping of the object's properties to fields in the database tables.

We saw this when we did something like `$book->title`.
The object was `$book`, the property was `title`
This *maps* to a table called `books` with a field called `title`.

Here's a big picture view of this:

<img src='http://making-the-internet.s3.amazonaws.com/laravel-orm-books@2x.png' style='max-width:1059px; width:100%' alt='ORM Diagram'>

If any of the above discussion of objects, properties, etc. is confusing, take a moment to refresh your memory on [Object Oriented Programming](https://github.com/susanBuck/dwa15-fall2016-notes/blob/master/03_Laravel/00_OOP_Primer.md).




## Practice
+ Retrieve the last 5 books that were added to the `books` table.
+ Retrieve all the books published after 1950.
+ Retrieve all the books in alphabetical order by title.
+ Retrieve all the books in descending order according to published date.
+ Find any books by the author `Bell Hooks` and update the author name to be `bell hooks` (lowercase).
+ Remove any books by the author &ldquo;J.K. Rowling&rdquo;.




## A different perspective...
While we spend a lot of time in this course deep in the workings of Laravel, it's important to understand that the paradigms we are working with are often universal across web programming software.

As an example of this, read about [Ruby On Rail's Active Records](http://guides.rubyonrails.org/active_record_basics.html) and note the similarities to Laravel's Eloquent ORM.



## Read More
+ <http://laravel.com/docs/queries>
+ <http://laravel.com/docs/eloquent>
