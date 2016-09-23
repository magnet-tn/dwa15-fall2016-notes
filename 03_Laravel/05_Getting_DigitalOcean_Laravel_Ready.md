With your Laravel application up and running locally, we now need to deploy it on your production server.

Before we can deploy, though, we need to do a few updates on your DigitalOcean server so it's set up with the necessary components that Laravel needs to run.

In these notes, we'll do the following:

1. Update PHP
2. Install Composer
3. Enable `mod_rewrite`



## Upgrade PHP
The image we built our DigitalOcean Droplets with came installed with PHP 5.5.9. The latest version of Laravel requires **PHP >= 5.6.4**, so we need to upgrade PHP on the Droplet.

SSH into your Droplet, and start by checking the version you're currently running:

```xml
$ php -v
PHP 5.5.9-1ubuntu4.19 (cli) (built: Jul 28 2016 19:31:33)
Copyright (c) 1997-2014 The PHP Group
Zend Engine v2.5.0, Copyright (c) 1998-2014 Zend Technologies
    with Zend OPcache v7.0.3, Copyright (c) 1999-2014, by Zend Technologies
```

You should see 5.5.9.

To upgrade we'll use `apt-get`, a command line utility for managing packages on Ubuntu's systems (which our Droplets run on). We'll use apt-get to retrieve and install PHP 5.6 from [this repository](https://launchpad.net/~ondrej/+archive/ubuntu/php/+index).

To do this, run the following commands, one at a time. Follow the instructions to hit `Enter` or `Y` (yes) when prompted.

```xml
$ sudo add-apt-repository ppa:ondrej/php
$ sudo apt-get update
$ sudo apt-get install php5.6 php5.6-mbstring php5.6-mcrypt php5.6-mysql php5.6-xml zip unzip
$ sudo a2dismod php5
$ sudo a2enmod php5.6
$ sudo service apache2 restart
```

When you're done, you can confirm PHP command line is running the correct version:

```xml
$ php -v
PHP 5.6.26-1+deb.sury.org~trusty+1 (cli)
Copyright (c) 1997-2016 The PHP Group
Zend Engine v2.6.0, Copyright (c) 1998-2016 Zend Technologies
    with Zend OPcache v7.0.6-dev, Copyright (c) 1999-2016, by Zend Technologies
```

And you can confirm Apache is running the correct version by visiting `http://your-digitalocean-ip/info.php` (assuming you have not deleted that `info.php` file.)

<img src='http://making-the-internet.s3.amazonaws.com/laravel-digitalocean-post-php-upgrade-apache@2x.png' style='max-width:654px;' alt=''>

[ref](https://help.ubuntu.com/community/Repositories/CommandLine)


## Install Composer on your Droplet

Before you install Composer, first run the following command to set up a swap file on your Droplet:

```xml
$ sudo fallocate -l 4G /swapfile
```

>> "Swap is an area on a hard drive that has been designated as a place where the operating system can temporarily store data that it can no longer hold in RAM." -[ref](https://www.digitalocean.com/community/tutorials/how-to-add-swap-on-ubuntu-14-04)

This preventative step will help when Composer runs memory intensive tasks.


Now move into your bin directory where you'll install Composer:

```bash
$ cd /usr/local/bin
```

Use cURL to download Composer:

```bash
$ curl -sS https://getcomposer.org/installer | sudo php
```

Rename the composer executable to `composer` so it's simple to call:

```bash
$ sudo mv composer.phar composer
```

Test it's working:

```bash
$ composer
```

See a list of Composer commands? Good, you're ready to move to the next step...




## Enable mod_rewrite
Laravel requires Apache's `mod_rewrite` for Routing purposes.

To enable this module, run the following command on your DigitalOcean droplet:

```xml
$ sudo a2enmod rewrite
```

Restart Apache to make these this change take effect:
```xml
$ sudo service apache2 restart
```


## Server setup complete!
At this point, you DigitalOcean Droplet has everything it needs to run a Laravel app. You're ready to move on to the next steps of deploying.
