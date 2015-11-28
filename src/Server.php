<?php

namespace SP\Phantomjs;

use Symfony\Component\Process\Process as SymfonyProcess;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RuntimeException;
use SP\Attempt\Attempt;
use GuzzleHttp\Promise\Promise;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2015, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class Server
{
    /**
     * @var Symfony\Component\Process\Process
     */
    private $process;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(SymfonyProcess $process = null, LoggerInterface $logger = null)
    {
        $this->process = $process ?: new Process();
        $this->logger = $logger ?: new NullLogger();
    }

    public function wait()
    {
        $attempt = new Attempt(function () {
            return $this->isStarted();
        });

        if (false === $attempt->execute()) {
            throw new RuntimeException(sprintf(
                'Process %s failed to start properly',
                $this->process->getCommandLine()
            ));
        }

        return true;
    }

    public function start()
    {
        $promise = new Promise([$this, 'wait']);

        $this->process->start(function ($type, $buffer) use ($promise) {
            if (Process::ERR === $type) {
                $this->logger->error($buffer);
            } else {
                $this->logger->debug($buffer);
            }

            if ($promise->getState() === Promise::PENDING) {
                if (Process::ERR === $type) {
                    $promise->reject(false);
                } else {
                    $promise->resolve(true);
                }
            }
        });

        return $promise;
    }

    public function getProcess()
    {
        return $this->process;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function isStarted()
    {
        return (bool) $this->process->getOutput();
    }
}
