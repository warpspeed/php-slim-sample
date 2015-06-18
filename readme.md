# WarpSpeed Slim Sample Application
[Slim](http://www.slimframework.com/) is a PHP micro-framework optimized for building RESTful APIs. [WarpSpeed](https://warpspeed.io/) makes it incredibly simple to work with and deploy Slim and other PHP based projects. This guide will help you get up and running with Slim and WarpSpeed.

## Vagrant Development Envrionment

This guide assumes that you are using the WarpSpeed Vagrant development environment. Doing so will help you follow best practices and keep your development and production environments as similar as possible. If you are not using WarpSpeed Vagrant, ignore the sections that involve using the VM.

## Fork and Clone the Sample Project
The best way to begin using this project is to fork the repository to your own GitHub account. This will allow you to make updates and begin using the project as a template for your own work. To fork the repository, simply click the "Fork" button for this repository.

Once you have forked the repository, you can clone it to your development environment or use pull-deploy to deploy it directly to a server configured with WarpSpeed.io.

Ideally, you should be using the WarpSpeed Vagrant development environment. The instructions below will assume this, although it isn't necessary if you already have a python environment set up.

To clone the repository to your local machine (not in your VM), use the following command:

```
# RUN THIS COMMAND FROM YOUR LOCAL ENVIRONMENT

cd ~/Sites
git clone git@github.com:YOUR_USERNAME/php-slim-sample.git warpspeed-slim.dev
```

## Create a Database

The sample project uses a MySQL database. This can easily be swapped with an SQLite or PostgreSQL database. To create a MySQL database and user with WarpSpeed, do the following:

```
# RUN THESE COMMANDS FROM YOUR LOCAL MACHINE

# cd to your warpspeed-vagrant directory
# and ssh into your VM
cd ~/warpspeed-vagrant
vagrant ssh

# then, run the db creation command
warpspeed mysql:db tasks_db tasks_user password123

# the terminal will prompt you for the
# MySQL admin password, enter 'vagrant'

Enter password: vagrant
```

This will create a database named "tasks_db" along with a user, "tasks_user", that has access via the password "password123". Feel free to change the values to suit your needs (hint: perhaps choosing a better password would be wise).

## Create a WarpSpeed Site

We need to create the appropriate server configuration files to run the site. To configure Nginx and Passenger to run your site, perform the following:

```
# if you aren't already in your VM then...
# cd to your warpspeed-vagrant directory
# and ssh into your VM
cd ~/warpspeed-vagrant
vagrant ssh

# then, run the site creation command
# notice that --force is used because the site directory already exists
warpspeed site:create php warpspeed-slim.dev --force
```
## Configure your .env.php file

This Slim sample project uses the PHP Data Object (PDO) wrapped in an generic object model to read from and write to your MySQL database. The `BaseModel.php` class located in the `models/` directory is designed similarly to Laravel's [Eloquent ORM](http://laravel.com/docs/5.0/eloquent). Read the source code comments for further details on implementation and funtionality. Pursuant to convention and best practices, the environment variables required as arguments to instantiate a new PDO object are declared externally from the web root `public/` directory. To complete the requisite fields of the .env.php file, run the following commands:

```
# RUN THESE COMMANDS FROM YOUR LOCAL MACHINE

# cd to your project's root directory
# and copy the contents of the .env.template.php
# file into a new file titled .env.php
cd ~/Sites/warpspeed-slim.dev
cp .env.template.php .env.php

# enter the requisite fields
nano .env.php

# the .env.php file should resemble the following when complete:
<?php

   return array(
   'DB_NAME' => 'tasks_db',
   'DB_USER' => 'tasks_user',
   'DB_PASS' => 'password123',
   'DB_HOST' => 'localhost',
   'MODE'    => 'DEVELOPMENT');

?>

# exit and save
```

## Install Slim and Run Migrations

Slim ulilizes Composer to manage its dependencies. To install the required libraries listed in the `composer.json` file, run the following commands:

```
# RUN THESE COMMANDS FROM YOUR VM

# cd to your project's root directory
# and install Silex via Composer
cd ~/sites/warpspeed-slim.dev
composer install

# migrate the tasks table
# enter your tasks_user password when prompted
# if following the guide exactly, 'password123'
mysql -u tasks_user -p tasks_db < sql/create_tasks_table.sql
Enter password: password123
```

The SQL statement used to generate the `tasks` table is located in `~/Sites/warpspeed-silex.dev/sql/`.

## Add a Hosts File Entry

To access your new Slim site, you will need to add an entry to your hosts file on your local machine (not your VM). To do so, perform the following:

```
# RUN THESE COMMANDS FROM YOUR LOCAL MACHINE

# open a terminal and run the following command (for Mac)
sudo nano /etc/hosts

# add this line to the end of the file
192.168.88.10 warpspeed-slim.dev

# exit and save
```

Now, whenever you access "warpspeed-slim.dev" in the browser, you will be directed to your Silex site within your VM.

## Restart your Site and Celebrate
Finally, we need to reload the site configuration to finalize and effectuate our changes. To do so, perform the following:

```
# RUN THESE COMMANDS FROM YOUR VM

# reload the site configuration
warpspeed site:reload warpspeed-slim.dev
```

Now you can access http://warpspeed-slim.dev on your local machine to view the site.

## Troubleshooting

If you have issues, chiefly a 500 Status Code Internal Error, and need to troubleshoot, view the NGINX error log for helpful clues.

```
# RUN THESE COMMANDS FROM YOUR VM

# open the NGINX error log
sudo nano /var/log/nginx/error.log

# ...or keep an open tab of the NGINX error log
sudo tail -f /var/log/nginx/error.log
```

If the error appears unique to PHP, view the `warpspeed-silex.dev-error.log`. To do so, run the following commands: 

```
# RUN THESE COMMANDS FROM YOUR VM

# open the site's error log
sudo nano /var/log/php/warpspeed-silex.dev-error.log

# ...or keep an open tab of the site's error log
sudo tail -f /var/log/php/warpspeed-silex.dev-error.log
```

# License
This sample project is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).


