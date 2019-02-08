# CommandBus Symfony Bundle

This open-source package is ready to development for CQRS and DDD. 

## Status build and coverage

### develop

[![Build Status](https://travis-ci.org/ferdyrurka/command-bus-symfony-bundle.svg?branch=develop)](https://travis-ci.org/ferdyrurka/command-bus-symfony-bundle)
[![Coverage Status](https://coveralls.io/repos/github/ferdyrurka/command-bus-symfony-bundle/badge.svg?branch=develop)](https://coveralls.io/github/ferdyrurka/command-bus-symfony-bundle?branch=develop)

### Current version (master)

[![Build Status](https://travis-ci.org/ferdyrurka/command-bus-symfony-bundle.svg?branch=master)](https://travis-ci.org/ferdyrurka/command-bus-symfony-bundle)
[![Coverage Status](https://coveralls.io/repos/github/ferdyrurka/command-bus-symfony-bundle/badge.svg?branch=master)](https://coveralls.io/github/ferdyrurka/command-bus-symfony-bundle?branch=master)

## Documentation

Documentation for CommandBus is in [click here](Resources/docs/index.md) 

## Stack

* PHP7.3
* PHPUnit
* Mockery
* ElasticSearch

## Run tests

```sh
php vendor/bin/phpunit
```

## Soon

* New database (Staff Doctrine2)
* QueryBus
* EventBus
* Safe your data. Not worked MySQL, added command to queue and waited to save.

## FAQ

* Why not use Monolog?

    * Because Monolog is not stable version for PHP 7.3 

## License 

Open GPL v3 or later

## Authors

* [Łukasz Staniszewski](http://lukaszstaniszewski.pl) < kontakt@lukaszstaniszewski.pl >
