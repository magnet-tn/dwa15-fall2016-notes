To demonstrate forms, lets create a form to add a new book in Foobooks.

The following form code should display when you visit `/books/create`:

```html
<form method='POST' action='/books'>
    {{ csrf_field() }}
    <input type='text' name='title'>
    <input type='submit' value='Submit'>
</form>
```

The above form will submit to `/books` via the `POST` method.

To accept this submission, we already have a route that looks like this:

```php
Route::post('/books', 'BookController@store')->name('books.store');
```

...which points to this action method in the BookController:

```php
/**
 * Store a newly created resource in storage.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 */
public function store(Request $request)
{
    return 'Process adding new book: '.$_POST['title'];
}
```

With the above setup, you can test the form processing flow by visiting `/books`, entering a title, and submitting the form.


## CSRF Protection in Laravel
The above form includes this line:

```html
{{ csrf_field() }}
```

Which will translate to something like this in your HTML;

```html
<input type='hidden' name='_token' value='Qw7HOT2zXe1ouFUuNov73baXFZTz4nHdf0CyJvZe'>
```

This line is necessary when submitting forms with `POST` (or `PUT` or `DELETE`) because it guards against CSRF attacks (explained below).

If you don't have the CSRF token in your form, you'll get a `TokenMismatchException` error when trying to submit your form using the `POST`, `PUT`, or `DELETE` methods.

