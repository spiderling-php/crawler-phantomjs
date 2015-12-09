<?php

namespace SP\Driver;

use SP\Spiderling\BrowserSession;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2015, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class PhantomSession extends BrowserSession
{
    public function __construct(PhantomBrowser $browser = null)
    {
        parent::__construct($browser ?: new PhantomBrowser());
    }
}
