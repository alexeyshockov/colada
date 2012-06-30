# Colada. Collections Framework for PHP [![Build Status](https://secure.travis-ci.org/alexeyshockov/colada.png)](http://travis-ci.org/alexeyshockov/colada)

## Goal

_Convenient_ and _safe_ way to work with collections.

## Benefits

* Rich maps (any type for keys).
* Functional idioms:
    * immutable collections (safest and usable for most cases),
    * lazy operations — don't skimp on it.
* Optional values (to solve NPE problem).

## Installation

Colada currently may be installed as submodule for your Git project:

``` bash
git submodule add git://github.com/alexeyshockov/colada.git vendor/colada
```

or throught [Composer](https://github.com/composer/composer):

``` json
{
    "require": {
        "alexeyshockov/colada": "dev-master"
    }
}
```

## Usage

Follow project's [wiki](https://github.com/alexeyshockov/colada/wiki) for usage information and examples.

Detailed [API documentation](http://alexeyshockov.github.com/colada/api/) (for current stable release).

## Playing with interactive shell

Colada includes small wrapper for standard PHP interactive shell, which adds some useful functions to it.

Lets play with it!

``` bash
$ ./shell.sh
// Call use_colada() function to benefit from all shortcuts ;)
Interactive shell

php > use_colada();
php > d(collection(-2, -1, 0, 1, 2));
$var0 = array(0 => -2, 1 => -1, 2 => 0, 3 => 1, 4 => 2)
php > // Call above are identical to: $var0->mapBy(function($x) { return $x + 1; });
php > d($var0->mapBy(x()->increment()));
$var1 = array(0 => -1, 1 => 0, 2 => 1, 3 => 2, 4 => 3)
php > d($var0->acceptBy(x()->isPositive()));
$var2 = array(0 => 0, 1 => 1, 2 => 2)
php > d($var0->rejectBy(x()->isPositive()));
$var3 = array(0 => -2, 1 => -1)

php > d($var0->groupsBy(function($x) { return ($x >= 0 ? 'positive' : 'negative'); }));
$var4 = array(
  0 =>
  array(
    0 => 'negative',
    1 =>
    array(
      0 => -2,
      1 => -1,
    ),
  ),
  1 =>
  array(
    0 => 'positive',
    1 =>
    array(
      0 => 0,
      1 => 1,
      2 => 2,
    ),
  ),
)
```

## Roadmap

* 0.x — API stabilization.
* 1.0 — Stable API.
* 1.x — Other minor features (sorted collections, etc.).
* 2.0 — Mutable collections.

More detailed view may be found in [appropriate page](https://github.com/alexeyshockov/colada/issues/milestones).
