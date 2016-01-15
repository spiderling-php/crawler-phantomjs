<?php

namespace SP\PhantomDriver\Test;

use PHPUnit_Framework_TestCase;
use SP\PhantomDriver\Server;
use Symfony\Component\Process\Process;

/**
 * @coversDefaultClass SP\PhantomDriver\Server
 */
class ServerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getProcess
     * @covers ::getLogger
     */
    public function testConstruct()
    {
        $normal = new Server();

        $this->assertInstanceOf('Symfony\Component\Process\Process', $normal->getProcess());
        $this->assertInstanceOf('Psr\Log\NullLogger', $normal->getLogger());

        $logger = new TestLogger();
        $server = new Server('phantomjs test.js', $logger);

        $this->assertSame('phantomjs test.js', $server->getProcess()->getCommandline());
        $this->assertSame($logger, $server->getLogger());
    }

    /**
     * @covers ::getClient
     */
    public function testGetClient()
    {
        $server = new Server();
        $client = $server->getClient();

        $this->assertInstanceOf(
            'GuzzleHttp\ClientInterface',
            $client,
            'It should be a valid and proper interface for Brwoser'
        );
    }

    /**
     * @covers ::start
     * @covers ::wait
     * @covers ::getWaitAttempt
     * @covers ::isStarted
     */
    public function testStart()
    {
        $process = 'sleep .01 && echo "Started working" && echo "Some error message" >> /dev/stderr';

        $logger = new TestLogger();

        $server = new Server($process, $logger);

        $promise = $server->start();

        $this->assertInstanceOf(
            'GuzzleHttp\Promise\Promise',
            $promise,
            'Start should return a promise object with wait method'
        );

        $this->assertFalse(
            $server->isStarted(),
            'The server must not be started yet, waiting for furst text response'
        );

        $promise->wait();

        $this->assertTrue(
            $server->isStarted(),
            'The server should be started after the wait'
        );

        $expected = [
            "debug Started working\n",
            "error Some error message\n",
        ];

        $this->assertEquals(
            $expected,
            $logger->getEntries(),
            'Should have recorded the normal and error messages in the logger'
        );
    }
}
