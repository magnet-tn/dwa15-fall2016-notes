## Purpose of environments
Building a modern web application often entails running that application in different environments that have different configuration needs.

For example, when running your application locally on your machine, you would define this as a `local` environment and it would have specific configuration needs:

+ Turn on all error reporting
+ Connect to a local, development database
+ Route outgoing mail through a service like [MailTrap.io](https://mailtrap.io/)

On the flip side, you also have your application running on a live server, which you would define as a `production` environment that may have these specific configuration needs:

+ Turn off all error reporting to the page
+ Connect to a live database
+ Send outgoing mail using a service like [SendGrid](https://sendgrid.com/)

`local` and `production` are the two most common environments, but you may have other ones like `staging`, `testing`, or more.

Additionally, each developer working on your project might also have their own local environment configurations.



## How they work
Laravel utilizes the [DotEnv](https://github.com/vlucas/phpdotenv) PHP library. Here's how it works:

There are global configurations defined in `config/` and then each specific environment can overwrite those configurations on a as-needed basis via a custom `.env` file.

Let's look at an example&mdash; your debugging configurations. When debugging is *on*, exceptions and errors are displayed in the browser. This is useful in local environments.

When debugging is *off*, exceptions and errors are suppressed and the user is shown a generic error page. This is useful in production environments.

If you open `/config/app.php`, you'll see this line that sets the debug configuration:

```php
'debug' => env('APP_DEBUG', false),
```

Here we see the [env](https://laravel.com/docs/helpers#method-env) helper function is setting 'debug' to the environment variable 'APP_DEBUG'. The second parameter, `false`, will be used if APP_DEBUG is not set.

If `APP_DEBUG` is not set, your application will default to debug = false, i.e. debugging is off.

This is a logical default.

If `APP_DEBUG` *is* set, it will be set in the `.env` at the root of your project:

```
APP_DEBUG=true
```

Because the `.env` file is __not__ tracked via git, it's possible that `APP_DEBUG` can be set to different values in different contexts (e.g. local v.s production).


## Consistent configurations
Not all configurations use the env helper method, for example in `config/session.php` encrypt is &ldquo;hardcoded&rdquo; as `false`:

```
'encrypt' => false,
```

This makes sense, because there's typically not a reason you would need to change this configuration in different environments.

However, if there is some reason you need to do just that, just edit the line to look like this:

```
'encrypt' => env('SESSION_ENCRYPT', false),
```

Now `encrypt` it will default to `false` but you have the option of overwriting it by setting `SESSION_ENCRYPT` in an environment's `.env` file.


## Reading configurations
In addition to examining your configuration files, you can see what specific configs are set to using the the global `config` helper function.

For example:

```php
# Echo out what the mail => driver config is set to
echo config('mail.driver');

# Dump *all* of the mail configs
dump(config('mail'))
```

Aside: [Wondering where you can quickly and easily test lines of code like these?](https://github.com/susanBuck/dwa15-fall2016-notes/blob/master/03_Laravel/99_Practice.md)


## What's my current environment?

You can find out the environment your application is currently running in using the Artisan `env` command.

```bash
$ php artisan env
Current application environment: local
```

Or you can output the environment using the App facade's `environment` method:

```php
echo \App::environment();
```

Aside: Wondering why there's a forward slash in the front of `App`? Read more: [Namespacing in Controllers](https://github.com/susanBuck/dwa15-fall2016-notes/blob/master/03_Laravel/12_Namespacing_in_Controllers.md)




## `.env.example`
Laravel comes with a `.env.example` file. While this file is not actually used anywhere, it can serve as a good template for suggested settings for your app.

This way, if you deploy to a new server, or a new developer clones your app to begin working on it, they can use `.env.example` as a starting point for their own `.env` file.


## Read More
+ <https://laravel.com/docs/configuration#environment-configuration>
