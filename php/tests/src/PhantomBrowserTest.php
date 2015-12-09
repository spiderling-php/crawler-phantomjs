<?php

namespace SP\Driver\Test;

use PHPUnit_Framework_TestCase;
use SP\Driver\PhantomBrowser;
use GuzzleHttp\Psr7\Uri;
use SP\Spiderling\Query;

/**
 * @coversDefaultClass SP\Driver\PhantomBrowser
 */
class PhantomBrowserTest extends PHPUnit_Framework_TestCase
{
    private $driver;
    private $server;
    private $client;

    public function setUp()
    {
        $this->server = $this
            ->getMockBuilder('SP\Driver\Server')
            ->disableOriginalConstructor()
            ->getMock();

        $this->client = $this
            ->getMockBuilder('SP\Driver\Client')
            ->disableOriginalConstructor()
            ->getMock();

        $this->driver = new PhantomBrowser($this->server, $this->client);
    }

    /**
     * @covers ::__construct
     * @covers ::getClient
     * @covers ::getServer
     */
    public function testConstruct()
    {
        $driver = new PhantomBrowser($this->server, $this->client);

        $this->assertSame($this->server, $driver->getServer());
        $this->assertSame($this->client, $driver->getClient());
    }

    /**
     * @covers ::removeAllCookies
     */
    public function testRemoveAllCookies()
    {
        $this->client
            ->expects($this->once())
            ->method('deleteJson')
            ->with('cookies');

        $this->driver->removeAllCookies();
    }

    /**
     * @covers ::start
     */
    public function testStart()
    {
        $this->server
            ->expects($this->once())
            ->method('start')
            ->willReturn('promise');

        $response = $this->driver->start();

        $this->assertEquals('promise', $response);
    }

    /**
     * @covers ::getUri
     */
    public function testGetUri()
    {
        $expected = new Uri('http://example.com');

        $this->client
            ->expects($this->once())
            ->method('getJson')
            ->with('url')
            ->willReturn((string) $expected);

        $result = $this->driver->getUri();

        $this->assertEquals($expected, $result);
    }

    public function dataActions()
    {
        return [
            ['click', [12], 'element/12/click'],
            ['select', [11], 'element/11/select'],
            ['moveMouseTo', [13], 'element/13/hover'],
        ];
    }

    /**
     * @dataProvider dataActions
     * @covers ::click
     * @covers ::select
     * @covers ::moveMouseTo
     */
    public function testActions($method, $params, $uri)
    {
        $this->client
            ->expects($this->once())
            ->method('postJson')
            ->with($uri);

        call_user_func_array([$this->driver, $method], $params);
    }

    public function dataReturnSetters()
    {
        return [
            [
                'executeJs',
                ['console.log("a")'],
                'execute',
                'console.log("a")'
            ],
            [
                'getElementIds',
                [new Query\Css('#test')],
                'elements',
                './/*[@id = \'test\']'
            ],
            [
                'getChildElementIds',
                [new Query\Css('#me'), 12],
                'element/12/elements',
                './/*[@id = \'me\']'
            ],
        ];
    }

    /**
     * @dataProvider dataReturnSetters
     * @covers ::executeJs
     * @covers ::getElementIds
     * @covers ::getChildElementIds
     */
    public function testReturnSetters($method, $params, $uri, $value)
    {
        $expected = 'return value';

        $this->client
            ->expects($this->once())
            ->method('postJson')
            ->with($uri, $value)
            ->willReturn($expected);

        $result = call_user_func_array([$this->driver, $method], $params);

        $this->assertEquals($expected, $result);
    }

    public function dataSetters()
    {
        return [
            ['open', [new Uri('http://example.com')], 'url', 'http://example.com'],
            ['saveScreenshot', ['file.jpg'], 'screenshot', 'file.jpg'],
            ['setValue', [2, 'val'], 'element/2/value', 'val'],
        ];
    }

    /**
     * @dataProvider dataSetters
     * @covers ::open
     * @covers ::saveScreenshot
     * @covers ::setValue
     * @covers ::executeJs
     */
    public function testSetters($method, $params, $uri, $value)
    {
        $this->client
            ->expects($this->once())
            ->method('postJson')
            ->with($uri, $value);

        call_user_func_array([$this->driver, $method], $params);
    }

    public function dataGetters()
    {
        return [
            ['getJsMessages', [], 'messages'],
            ['getJsErrors', [], 'errors'],
            ['getFullHtml', [], 'source'],
            ['getText', [1], 'element/1/text'],
            ['getTagName', [2], 'element/2/name'],
            ['getAttribute', [3, 'href'], 'element/3/attribute/href'],
            ['getHtml', [4], 'element/4/html'],
            ['getValue', [5], 'element/5/value'],
            ['isVisible', [6], 'element/6/visible'],
            ['isSelected', [7], 'element/7/selected'],
            ['isChecked', [8], 'element/8/checked'],
        ];
    }

    /**
     * @dataProvider dataGetters
     * @covers ::getJsMessages
     * @covers ::getJsErrors
     * @covers ::getFullHtml
     * @covers ::getText
     * @covers ::getTagName
     * @covers ::getAttribute
     * @covers ::getHtml
     * @covers ::getValue
     * @covers ::isVisible
     * @covers ::isSelected
     * @covers ::isChecked
     */
    public function testElementGetters($method, $params, $uri)
    {
        $expected = 'some value';

        $this->client
            ->expects($this->once())
            ->method('getJson')
            ->with($uri)
            ->willReturn($expected);

        $result = call_user_func_array([$this->driver, $method], $params);

        $this->assertEquals($expected, $result);
    }
}
