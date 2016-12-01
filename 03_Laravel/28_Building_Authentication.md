## Introduction
+ Use of authentication in web apps
+ Define: Authentication
    + Aka: Users, Authorization, Auth, etc.
    + Users can register/login/logout
    + App recognizes user
    + Terms: **User** = visitor, logged in **Guest** = visitor, not logged in


## What we need...
+ Registration page
+ Login page
+ Logout functionality
+ Forgot password page
+ Ability to &ldquo;recognize&rdquo; the user
    + Lock down pages/features depending on whether visitor is User/Guest
    + Display different content depending on whether visitor is User/Guest and which User
    + Etc...

Good news: Much of this is &ldquo;baked-in&rdquo; to Laravel...

### Auth config
Open `config/auth.php` to see the default configurations for how authentication works. For our purposes, we don't have to change anything here, but you should read though the well-commented options.


### Auth Controllers
Next, explore the Auth related controllers:

```xml
/app/Http/Controllers/Auth/ForgotPasswordController.php
                          /LoginController.php
                          /RegisterController.php
                          /ResetPasswordController.php
```


### User Model
Next, explore the User model: `app\User.php` designed to interact the `users` table which is created via the...

### User's table migration
Laravel ships with a users table migration at `database/migrations/2014_10_12_000000_create_users_table.php`.

Open this migration to see the design of the table via the up method.


```php
public function up()
{
    Schema::create('users', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
        $table->rememberToken();
        $table->timestamps();
    });
}
```

For most situations, you shouldn't edit any of these fields (Laravel's auth features need it to work), however, you can add *new* fields that might be specific to your application (e.g., `birth_date`, `postal_code`, `first_name`, `last_name`, etc.)

Make sure your migrations have been run and you have the `users` table in your database.

<img src='http://making-the-internet.s3.amazonaws.com/laravel-confirm-users-table-exists@2x.png' style='max-width:1102px; width:100%' alt=''>


## What else do we need? Views & Routes

The final two components we need is Routes and Views for a registration page, a login page, and some other necessary destinations.

To quickly generate all these necessary files (with some useful boilerplate code), run `php artisan make:auth` in the root of your project:

```
~/foobooks $ php artisan make:auth
Authentication scaffolding generated successfully.
```

After running this command, if you do a `git status` you can get a glance of what this command did...

```xml
	modified:   routes/web.php

Untracked files:
  (use "git add <file>..." to include in what will be committed)

	app/Http/Controllers/HomeController.php
	resources/views/auth/
	resources/views/home.blade.php
	resources/views/layouts/app.blade.php
```


### Views
Exploring your Views folder, you should see the following additions:

+ `resources/views/`
    + `home.blade.php`
    + `layouts/`
        + `app.blade.php`
    + `auth/`
        + `passwords/`
            + `email.blade.php`
            + `reset.blade.php`
        + `login.blade.php`
        + `register.blade.php`



### Routes
And if you open `routes/web.php` you'll see this line was added:

```
Auth::routes();
```

That one command creates all the necessary routes for a user authentication; to see all the routes, run `php artisan route:list`

<img src='http://making-the-internet.s3.amazonaws.com/laravel-auth-routes@2x.png' style='width:100%; max-width:1000px' alt=''>


## Try it: Register

Visit and test `http://localhost/register`

Upon filling out the form and submitting, you should be logged in and redirected to `http://localhost/home`.

How do you know if login worked? First, check your `users` table and make sure a new row was added.

Second, you can confirm you are logged in by adding and visiting this temporary route:

```php
Route::get('/show-login-status', function() {

    # You may access the authenticated user via the Auth facade
    $user = Auth::user();

    if($user)
        dump($user->toArray());
    else
        dump('You are not logged in.');

    return;
});
```

Here's the two different outputs you'll see from this debugging route, depending on whether you're logged in or not:

<img src='http://making-the-internet.s3.amazonaws.com/laravel-confirm-logging-in-worked@2x.png' style='max-width:1121px; width:100%' alt='Confirmed logging in worked'>


## Customize it: Logout

By default, logout is managed via a POST route, which requires a submission of a form.

To simplify things, let's override this default behavior and allow logout to be accessed via a GET route:

In your routes file, below `Auth::routes();` add a GET route for logout:

```php
Auth::routes();
Route::get('/logout','Auth\LoginController@logout')->name('logout');
```

Now you can logout by visiting/linking to `http://localhost/logout`


## Try it: login
Upon registration you were automatically logged in, so now let's test the login functionality independently.

