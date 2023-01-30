# Php-Time-Zone

[![Latest Version](https://img.shields.io/github/release/joy2362/php-time-zone.svg?style=flat-square)](https://github.com/jessedp/php-timezones/releases)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/joy2362/php-time-zone.svg?style=flat-square)](https://packagist.org/packages/jessedp/php-timezones)

A wrapper to enumerate PHP 7.* timezones list.
## Basics

* Creates timezone arrays based on PHP's supported timezones with optional grouping by region
* Lists are sorted by offset from high (+14:00) to low (-11:00)
* Return as php arrays for whatever use your heart desires

## Installation

You can install this package using [Composer](https://getcomposer.org).

``` bash
$ composer require joy2362/php-time-zone
```
Publish config file:
``` bash
$ php artisan vendor:publish --provider="Joy2362\PhpTimezone\PhpTimeZoneServiceProvider" --tag="config"
```
## Usage

### 1.Get all timezone list with label and value pair

``` php
TimeZone::list();
```
### 2.Get all timezone list only value

``` php
TimeZone::listWithoutLabel();
```

### 3.Get all timezone list only label

``` php
TimeZone::listWithoutValue();
```

### 4.Get timezone list by a region

``` php
TimeZone::listByRegion('Asia');
```

### 5.Get timezone label from value

``` php
TimeZone::getLabelFromValue('Asia/Dhaka');
```
### 6.Get timezone value from label

``` php
TimeZone::getValueFromLabel('Asia/Dhaka');
```

### 7.Get region list

``` php
TimeZone::getRegions();
```

### 8.Get supported zone list

``` php
TimeZone::getSupportedTimeZone();
```

## Thanks to

This is based off some lovely work by:

* https://github.com/jessedp/php-timezones
