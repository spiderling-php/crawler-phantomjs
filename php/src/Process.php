<?php

namespace SP\Driver;

use Symfony\Component\Process\Process as SymfonyProcess;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2015, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class Process extends SymfonyProcess
{
    public function __construct(
        $commandline = null,
        $cwd = null,
        array $env = null,
        $input = null,
        $timeout = 60,
        array $options = array()
    ) {
        parent::__construct(
            $commandline ?: 'phantomjs server.js --ssl-protocol=any --ignore-ssl-errors=true',
            $cwd ?: realpath(__DIR__.'/../../js/src'),
            $env,
            $input,
            $timeout,
            $options
        );
    }
}
