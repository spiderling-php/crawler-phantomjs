<?php

namespace SP\PhantomDriver\Test;

use PHPUnit_Framework_TestCase;
use SP\PhantomDriver\Browser;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\Response;
use SP\Spiderling\Query;

/**
 * @coversDefaultClass SP\PhantomDriver\Browser
 */
class BrowserTest extends PHPUnit_Framework_TestCase
{
    private $driver;
    private $server;
    private $client;

    public function setUp()
    {
        $this->client = $this->getMock('GuzzleHttp\ClientInterface');
        $this->driver = new Browser($this->client);
    }

    /**
     * @covers ::__construct
     * @covers ::getClient
     */
    public function testConstruct()
    {
        $driver = new Browser($this->client);

        $this->assertSame($this->client, $driver->getClient());
    }

    /**
     * @covers ::removeAllCookies
     */
    public function testRemoveAllCookies()
    {
        $this->client
            ->expects($this->once())
            ->method('request')
            ->with('delete', 'cookies');

        $this->driver->removeAllCookies();
    }

    /**
     * @covers ::jsonResponse
     */
    public function testJsonResponse()
    {
        $response = $this->driver->jsonResponse(new Response(200, [], '[{"one":"test"}]'));

        $this->assertSame([['one' => 'test']], $response);
    }

    /**
     * @covers ::getUri
     */
    public function testGetUri()
    {
        $this->client
            ->expects($this->once())
            ->method('request')
            ->with('get', 'url')
            ->willReturn(new Response(200, [], '"http://example.com"'));

        $result = $this->driver->getUri();

        $this->assertEquals(new Uri('http://example.com'), $result);
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
            ->method('request')
            ->with('post', $uri);

        call_user_func_array([$this->driver, $method], $params);
    }

    public function dataReturnSetters()
    {
        return [
            'executeJs' => [
                'executeJs',
                ['console.log("a")'],
                'execute',
                'console.log("a")'
            ],
            'getElementIds' => [
                'getElementIds',
                [new Query\Css('#test')],
                'elements',
                './/*[@id = \'test\']'
            ],
            'getChildElementIds' => [
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
        $response = new Response(200, [], '"return value"');

        $this->client
            ->expects($this->once())
            ->method('request')
            ->with('post', $uri, ['form_params' => ['value' => $value]])
            ->willReturn($response);

        $result = call_user_func_array([$this->driver, $method], $params);

        $this->assertEquals('return value', $result);
    }


    public function dataQueryIds()
    {
        return [
            'Global' => [
                new Query\Css('#test'),
                null,
                'elements',
                './/*[@id = \'test\']'
            ],
            'Children' => [
                new Query\Css('#me'),
                12,
                'element/12/elements',
                './/*[@id = \'me\']'
            ],
        ];
    }

    /**
     * @dataProvider dataQueryIds
     * @covers ::queryIds
     */
    public function testQueryIds($query, $parentId, $uri, $value)
    {
        $response = new Response(200, [], '["22", "23"]');

        $this->client
            ->expects($this->once())
            ->method('request')
            ->with('post', $uri, ['form_params' => ['value' => $value]])
            ->willReturn($response);

        $result = $this->driver->queryIds($query, $parentId);

        $this->assertEquals([22, 23], $result);
    }

    public function dataSetters()
    {
        return [
            'open' => ['open', [new Uri('http://example.com')], 'url', 'http://example.com'],
            'saveScreenshot' => ['saveScreenshot', ['file.jpg'], 'screenshot', 'file.jpg'],
            'confirm' => ['confirm', [true], 'confirm', true],
            'setValue' => ['setValue', [2, 'val'], 'element/2/value', 'val'],
            'setFile' => ['setFile', [2, 'file.txt'], 'element/2/upload', 'file.txt'],
        ];
    }

    /**
     * @dataProvider dataSetters
     * @covers ::open
     * @covers ::saveScreenshot
     * @covers ::setValue
     * @covers ::setFile
     * @covers ::confirm
     * @covers ::executeJs
     */
    public function testSetters($method, $params, $uri, $value)
    {
        $this->client
            ->expects($this->once())
            ->method('request')
            ->with('post', $uri, ['form_params' => ['value' => $value]]);

        call_user_func_array([$this->driver, $method], $params);
    }

    public function dataGetters()
    {
        return [
            ['getJsMessages', [], 'messages'],
            ['getJsErrors', [], 'errors'],
            ['getFullHtml', [], 'source'],
            ['getAlertText', [], 'alert'],
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
     * @covers ::getAlertText
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
        $response = new Response(200, [], '"return value"');

        $this->client
            ->expects($this->once())
            ->method('request')
            ->with('get', $uri)
            ->willReturn($response);

        $result = call_user_func_array([$this->driver, $method], $params);

        $this->assertEquals('return value', $result);
    }
}
