PSX DateTime
===

## About

Stricter date time implementations which only accepts RFC3339 date time strings.
Handles date time, date, time and duration formats.

## Usage

```php
<?php

// date time
$dateTime = new DateTime('2016-03-28T23:27:00Z');
$dateTime = new DateTime(2016, 3, 28, 23, 27, 0);

echo $dateTime->toString(); // 2016-03-28T23:27:00Z

// date
$date = new Date('2016-03-28');
$date = new Date(2016, 3, 28);

echo $date->toString(); // 2016-03-28

// time
$time = new Time('23:27:00');
$time = new Time(23, 27, 0);

echo $time->toString(); // 23:27:00

// duration
$duration = new Duration('P1D');
$duration = new Duration(0, 0, 1);

echo $duration->toString(); // P0Y0M1D

```
