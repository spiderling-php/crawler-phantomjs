<?php

namespace SP\Phantomjs\Test;

use PHPUnit_Framework_TestCase;
use SP\Phantomjs\Process;
use GuzzleHttp\Psr7\Response;

/**
 * @coversDefaultClass SP\Phantomjs\Process
 */
class ProcessTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $process = new Process();

        $this->assertEquals(
            'phantomjs server.js --ssl-protocol=any --ignore-ssl-errors=true',
            $process->getCommandLine()
        );

        $this->assertEquals(
            realpath(__DIR__.'/../../../js/src'),
            $process->getWorkingDirectory()
        );
    }
}
