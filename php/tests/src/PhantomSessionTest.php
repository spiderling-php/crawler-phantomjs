<?php

namespace SP\Driver\Test;

use PHPUnit_Framework_TestCase;
use SP\Driver\PhantomSession;

/**
 * @coversDefaultClass SP\Driver\PhantomSession
 */
class PhantomSessionTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $browser = $this
            ->getMockBuilder('SP\Driver\PhantomBrowser')
            ->disableOriginalConstructor()
            ->getMock();

        $session = new PhantomSession($browser);

        $this->assertInstanceOf('SP\Spiderling\BrowserInterface', $session->getBrowser());

        $this->assertSame($browser, $session->getBrowser());
    }

    public function testConstructDefault()
    {
        $session = new PhantomSession();

        $this->assertInstanceOf('SP\Driver\PhantomBrowser', $session->getBrowser());
    }
}
