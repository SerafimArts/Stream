<p align="center">
    <img src="./resources/logo.png" width="128" alt="Stream" />
</p>

<p align="center">
    <a href="https://travis-ci.org/SerafimArts/Stream"><img src="https://travis-ci.org/SerafimArts/Stream.svg?branch=master" alt="Travis CI" /></a>
    <a href="https://scrutinizer-ci.com/g/SerafimArts/Stream/?branch=master"><img src="https://scrutinizer-ci.com/g/SerafimArts/Stream/badges/quality-score.png?b=master" alt="Scrutinizer CI" /></a>
    <a href="https://packagist.org/packages/serafim/stream"><img src="https://poser.pugx.org/serafim/stream/version" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/serafim/stream"><img src="https://poser.pugx.org/serafim/stream/v/unstable" alt="Latest Unstable Version"></a>
    <a href="https://raw.githubusercontent.com/serafim/stream/master/LICENSE.md"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square" alt="License MIT"></a>
    <a href="https://packagist.org/packages/serafim/stream"><img src="https://poser.pugx.org/serafim/stream/downloads" alt="Total Downloads"></a>
</p>

# PHP File Streaming Package

## Installation

`composer require serafim/stream`

[Package on packagist.org](https://packagist.org/packages/serafim/stream)

## Introduction

Stream package provides the ability to override the data contained within the 
files in real time.

## Protocol Streaming

```php
<?php

use Serafim\Stream\Stream;

Stream::create('some')
    ->tryRead(function (string $pathname): string {
        return $pathname;
    });

echo \file_get_contents('some://example'); // string(7) "example"
```

```php
<?php

use Serafim\Stream\Stream;

Stream::create('four')
    ->onRead(function (string $sources): string {
        return $sources . "\n" . 'return 4;';
    });

echo require 'four://example.php'; // int(1) "4"
```

## Composer

```php
<?php
use Serafim\Stream\ClassLoader;

$composer = require __DIR__ . '/vendor/autoload.php';

$loader = new ClassLoader($composer);
$loader->when
    // The stream will be triggered only on those files
    // whose namespace starts with "App"
    ->namespace('App')
    ->then(function (string $sources): string {
        \var_dump(42);
        
        return $sources;
    });

// When loading this class, var_dump(42) will be displayed.
new App\Example();
```

## Composer Filters

Each filter starts with calling the `$loader->when` method.

### Filter `where`

It works when the result of an anonymous function passed to the method `where` 
returns the `true`.

```php
$loader->when->where(function (string $class, string $pathname): bool {
    return $class === 'User';
});

$user = new User();
```

### Filter `not`

It works when the result of an anonymous function passed to the method `not` 
returns the `false`.

```php
$loader->when->not(function (string $class, string $pathname): bool {
    return $class !== 'User';
});

$user = new User();
```

### Filter `every`

Works when each rule applied inside an anonymous function returns
a positive result.

```php
use Serafim\Stream\Filter\Conjunction;

$loader->when->every(function (Conjunction $fn) {
    $fn->where(...);
    // AND
    $fn->where(...);
});
```

### Filter `any`

Works when any (one of) rule applied inside an anonymous function returns
a positive result.

```php
use Serafim\Stream\Filter\Disjunction;

$loader->when->any(function (Disjunction $fn) {
    $fn->where(...); 
    // OR
    $fn->where(...);
});
```

### Filter `fqn`

Works in the case when the fqn (Fully qualified name) corresponds 
to the specified.

```php
$loader->when->fqn('App\\User');

new App\User(); // Stream works
new Some\App\User(); // Stream does not work
```

### Filter `className`

Works in the case when the class name corresponds 
to the specified.

```php
$loader->when->className('User');

new App\User(); // OK
new Any\User(); // OK
```

### Filter `namespace`

Works in the case when the namespace corresponds 
to the specified.

```php
$loader->when->className('App');

new App\User(); // OK
new App\Message(); // OK
```

### Filter `fileName`

Works in the case when the file name corresponds 
to the specified.

```php
$loader->when->fileName('App');

new App(); // The stream is triggered if the file name matches the class name.
```

### Filter `pathNameMatches`

The stream is triggered if the path matches the regular expression.

```php
$loader->when->pathNameMatches('Models/.*');
```

### Filter `fileNameMatches`

The stream is triggered if the file name matches the regular expression.

```php
$loader->when->fileNameMatches('\w+Interface');
```

### Filter `classNameMatches`

The stream is triggered if the class name matches the regular expression.

```php
$loader->when->classNameMatches('\w+Interface');
```

### Filter `fqnMatches`

The stream is triggered if the fqn (Fully qualified name) matches the regular expression.

```php
$loader->when->fqnMatches('App\\.*?\\\w+Interface');
```

### Filter `withVendors`

The stream is triggered if the file is loaded from the vendor 
directory (by default, all vendor files are ignored)

```php
$loader->when->withVendors();
```


