<?php

namespace SP\PhantomDriver\Test;

use Psr\Log\AbstractLogger;

class TestLogger extends AbstractLogger
{
    /**
     * @var array
     */
    private $entries = [];

    public function log($level, $message, array $context = array())
    {
        $this->entries []= "$level $message";
    }

    /**
     * @return array
     */
    public function getEntries()
    {
        return $this->entries;
    }
}
