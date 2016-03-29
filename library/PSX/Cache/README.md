PSX Cache
===

## About

[PSR-6](http://www.php-fig.org/psr/psr-6/) implementation using the doctrine 
cache system. Through theses classes it is possible to access the doctrine cache
system through an PSR-6 interface.

## Usage

```php
<?php

$pool = new PSX\Cache\Pool(new Doctrine\Common\Cache\FilesystemCache());
$item = $pool->getItem('foo');

if (!$item->isHit()) {
    $value = doComplexTask();

    $item->set($value);
    $item->expiresAfter(3600);

    $pool->save($item);
} else {
    $value = $item->get();
}

```
