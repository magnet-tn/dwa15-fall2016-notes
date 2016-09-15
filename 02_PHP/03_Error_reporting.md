## File-level configuration
Add the following code to the top of your display page (before the doctype).

~~~php
<?php
error_reporting(E_ALL);       # Report Errors, Warnings, and Notices
ini_set('display_errors', 1); # Display errors on page (instead of a log file)
?>
~~~

These lines are indicating that your file should report and display all errors.

However, there may be instances where you have an error and you get zero feedback&mdash; you'll just see a blank white screen.

This can occur if you have a specific kind of error, a *parse error*. Parse errors will prevent the file from being parsed, and thus your page level configurations will never be processed [ref](http://stackoverflow.com/questions/16933606/error-reportinge-all-does-not-produce-error).


## Server-level configuration
You can also enable PHP errors on the server so that reporting will function, regardless of whether your script has a parse error.

To enable errors on the server, we first need to dig up some details about your PHP setup.

At the top of your page add the `phpinfo();` function to get a list of your current server configurations.

~~~php
<?php
phpinfo();
?>
~~~

From the resulting table, we're interested in the following:

* The location of your `php.ini` file. Example:
	* MAMP on Mac: `/Applications/MAMP/bin/php/php5.5.14/conf/php.ini`
	* XAMPP on PC: `C:\xampp\php\php.ini`

* What your `display_errors` configuration is set to.
* What your `error_reporting` configuration is set to.

To adjust these settings, track down the `php.ini` file and change `display_errors` to be `on` and `error_reporting` to be `E_ALL`.

**To make these changes take effect, restart your server**.


## Error Levels

<img src='http://making-the-internet.s3.amazonaws.com/php-errors-warning-notices.png?@2x' class='' style='max-width:676px; width:100%' alt='PHP Error Levels'>

### Error

*You are doing something so wrong the script will terminate.*

~~~php
echo "foo
echo "bar";
~~~

### Warning

*You are doing something wrong and it is very likely to cause errors in the future, so please fix it.*

~~~php
fopen('this-file-does-not-exist.txt');
~~~

### Notice

*You probably shouldn't be doing what you're doing, but I'll let you do it anyway.*

~~~php
echo $foobar;
~~~


On a **development / local server** it's suggested you have **all levels** of errors reported.

On a **production / live server** it's best practices to **suppress all** errors.


## Tips / Notes
* Read more: [php.net error reporting](http://www.php.net/manual/en/function.error-reporting.php)
