<?php

namespace SP\Phantomjs;

use GuzzleHttp\Client as GuzzleClient;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2015, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class Client extends GuzzleClient
{
    public function __construct(array $config = array())
    {
        parent::__construct(
            array_merge(
                ['base_uri' => 'http://localhost:8281'],
                $config
            )
        );
    }

    public function deleteJson($uri)
    {
        $response = $this->delete($uri);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getJson($uri)
    {
        $response = $this->get($uri);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function postJson($uri, $value = null)
    {
        $options = $value ? ['form_params' => ['value' => $value]] : null;
        $response = $this->post($uri, $options);

        return json_decode($response->getBody()->getContents(), true);
    }
}
