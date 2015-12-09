<?php

namespace SP\Phantomjs\Test;

use PHPUnit_Framework_TestCase;
use SP\Phantomjs\Server;
use Symfony\Component\Process\Process;

/**
 * @coversDefaultClass SP\Phantomjs\Server
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
        $process = new Process('phantomjs echo.js');
        $logger = new TestLogger();

        $server = new Server($process, $logger);

        $this->assertSame($process, $server->getProcess());
        $this->assertSame($logger, $server->getLogger());
    }

    /**
     * @covers ::start
     * @covers ::wait
     * @covers ::isStarted
     */
    public function testStart()
    {
        $process = new Process('sleep .01 && echo "Started working" && echo "Some error message" >> /dev/stderr');

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
