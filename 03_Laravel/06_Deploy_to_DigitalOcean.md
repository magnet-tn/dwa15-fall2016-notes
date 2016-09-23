The following steps outline the procedure for deploying a new Laravel application on our DigitalOcean Droplets:

Summary:

1. Clone your Laravel app
2. Build the `vendor/` directory via `composer install`
3. Set permissions
4. Set up `.env` file
5. Configure subdomain



## Clone your Laravel app
While SSH'd into your Droplet, navigate into your document root at `/var/www/html`:

```xml
$ cd /var/www/html
```

Now use Git to clone your project from Github. In the following example I'm showing `foobooks` being cloned from my Github account. Edit as necessary for whatever app you're deploying.

```xml
$ git clone git@github.com:susanBuck/foobooks.git
```

Navigate into the resulting directory (in this case, it's `foobooks`) and use the `ll` command to confirm all your files are there.

```xml
$ cd foobooks
$ ll
drwxr-xr-x 13 root     root   4096 Sep 22 18:53 ./
drwxr-xr-x  4 root     root   4096 Sep 22 18:46 ../
drwxr-xr-x  6 root     root   4096 Sep 22 18:46 app/
-rwxr-xr-x  1 root     root   1646 Sep 22 18:46 artisan*
drwxr-xr-x  3 root     root   4096 Sep 22 18:46 bootstrap/
-rw-r--r--  1 root     root   1283 Sep 22 18:46 composer.json
-rw-r--r--  1 root     root 124068 Sep 22 18:46 composer.lock
drwxr-xr-x  2 root     root   4096 Sep 22 18:46 config/
drwxr-xr-x  5 root     root   4096 Sep 22 18:46 database/
-rw-r--r--  1 root     root    491 Sep 22 18:46 .env.example
drwxr-xr-x  8 root     root   4096 Sep 22 18:46 .git/
-rw-r--r--  1 root     root     61 Sep 22 18:46 .gitattributes
-rw-r--r--  1 root     root     80 Sep 22 18:46 .gitignore
-rw-r--r--  1 root     root    556 Sep 22 18:46 gulpfile.js
-rw-r--r--  1 root     root    400 Sep 22 18:46 package.json
-rw-r--r--  1 root     root    930 Sep 22 18:46 phpunit.xml
drwxr-xr-x  4 root     root   4096 Sep 22 18:46 public/
-rw-r--r--  1 root     root     21 Sep 22 18:46 readme.md
drwxr-xr-x  5 root     root   4096 Sep 22 18:46 resources/
drwxr-xr-x  2 root     root   4096 Sep 22 18:46 routes/
-rw-r--r--  1 root     root    563 Sep 22 18:46 server.php
drwxr-xr-x  5 root     root   4096 Sep 22 18:46 storage/
drwxr-xr-x  2 root     root   4096 Sep 22 18:46 tests/
```





## Build vendor/ directory
If you compare the contents of your local application files to your production application files on your Droplet, you'll notice the Droplet version is missing a `vendor/` directory.

<img src='http://making-the-internet.s3.amazonaws.com/laravel-foobooks-on-droplet-no-vendor-directory@2x.png' style='max-width:1026px; width:100%'>

This is because vendors are managed by Composer and *not* version controlled. This is configured via `.gitignore` which lists `vendor/` as a directory to ignore:

<img src='http://making-the-internet.s3.amazonaws.com/laravel-vendor-in-gitignore@2x.png' style='max-width:516px; width:100%'>

Given this, you need to have Composer build your vendor's directory with this command:

```xml
$ composer install
```

When that command is complete (it may take a few minutes), if you view the contents of your live app, you should now see a `vendor/` directory.


## Permissions
As discussed when we were setting up Laravel on your local server, Laravel/the Apache web server needs write access to the `storage` and `bootstrap/cache` directories.

This was a step we could skip locally because of how your local servers are configured, but it can't be skipped on your live servers. On your DigitalOcean server we need to make sure Apache has write access to these folders.

To do this, we first identified that on DigitalOcean servers, Apache runs under a user called `www-data`.

Given that, make the user `www-data` own the `storage` directory and everything in it (`-R`):

```xml
$ sudo chown -R www-data storage
```

And now do the same two steps for the `bootstrap/cache` directory:
```xml
$ sudo chown -R www-data bootstrap/cache
```

<sup>
Ref: [SuperUser: Setting correct permissions for uploading files](http://superuser.com/a/581259/84723)
</sup>




## Set up .env file on production
Like the `vendors/` folder, the `.env` file is also listed in `.gitignore` so it's also excluded from version control. Because of this, you need to manually create a `.env` file on your live server in order for your application to work there.

This can be done by copying the provided `.env.example.env` file to `.env`

```xml
$ cp .env.example .env
```

Next, you need to generate a app key:

```xml
$ php artisan key:generate
```

(If you're curious, you can `cat .env` to see the new key that was generated)

Later we'll discuss environments in full details and explain what exactly .env is doing and how it works.



## Configure subdomain
To access your Laravel application from the web, you'll want to set up a subdomain that points to it. For this you will follow the same procedure you did to create `http://helloworld.yourdomain.com` and `http://p1.yourdomain.com`.

For clarity's sake, let's outline the procedure again...

Find your DNS settings from your domain provider and create a new domain that points to your Droplet's IP Address. In our example, we're setting up `http://foobooks.dwa15-practice.biz`:

<img src='http://making-the-internet.s3.amazonaws.com/laravel-foobooks-subdomain@2x.png' class='' style='max-width:1260px; width:100%' alt=''>

Next, you need to set up a VirtualHost record for this new domain. If you'll recall, this is done in the `/etc/apache2/sites-enabled/` directory on your Droplet in a file called `000-default.conf`.

At the end of this file, *after* any existing VirtualHost blocks you may already have, add a new one:

```html
<VirtualHost *:80>
	ServerName foobooks.dwa15-practice.biz
	DocumentRoot "/var/www/html/foobooks/public"
	<Directory "/var/www/html/foobooks/public">
		AllowOverride All
	 </Directory>
</VirtualHost>
```

**IMPORTANT:** For Laravel apps, the root needs to point the the `public` folder within your application. Note how that's done above for `ServerName` and `DocumentRoot`.

Here's an example of what our `000-default.conf` file looked like after adding this:

<img src='http://making-the-internet.s3.amazonaws.com/laravel-foobooks-virtualhost@2x.png' class='' style='max-width:853px; width:100%' alt=''>

After you save your changes to `000-default.conf`, restart Apache to make the change take effect:

```xml
$ sudo service apache2 restart
```



## Test it out
Your Laravel app should now be running on your subdomain.

<img src='http://making-the-internet.s3.amazonaws.com/laravel-foobooks-on-droplet@2x.png' style='max-width:592px; width:100%'>




## Moving forward
The above steps were a one-time-process for deploying a new Laravel application.

Moving forward, your deployment process will look like this:

1. From local `add`, `commit`, and `push` changes.
2. SSH into your DigitalOcean droplet and navigate into your app folder, then run `git pull origin master`.
2. Also while SSH'd in to your app folder, run `composer install` to install any dependencies.




## Troubleshooting


__Issue: Composer memory issue__

You attempt to run a Composer command but get an error about `lack of memory or swap, or not having swap configured` and/or `Cannot allocate memory`.

To fix, set up a swap file with the following command:

```xml
$ sudo fallocate -l 4G /swapfile
```

>> "Swap is an area on a hard drive that has been designated as a place where the operating system can temporarily store data that it can no longer hold in RAM." -[ref](https://www.digitalocean.com/community/tutorials/how-to-add-swap-on-ubuntu-14-04)


__Log files__
+ Apache: `/var/log/apache2/error.log`
+ Laravel: `/path/to/your/app/storage/logs/laravel.log`


__Common issues__

1. Lack of a `.env` file.
2. Forgetting to build the `vendors` directory.
3. Forgetting to set the necessary write permissions on `storage` and `bootstrap/cache`
