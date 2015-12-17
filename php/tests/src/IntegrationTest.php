<?php

namespace SP\Driver\Test;

use SP\DriverTest\BrowserDriverTest;
use SP\Driver\PhantomBrowser;

class IntegrationTest extends BrowserDriverTest
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $driver = new PhantomBrowser();
        $driver->start()->wait();

        self::setDriver($driver);
    }
}
