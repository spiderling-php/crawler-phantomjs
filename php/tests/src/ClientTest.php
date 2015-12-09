<?php

namespace SP\Driver\Test;

use PHPUnit_Framework_TestCase;
use SP\Driver\Client;
use GuzzleHttp\Psr7\Response;

/**
 * @coversDefaultClass SP\Driver\Client
 */
class ClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $client = new Client();

        $this->assertEquals('http://localhost:8281', $client->getConfig('base_uri'));
    }

    /**
     * @covers ::getJson
     */
    public function testGetJson()
    {
        $client = $this->getMock('SP\Driver\Client', ['get']);

        $client
            ->expects($this->once())
            ->method('get')
            ->with('test')
            ->willReturn(new Response(200, [], '["test","big"]'));

        $return = $client->getJson('test');

        $this->assertEquals(['test', 'big'], $return);
    }

    /**
     * @covers ::deleteJson
     */
    public function testDeleteJson()
    {
        $client = $this->getMock('SP\Driver\Client', ['delete']);

        $client
            ->expects($this->once())
            ->method('delete')
            ->with('test')
            ->willReturn(new Response(200, [], '["test","big"]'));

        $return = $client->deleteJson('test');

        $this->assertEquals(['test', 'big'], $return);
    }

    /**
     * @covers ::postJson
     */
    public function testPostJson()
    {
        $client = $this->getMock('SP\Driver\Client', ['post']);

        $client
            ->expects($this->once())
            ->method('post')
            ->with('test', ['form_params' => ['value' => 'big']])
            ->willReturn(new Response(200, [], '["test","big"]'));

        $return = $client->postJson('test', 'big');

        $this->assertEquals(['test', 'big'], $return);
    }
}
