<?php

namespace SP\PhantomDriver\Test;

use SP\DriverTest\BrowserDriverTest;
use SP\PhantomDriver\Server;
use SP\PhantomDriver\Browser;

class IntegrationTest extends BrowserDriverTest
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $server = new Server();
        $server->start()->wait();

        self::setDriver(new Browser($server->getClient()));
    }
}
