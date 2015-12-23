<?php

namespace SP\PhantomDriver;

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

    /**
     * @param  string $uri
     */
    public function deleteJson($uri)
    {
        $response = $this->delete($uri);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param  string $uri
     */
    public function getJson($uri)
    {
        $response = $this->get($uri);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param  string $uri
     * @param  string $value
     */
    public function postJson($uri, $value = null)
    {
        $options = $value ? ['form_params' => ['value' => $value]] : [];
        $response = $this->post($uri, $options);

        return json_decode($response->getBody()->getContents(), true);
    }
}
