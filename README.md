Crawler Phantomjs
=================

[![Build Status](https://travis-ci.org/spiderling-php/phantom-driver.svg?branch=master)](https://travis-ci.org/spiderling-php/phantom-driver)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/spiderling-php/phantom-driver/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/spiderling-php/phantom-driver/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/spiderling-php/phantom-driver/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/spiderling-php/phantom-driver/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/spiderling-php/phantom-driver/v/stable)](https://packagist.org/packages/spiderling-php/phantom-driver)
PhantomJS crawler for spiderling

Installation
------------

Install via composer

```
composer require spiderling-php/phantom-driver
```

Usage
-----
```php
use SP\Driver\PhantomSession;

$session = new PhantomSession();

$session->open('http://google.com');
```

License
-------

Copyright (c) 2015, Spiderling Developed by Ivan Kerin

Under BSD-3-Clause license, read LICENSE file.