CSRF protection in Laravel is an example of [__Middleware__](http://laravel.com/docs/middleware#terminable-middleware):

> *&ldquo;HTTP middleware provide a convenient mechanism for filtering HTTP requests entering your application. For example, Laravel includes a middleware that verifies the user of your application is authenticated. If the user is not authenticated, the middleware will redirect the user to the login screen. However, if the user is authenticated, the middleware will allow the request to proceed further into the application.*

> *Of course, additional middleware can be written to perform a variety of tasks besides authentication. A CORS middleware might be responsible for adding the proper headers to all responses leaving your application. A logging middleware might log all incoming requests to your application.*

> *There are several middleware included in the Laravel framework, including middleware for maintenance, authentication, CSRF protection, and more. All of these middleware are located in the app/Http/Middleware directory.&rdquo;*




## What is CSRF?
CSRF, or __Cross-Site Request Forgery__, is a type of web application vulnerability where a user unintentionally runs a script in their browser that takes advantage of their logged in status on a target site.

For example, imagine you set up a route so that when users go to `http://yourdomain.com/users/delete` it will delete their account.

You've implemented proper JavaScript checking that confirms with the user they wish to delete their account before ever triggering the `/users/delete` method.

However, a hacker has dug through your source code to find this URL and wishes to trick your users into deleting their accounts.

To accomplish this, they could present the victim with an image (in a forum post, in a email, etc.) with the delete URL set as the image src, which would execute `http://yourdomain.com/users/delete`:

```html
<img src='http://yourdomain.com/users/delete'>
```

Alternatively, they could trick you into clicking on a link leading directly to the delete page:

```html
<a href='http://yourdomain.com/users/delete'>Free stuff!</a>
```

In the above examples, the hacker is taking advantage of the fact that the victim user is potentially already logged in on the victim site.

[Here's another example...](https://gist.github.com/susanBuck/dc9bdf3eccb15314f31d)

To prevent CSRF attacks you want to verify that the origin of requests on your site are coming from within your site. This is done by sending a unique, encrypted token with each form submission, the CSRF token.

When the form submission is received by your application, Laravel automatically checks the token to make sure it's valid, thereby verifying the form submissions is legitimate.




## Excluding from CSRF checking
There may be instances where you want to bypass CSRF checking. This can be done by adding specific URLs to the `$except` array in `app/Http/Middleware/VerifyCSRFToken.php`, for example:

```php
protected $except = [
    '/books',
];
```




## Form methods other than POST and GET
In addition to POST and GET, there are three other HTTP methods you might wish to use when processing your forms: `PUT`, `PATCH`, and `DELETE`. These latter methods are not actually available in HTML forms, but Laravel has [Form Method Spoofing](http://laravel.com/docs/routing#form-method-spoofing) which lets you mimic them.

To do this, use the `method_field` helper method:

```html
<form method='POST' action='/books/create'>
    {{ method_field('PUT') }}
    {{ csrf_field() }}
    <input type='text' name='title'>
    <input type='submit' value='Submit'>
</form>
```

This will add a hidden field to your form, used to indicate to Laravel what method you're using:

```html
<input type="hidden" name="_method" value="PUT">
```




## Accessing form data using the Request class
As mentioned above, when we submit this form it submits to the `store` method in the BookController:

```php
/**
 * Store a newly created resource in storage.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 */
public function store(Request $request)
{
    return 'Process adding new book: '.$_POST['title'];
}
```

Up until this point, we retrieved data from the form using PHP's superglobal `$_POST`. This works, but there's a better/more sophisticated way...

In Laravel, request information is made available via a `Request` class. To use the `Request` class, start by making sure this `use` statement at the top of your Controller:

```php
use Illuminate\Http\Request;
```

Because we used Artisan to initially create the BookController as a resource controller, this statement should already be there. If you manually created your controller, you'll need to add it.

Next, you need to make sure the `Request` object is available to the action action in which you want to use it, which in this case is `store`:

```php
public function store(Request $request) {

```
Once again, this should be already done if you generated your controller using Artisan.

Once you do that, your action method will have access to the `$request` object, and that can replace `$_POST`:

```php
/**
 * Responds to requests to POST /books/create
 */
public function store(Request $request) {
    $title = $request->input('title');
    return 'Process adding new book: '.$title;
}
```



## More methods available in the Request object
In the above example, we used the Request object's `input` method to get the data from one particular field in the form:

```php
$title = $request->input('title');
```

This is just one of the methods available to us from the Request object. Another example is the `all` method, which as the name suggests, gets all the data from the form:

```php
$data = $request->all();
```

Here are some other notable examples of methods for retrieving data from the Request object:
```php
# Get the input 'name', but if it's not there, default to 'Sally'
$name = $request->input('name', 'Sally');

# Get only the username and password
$input = $request->only(['username', 'password']);
$input = $request->only('username', 'password');

# Get everything except `credit_card`
$input = $request->except(['credit_card']);
$input = $request->except('credit_card');
```

The Request object holds more info than just form field data. For example, you can also get Query String info using the `query` method.

```php
# Example URL: http://domain.com?limit=100
$limit = $request->query('limit');
```

Or maybe you need to determine if the request is coming via Ajax. For that you can use the `ajax` method:

```php
$isAjax = $request->ajax();
```

To see a full list of methods available to the Request object, refer to the [API documentation](https://laravel.com/api/5.3/Illuminate/Http/Request.html) and the [docs on Requests](https://laravel.com/docs/5.3/requests#accessing-the-request).




## Dependency Injection
The above practice of passing `Request $request` to the postCreate method is an example of __dependency injection__.

```php
public function store(Request $request) {

    [...]

}
```

Dependency injection is a programming practice that lets you provide (i.e. inject) class dependencies on a method by method basis. The benefit of doing this is you can easily swap out different dependencies or &ldquo;mock&rdquo; dependencies for testing purposes.

Dependency injection is a more advanced topic, and not something you need to master right now. It's mentioned here just so you know why the `Request $request` object was passed to the method in the way that it was.


<!--
## Laravel Collective: Forms & HTML
Laravel 4 included a `Form` helper that included lots of helpers to generate form tags. This feature was removed in Laravel 5, but a group of developers have maintained the feature as a 3rd-party package making it possible to add it back. If interested, you can learn more about this package here: [Laravel Collective: Forms & HTML](http://laravelcollective.com/docs/5.1/html).
 -->
