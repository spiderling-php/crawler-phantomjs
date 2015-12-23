<?php

namespace SP\PhantomDriver\Test;

use PHPUnit_Framework_TestCase;
use SP\PhantomDriver\Session;

/**
 * @coversDefaultClass SP\PhantomDriver\Session
 */
class SessionTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $browser = $this
            ->getMockBuilder('SP\PhantomDriver\Browser')
            ->disableOriginalConstructor()
            ->getMock();

        $session = new Session($browser);

        $this->assertInstanceOf('SP\Spiderling\BrowserInterface', $session->getBrowser());

        $this->assertSame($browser, $session->getBrowser());
    }

    public function testConstructDefault()
    {
        $session = new Session();

        $this->assertInstanceOf('SP\PhantomDriver\Browser', $session->getBrowser());
    }
}
