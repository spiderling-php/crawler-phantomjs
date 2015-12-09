Crawler Phantomjs
=================

[![Build Status](https://travis-ci.org/spiderling-php/crawler-phantomjs.svg?branch=master)](https://travis-ci.org/spiderling-php/crawler-phantomjs)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/spiderling-php/crawler-phantomjs/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/spiderling-php/crawler-phantomjs/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/spiderling-php/crawler-phantomjs/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/spiderling-php/crawler-phantomjs/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/spiderling-php/crawler-phantomjs/v/stable)](https://packagist.org/packages/spiderling-php/crawler-phantomjs)
PhantomJS crawler for spiderling

Installation
------------

Install via composer

```
composer require spiderling-php/crawler-phantomjs
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