Login at `http://localhost/login`.

Upon successful login you're redirected to `/home` by default.

To configure this this edit the line `$redirectTo = '/home';` in the following files to the destination of your choice.

+ `/app/Http/Controllers/Auth/`
    + `LoginController.php`
    + `RegisterController.php`
    + `ResetPasswordController.php`


## Your job: fix the views
Laravel builds the auth related views using Bootstrap and they all extend the layout `/layouts/app.blade.php`. As a result, they look quite different from what our app has looked like so far.

To bring the design of the auth views into line with our design, you'll need to edit the following files:

+ `resources/views/`
    + `auth/`
        + `passwords/`
            + `email.blade.php`
            + `reset.blade.php`
        + `login.blade.php`
        + `register.blade.php`


## More customization: Post-logout behavior
Right now, post-logout a user is sent back to the foobooks main page, but it may not be clear to the user that they were successfully logged out. It'd be nice to add a flash message letting the user know they were logged out.

The logout process is &ldquo;baked-in&rdquo; to the core Laravel files, so how can we add this customization?

In order to do this, you'll need to override the default `logout` method that is set in [AuthenticateUsers.php](https://github.com/laravel/framework/blob/5.2/src/Illuminate/Foundation/Auth/AuthenticatesUsers.php).

Note, we're *overriding*, not *overwriting*. Overwriting would imply we're changing `AuthenticateUsers.php` which we do not want to do as any changes could be lost in the next update since this is a vendor file.

Instead, we *override* this method my redefining it in the `LoginController.php`, changing/adding what we need:

```php
/**
 * Log the user out of the application.
 *
 * @param  Request  $request
 * @return \Illuminate\Http\Response
 */
public function logout(Request $request)
{
    $this->guard()->logout();

    $request->session()->flush();

    $request->session()->regenerate();

    Session::flash('flash_message','You have been logged out.'); # <-- NEW

    return redirect('/');
}

```

Note the addition of the flash message before the direct happens.

You'll need to also add the following use statements at the top of `LoginController.php`:

```php
use Illuminate\Http\Request;
use Session;
```

Now when you test your `/logout` again, you should be directed to `http://localhost/` with a flash message:
<img src='http://making-the-internet.s3.amazonaws.com/laravel-logout-confirmed@2x.png' style='max-width:853px; width:100%' alt=''>



## Seeds
At this point, you should have a working authentication system complete with login, registration, and logout.

In the next note set, we'll look at examples of utilizing authentication, but before we conclude here, let's set up a Seeder for the `users` table.

Like all the tables in your project, you want to seed your `users` table with sample data.

In this course, seeding the `users` table is important/required because it will save us time when helping you debug&mdash; if every student seeds with the same 2 example users (below) we can quickly log into your app without having to register a new user.

First step, create the `UsersTableSeeder`:

```bash
$ php artisan make:seeder UsersTableSeeder
```

In the resulting `/database/seeds/UsersTableSeeder.php` file update the run method with this code:

```php
/**
 * Run the database seeds.
 *
 * @return void
 */
public function run()
{

    # Define the users you want to add
    $users = [
        ['jill@harvard.edu','jill','helloworld'], # <-- Required for P4
        ['jamal@harvard.edu','jamal','helloworld'], # <-- Required for P4
        ['susanbuck@fas.harvard.edu','susan','helloworld'] # <-- Update with your own info, or remove
    ];

    # Get existing users to prevent duplicates
    $existingUsers = User::all()->keyBy('email')->toArray();

    foreach($users as $user) {

        # If the user does not already exist, add them
        if(!array_key_exists($user[0],$existingUsers)) {
            $user = User::create([
                'email' => $user[0],
                'name' => $user[1],
                'password' => Hash::make($user[2]),
            ]);
        }
    }
}
```

Note the array of users to be added at the start of the method. If you're working on P4, leave jill/jamal as they're a requirement of the project.

Don't forget to also update `/database/seeds/DatabaseSeeder.php` so the `run()` method invokes this new seeder:

```php
$this->call(UsersTableSeeder::class);
```

Test your new seeder to make sure it runs without error.

With this seeding in place, anyone in this course (myself, a TA, a classmate) can log into your site using `jill@harvard.edu` / `helloworld` or `jamal@harvard.edu` / `helloworld`.

Obviously, beyond the scope of this academic setting, should you take your application into the &ldquo;real world&rdquo;, you would want to remove these seeds or update them to use a stronger password.
