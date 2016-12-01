The following is a very rough outline of the modifications I'll make to foobooks during Lecture 13.

This should not be considered a stand-alone document; for full details please refer to the lecture video and the foobooks code source.

## Integrating your existing data and authentication
Couple different approaches:

1. *One to Many*: Every book is connected to a single user
2. *Many to Many*: Individual books aren't associated with any one user. Instead users can favorite books, powered by a many to many relationship between books and users.

Let's go with #1.


## Connect books and users
To do this, we'll need a migration to update our `books` table to have the `user_id` foreign key. This is the same thing we did when connecting authors and books.

Create the migration:
```bash
$ php artisan make:migration connect_books_and_users
```

Fill in the migration:
```php
public function up()
{
    Schema::table('books', function (Blueprint $table) {

        # Add a new INT field called `user_id` that has to be unsigned (i.e. positive)
        $table->integer('user_id')->unsigned();

        # This field `user_id` is a foreign key that connects to the `id` field in the `authors` table
        $table->foreign('user_id')->references('id')->on('users');

    });
}

public function down()
{
    Schema::table('books', function (Blueprint $table) {

        # ref: http://laravel.com/docs/5.1/migrations#dropping-indexes
        $table->dropForeign('books_user_id_foreign');

        $table->dropColumn('user_id');
    });
}
```


## Update seeders
Because we've created a foreign key between `books` and `users`, make sure your UsersTableSeeder is invoked *before* the BooksTableSeeder:

```php
# DatabaseSeeder.php
$this->call(UsersTableSeeder::class);
$this->call(TagsTableSeeder::class);
$this->call(AuthorsTableSeeder::class);
$this->call(BooksTableSeeder::class);
$this->call(BookTagTableSeeder::class);
```

Finally, update the BooksTableSeeder so that each book is associated with a user.

```php
$author_id = Author::where('last_name','=','Fitzgerald')->pluck('id')->first();

DB::table('books')->insert([
    'created_at' => Carbon\Carbon::now()->toDateTimeString(),
    'updated_at' => Carbon\Carbon::now()->toDateTimeString(),
    'title' => 'The Great Gatsby',
    'author_id' => $author_id,
    'published' => 1925,
    'cover' => 'http://img2.imagesbn.com/p/9780743273565_p0_v4_s114x166.JPG',
    'purchase_link' => 'http://www.barnesandnoble.com/w/the-great-gatsby-francis-scott-fitzgerald/1116668135?ean=9780743273565',
    'user_id' => 1, # <--- NEW LINE
]);
```

Make the above change for all three books. In our example, we're giving all 3 seeded books to user id 1 (`jill@harvard.edu`).


## Update models
Next, update the `Book` and `User` model so they're aware of this new relationship.

Add this to `Book.php`:
```php
public function user() {
    return $this->belongsTo('App\User');
}
```

Add this to `User.php`:
```php
public function books() {
    return $this->hasMany('App\Book');
}
```


## Your Books
Setup complete! Now let's make it so that when a user is logged in they only see *their* books.
Update the `index` method in the BookController like so:

```php
/**
* GET
*/
public function index(Request $request)
{

    $user = $request->user();

    # Note: We're getting the user from the request, but you can also get it like this:
    //$user = Auth::user();

    if($user) {
        # Approach 1)
        //$books = Book::where('user_id', '=', $user->id)->orderBy('id','DESC')->get();

        # Approach 2) Take advantage of Model relationships
        $books = $user->books()->get();
    }
    else {
        $books = [];
    }

    return view('book.index')->with([
        'books' => $books
    ]);
}
```

__Test it out__
+ How many books does Jill see?
+ Update the `books` table to reduce the number of books that Jill sees and test it again.
+ How many books does Jamal see? (Should be none by default.)
+ What do you see when not logged in


## Associating books with the logged in user
Update BookController's `store` method:

```php
$book = new Book();
$book->title = $request->input('title');
$book->published = $request->input('published');
$book->cover = $request->input('cover');
$book->author_id = $request->author_id;
$book->purchase_link = $request->input('purchase_link');
$book->user_id = $request->user()->id; # <--- NEW LINE
$book->save();
```

Once a book is created it is tied to that user; because of this there's no need to do anything with the *Edit Book* in regards to user associations.


## Route adjustments for guests vs. users
Now that books are associated with specific users, it doesn't make sense that guests would be able to see an index of all the books. Instead, guests should see some sort of welcome page.

So let's create a generic welcome page via the `PageController.php` we created earlier in the semester:

```php
<?php
# /app/Http/Controllers/PageController.php

/**
*
*/
public function welcome(Request $request) {

    # Logged in users should not see the welcome page, send them to the books index instead.
    if($request->user())
        return redirect('/books');

    return view('welcome');
}
```

Update the `welcome` view to:
```php
# /resources/views/welcome.blade.php
@extends('layouts.master')

@section('title')
    Welcome to Foobooks
@endsection

@section('content')
    <p>
        Welcome to Foobooks, a personal book organizer.
        To get started <a href='/login'>log in</a> or <a href='/register'>register</a>.
    </p>
@endsection
```

Then update your main route (`/`) to look like this:

```php
Route::get('/', 'PageController@welcome');
```

And lock down the `/books` route with auth middleware:

```php
Route::get('/books', 'BookController@index')->name('books.index')->middleware('auth');
```

__Try it out...__
+ While logged out, visit `http://localhost/`&mdash; you should see a welcome page.
+ While logged out, try and visit `http://localhost/books`&mdash; you should be directed to the login page.
+ While logged in, visit `http://localhost/`&mdash; you should see the book listing page.
