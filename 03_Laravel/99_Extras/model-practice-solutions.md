Solutions to practice problems in notes on Models

```php
/**
* Practice from notes on Models:
* Remove any books by the author “J.K. Rowling”
*/
public function example6() {

    Book::where('author','LIKE','J.K. Rowling')->delete();

    # Resulting SQL: delete from `books` where `author` LIKE 'J.K. Rowling'

    return 'Deleted all books where author like J.K. Rowling';
}


/**
* Practice from notes on Models:
* Find any books by the author Bell Hooks and update the author name to be bell hooks (lowercase).
*/
public function example5() {

    # Approach # 1
    # Get all the books that match the criteria
    $books = Book::where('author','=','Bell Hooks')->get();

    # Loop through each book and update them
    foreach($books as $book) {
        $book->author = 'bell hooks';
        $book->save();
    }

    # Resulting SQL:
    # Always:
    #   1) select * from `books` where `author` = 'Bell Hooks'
    # Only if there's something to update:
    #   2) update `books` set `updated_at` = '2016-04-12 18:46:04', `author` = 'bell hooks' where `id` = '8'

    # Approach #2
    Book::where('author', '=', 'Bell Hooks')->update(['author' => 'bell hooks']);
    # Resulting SQL:
    # Always:
    #   1) update `books` set `author` = 'bell hooks', `updated_at` = '2016-04-12 18:44:46' where `author` = 'Bell Hooks'

    return '"Bell Hooks" => "bell hooks"';
}


/**
* Practice from notes on Models:
* Retrieve all the books in descending order according to published date
*/
public function example4() {

    $books = Book::orderBy('published','desc')->get();

    dump($books->toArray());

    # Underlying SQL: select * from `books` order by `published` desc

}


/**
* Practice from notes on Models:
* Retrieve all the books in alphabetical order by title
*/
public function example3() {

    $books = Book::orderBy('title','asc')->get();

    dump($books->toArray());

    # Underlying SQL: select * from `books` order by `title` asc
}


/**
* Practice from notes on Models:
* Retrieve all the books published after 1950
*/
public function example2() {

    $books = Book::where('published','>',1950)->get();

    dump($books->toArray());

    # Underlying SQL: select * from `books` where `published` > '1950'

}


/**
* Practice from notes on Models:
* Show the last 5 books that were added to the books table
*/
public function example1() {

    # Ref: https://laravel.com/docs/5.2/queries#ordering-grouping-limit-and-offset
    $books = Book::orderBy('id', 'desc')->get()->take(5);

    dump($books->toArray());

    # Underlying SQL: select * from `books` order by `id` desc

}
```
