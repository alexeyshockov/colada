# Colada

[![Latest Stable Version](https://poser.pugx.org/alexeyshockov/colada/v/stable)](https://packagist.org/packages/alexeyshockov/colada)
[![Build Status](https://travis-ci.org/alexeyshockov/colada.svg?branch=master)](https://travis-ci.org/alexeyshockov/colada)
[![Test Coverage](https://api.codeclimate.com/v1/badges/f82172ad33818d09b5a7/test_coverage)](https://codeclimate.com/github/alexeyshockov/colada/test_coverage)

## Goal

_Convenient_ and _safe_ way to work with collections.

...And it's been (more or less) solved by some other libraries, like [nikic/iter](https://github.com/nikic/iter), 
[doctrine/collections](https://github.com/doctrine/collections) or even 
[php-ds/php-ds](https://github.com/php-ds/polyfill). That's why the current version of the library contains just a few 
helpers for that libraries, to conveniently integrate them together. 

## Installation

```
composer require alexeyshockov/colada:~3.0
```

## Usage

The library's functions are split by 

### \Colada\ds\{group_by}

Helpers from this namespace require [php-ds/php-ds](https://github.com/php-ds/polyfill) to be available.

`group_by()` function prodices a two dimension array (`\ArrayObject` or `\Ds\Map`, depends on the group key type), from 
an iterable based on a group function.

```php
TODO
```

### \Colada\GuzzleHttp\{coroutine_invoke, coroutine, time_sleep}
### \Colada\React\{coroutine_invoke, coroutine}

Helpers from this namespace require [guzzlehttp/promises](https://github.com/guzzle/promises) or 
[react/promise](https://github.com/reactphp/promise) to be available.

This is the same concept applied to two most popular libs with async capabilities. If you are familiar with async/await 
in C# or node.js or coroutines in Go, this should be simple. Take a look at the script, written in callback-style and in 
coroutine-style:

```php
TODO
```

```php
TODO
```

### \Colada\iter\opt\{get, head, last, find_one}

Helpers from this namespace require [phpoption/phpoption](https://github.com/schmittjoh/php-option) to be available.

### \Colada\iter\{to_kv_pairs, each_n_and_last, uasort, uksort}

Simple helpers for general `iterable` types.

`uasort()` and `uksort()` are basically equivalents for the internal ones, but work for arbitrary `iterable` type.

## Contributing

### Running Tests

To run all the tests, install the vendors (with Composer) and execute:
```
vendor/bin/phpunit --testdox
```

### Public API

All classes or functions that are intended to be used by a user should be marked with `@api` PHPDoc tag. Anything without this mark 
are internal and should not be used by the end user (no guarantees that the interface will stay the same between 
versions).

## Alternatives

* https://github.com/consolidation/annotated-command — similar approach, but from a different angle
