# Acme

## Laravel Notes

### Environment Detection

Machine name MUST start with acme-production for app to run in production mode. 
See ./bootstrap/start.php $app->detectEnvironment().

### Migrations

Migration files are in ./app/database/migrations.  To initialize dev database use
php artisan migrate:generate (answer yes) followed by php artisan migrate to run
outstanding migrations.  See http://laravel.com/docs/4.2/migrations#running-migrations

### Dev Dependencies

Use composer update --dev.  Note: the final arbiter of laravel dev dependency loading
is the laravel environment/config.  See ./app/config/local/app.php

### Way Generators

Templates are in ./app/templates

### PHPUnit/Testing

run *php vendor/bin/phpunit* from inside vagrant instance (vagrant ssh).

### Service Providers

Acme\Storage\ServiceProvider: Binds repository interfaces

- Most likely a list of bindings to Eloquent implementations of our storage repository classes.
- These Eloquent implementations should have Eloquent models injected via DI for testing purposes so resist using \SomeModel inside of the class.  Use its injected instance instead.


Acme\Service\ServiceProvider: Binds service interfaces

- Bindings of Service interfaces that serve REST endpoints that interact with multiple storage repositories/models.  i.e. a CommentService that interacts with multiple polymorphic models/storage respositories.
- Please resist making "Eloquent" implementations of services.  Service classes should have Repository interfaces directly injected.

### Vagrant

Make sure you have the latest versions of vagrant, virtualbox installed, and then run this: 

vagrant plugin install vagrant-vbguest

## Front End

### Bower

Install js dependencies to bower vendor dir.  *bower install* or *bower update*.

### NPM

Install *npm install* or *npm update*

### Grunt

See ./Gruntfile.js for build maps.  Should build from bower vendor packages.
Build from non-uglified/minified files when possible.  Some packages don't
provide non-minified sources.  Use of *grunt watch* will cover most.
Otherwise start with *grunt uglify* to build angular packages, etc.
