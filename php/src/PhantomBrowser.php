<?php

namespace SP\Driver;

use SP\Spiderling\BrowserInterface;
use Psr\Http\Message\UriInterface;
use Psr\Log\LoggerInterface;
use SP\Spiderling\Query;
use GuzzleHttp\Psr7\Uri;
use SP\Attempt\Attempt;
use GuzzleHttp\Psr7\Request;
/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2015, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class PhantomBrowser implements BrowserInterface
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var Client
     */
    private $client;

    public function __construct(Server $server = null, Client $client = null)
    {
        $this->server = $server ?: new Server();
        $this->client = $client ?: new Client();
    }

    /**
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return GuzzleHttp\Promise\Promise
     */
    public function start()
    {
        return $this->server->start();
    }

    /**
     * @return string
     */
    public function getAlertText()
    {

    }

    /**
     * @param  string $confirm
     */
    public function confirm($confirm)
    {

    }

    public function removeAllCookies()
    {
        $this->client->deleteJson('cookies');
    }

    /**
     * @param  string $javascript
     * @return mixed
     */
    public function executeJs($javascript)
    {
        return $this->client->postJson('execute', $javascript);
    }

    /**
     * @return array
     */
    public function getJsErrors()
    {
        return $this->client->getJson('errors');
    }

    /**
     * @return array
     */
    public function getJsMessages()
    {
        return $this->client->getJson('messages');
    }

    /**
     * @param  string $id
     */
    public function moveMouseTo($id)
    {
        $this->client->postJson("element/{$id}/hover");
    }

    /**
     * @param  string $file
     */
    public function saveScreenshot($file)
    {
        $this->client->postJson('screenshot', $file);
    }


    /**
     * @param  UriInterface $uri
     */
    public function open(UriInterface $uri)
    {
        $this->client->postJson('url', (string) $uri);
    }

    /**
     * @return UriInterface
     */
    public function getUri()
    {
        return new Uri($this->client->getJson('url'));
    }

    /**
     * @param  string $id
     * @return string
     */
    public function getText($id)
    {
        return trim($this->client->getJson("element/{$id}/text"));
    }

    /**
     * @param  string $id
     * @return string
     */
    public function getTagName($id)
    {
        return $this->client->getJson("element/{$id}/name");
    }

    /**
     * @param  string $id
     * @param  string $name
     * @return string
     */
    public function getAttribute($id, $name)
    {
        return $this->client->getJson("element/{$id}/attribute/{$name}");
    }

    /**
     * @param  string $id
     * @return string
     */
    public function getHtml($id)
    {
        return $this->client->getJson("element/{$id}/html");
    }

    /**
     * @return string
     */
    public function getFullHtml()
    {
        return $this->client->getJson('source');
    }

    /**
     * @param  string $id
     * @return string
     */
    public function getValue($id)
    {
        return $this->client->getJson("element/{$id}/value");
    }

    /**
     * @param  string $id
     * @return boolean
     */
    public function isVisible($id)
    {
        return $this->client->getJson("element/{$id}/visible");
    }

    /**
     * @param  string $id
     * @return boolean
     */
    public function isSelected($id)
    {
        return $this->client->getJson("element/{$id}/selected");
    }

    /**
     * @param  string $id
     * @return boolean
     */
    public function isChecked($id)
    {
        return $this->client->getJson("element/{$id}/checked");
    }

    /**
     * @param  string $id
     * @param  mixed  $value
     */
    public function setValue($id, $value)
    {
        $this->client->postJson("element/{$id}/value", $value);
    }

    /**
     * @param  string $id
     * @param  mixed  $file
     */
    public function setFile($id, $file)
    {
        $this->client->postJson("element/{$id}/upload", $file);
    }

    /**
     * @param  string $id
     */
    public function click($id)
    {
        $this->client->postJson("element/{$id}/click");
    }

    /**
     * @param  string $id
     */
    public function select($id)
    {
        $this->client->postJson("element/{$id}/select");
    }

    /**
     * @param  Query\AbstractQuery $query
     * @return array
     */
    public function getElementIds(Query\AbstractQuery $query)
    {
        return $this->client->postJson('elements', '.'.$query->getXPath());
    }

    /**
     * @param  Query\AbstractQuery $query
     * @param  string              $parentId
     * @return array
     */
    public function getChildElementIds(Query\AbstractQuery $query, $parentId)
    {
        return $this->client->postJson("element/$parentId/elements", '.'.$query->getXPath());
    }

    /**
     * @param  Query\AbstractQuery $query
     * @param  string              $parentId
     * @return array
     */
    public function queryIds(Query\AbstractQuery $query, $parentId = null)
    {
        $attempt = new Attempt(function() use ($query, $parentId) {
            $ids = $parentId === null
                ? $this->getElementIds($query)
                : $this->getChildElementIds($query, $parentId);

            return $query->getFilters()->matchAll($this, (array) $ids);
        });

        return $attempt->execute();
    }
}
