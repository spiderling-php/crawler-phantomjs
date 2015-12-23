<?php

namespace SP\PhantomDriver\Test;

use SP\DriverTest\BrowserDriverTest;
use SP\PhantomDriver\Browser;

class IntegrationTest extends BrowserDriverTest
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $driver = new Browser();
        $driver->start()->wait();

        self::setDriver($driver);
    }
}
