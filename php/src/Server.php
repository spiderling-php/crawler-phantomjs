<?php

namespace SP\PhantomDriver;

use Symfony\Component\Process\Process;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RuntimeException;
use SP\Attempt\Attempt;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Client;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2015, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class Server
{
    /**
     * @var SymfonyProcess
     */
    private $process;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        $command = 'phantomjs server.js --ssl-protocol=any --ignore-ssl-errors=true --port=8281',
        LoggerInterface $logger = null
    ) {
        $this->process = new Process($command, realpath(__DIR__.'/../../js/src'));
        $this->logger = $logger ?: new NullLogger();
    }

    public function getClient(array $options = [])
    {
        return new Client(array_merge(['base_uri' => 'http://localhost:8281'], $options));
    }

    /**
     * @return Attempt
     */
    public function getWaitAttempt()
    {
        return new Attempt(function () {
            return $this->isStarted();
        });
    }

    /**
     * @return bool
     * @throws RuntimeException
     */
    public function wait()
    {
        return $this->getWaitAttempt()->executeOrFail();
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
