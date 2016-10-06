Throughout lecture and your practice work, we'll need a way to test out examples.

For example, in the notes on Environments, we learn that you can get config variables using the config helper, e.g.:

```php
echo config('mail.driver');
```

Where should you run this command to test it out? You have a few options.


## Option 1: Practice route
You can create a temporary practice route:

```py
Route::get('/practice', function() {
    echo config('mail.driver');
});
```

+ __Pros:__ Quick and easy
+ __Cons:__ Not ideal to clutter up your routes file, but not a big deal if you remember to remove it when done.


## Option 2: Practice controller + routes
Create a Controller dedicated to practice work. This is what I'll do for lecture.

In my web routes I'll add this:

```php
/**
* A quick and dirty way to set up a whole bunch of practice routes
* that I'll use in lecture.
*/
Route::get('/practice', 'PracticeController@index')->name('practice.index');
for($i = 0; $i < 100; $i++) {
    Route::get('/practice/'.$i, 'PracticeController@example'.$i)->name('practice.example'.$i);
}
```

This sets up 100 practice routes for my use during the semester, without having to hard code each one..

+ `http://localhost/practice/1`
+ `http://localhost/practice/2`
+ `http://localhost/practice/3`
+ ...etc

Then I'll have a dedicated PracticeController where I can create all my examples (I'll show this in lecture).

+ __Pros:__ It's convenient, and I'll have a running history of examples to be tracked in Github so they're available for your reference.
+ __Cons:__ Creating routes with a for loop is not a good idea for general use, but it's acceptable for learning purposes.



## Option 3 Artisan Tinker
If you run `php artisan tinker` in your Laravel project, you'll get an interactive console for tinkering with your app.

Example:

```xml
/Applications/MAMP/htdocs/foobooks (master)  $ php artisan tinker
Psy Shell v0.7.2 (PHP 5.6.25 — cli) by Justin Hileman
>>> echo config('mail.driver');
smtp⏎
=> null
```

Tip: Hit `ctrl` + `c` to exit the Tinker console.

+ __Pros:__ Convenient for quick examples, requires not additional files to clutter up your project.
+ __Cons:__ For lecture purposes this isn't ideal, because there will be no history of the examples, but feel free to use during your own practice.
