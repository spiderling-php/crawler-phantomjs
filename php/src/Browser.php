<?php

namespace SP\PhantomDriver;

use SP\Spiderling\BrowserInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use SP\Spiderling\Query;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Uri;
use SP\Attempt\Attempt;
use GuzzleHttp\Psr7\Request;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright 2015, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class Browser implements BrowserInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    public function jsonResponse(ResponseInterface $response)
    {
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @return string
     */
    public function getAlertText()
    {
        return $this->jsonResponse($this->client->request('get', 'alert'));
    }

    /**
     * @param  boolean $confirm
     */
    public function confirm($confirm)
    {
        $this->client->request('post', 'confirm', ['form_params' => ['value' => (bool) $confirm]]);
    }

    public function removeAllCookies()
    {
        $this->client->request('delete', 'cookies');
    }

    /**
     * @param  string $javascript
     * @return mixed
     */
    public function executeJs($javascript)
    {
        return $this->jsonResponse($this->client->request('post', 'execute', ['form_params' => ['value' => $javascript]]));
    }

    /**
     * @return array
     */
    public function getJsErrors()
    {
        return $this->jsonResponse($this->client->request('get', 'errors'));
    }

    /**
     * @return array
     */
    public function getJsMessages()
    {
        return $this->jsonResponse($this->client->request('get', 'messages'));
    }

    /**
     * @param  string $id
     */
    public function moveMouseTo($id)
    {
        $this->client->request('post', "element/{$id}/hover");
    }

    /**
     * @param  string $file
     */
    public function saveScreenshot($file)
    {
        $this->client->request('post', 'screenshot', ['form_params' => ['value' => $file]]);
    }


    /**
     * @param  UriInterface $uri
     */
    public function open(UriInterface $uri)
    {
        $this->client->request('post', 'url', ['form_params' => ['value' => (string) $uri]]);
    }

    /**
     * @return UriInterface
     */
    public function getUri()
    {
        return new Uri($this->jsonResponse($this->client->request('get', 'url')));
    }

    /**
     * @param  string $id
     * @return string
     */
    public function getText($id)
    {
        return trim($this->jsonResponse($this->client->request('get', "element/{$id}/text")));
    }

    /**
     * @param  string $id
     * @return string
     */
    public function getTagName($id)
    {
        return $this->jsonResponse($this->client->request('get', "element/{$id}/name"));
    }

    /**
     * @param  string $id
     * @param  string $name
     * @return string
     */
    public function getAttribute($id, $name)
    {
        return $this->jsonResponse($this->client->request('get', "element/{$id}/attribute/{$name}"));
    }

    /**
     * @param  string $id
     * @return string
     */
    public function getHtml($id)
    {
        return $this->jsonResponse($this->client->request('get', "element/{$id}/html"));
    }

    /**
     * @return string
     */
    public function getFullHtml()
    {
        return $this->jsonResponse($this->client->request('get', 'source'));
    }

    /**
     * @param  string $id
     * @return string
     */
    public function getValue($id)
    {
        return $this->jsonResponse($this->client->request('get', "element/{$id}/value"));
    }

    /**
     * @param  string $id
     * @return boolean
     */
    public function isVisible($id)
    {
        return $this->jsonResponse($this->client->request('get', "element/{$id}/visible"));
    }

    /**
     * @param  string $id
     * @return boolean
     */
    public function isSelected($id)
    {
        return $this->jsonResponse($this->client->request('get', "element/{$id}/selected"));
    }

    /**
     * @param  string $id
     * @return boolean
     */
    public function isChecked($id)
    {
        return $this->jsonResponse($this->client->request('get', "element/{$id}/checked"));
    }

    /**
     * @param  string $id
     * @param  mixed  $value
     */
    public function setValue($id, $value)
    {
        $this->client->request('post', "element/{$id}/value", ['form_params' => ['value' => $value]]);
    }

    /**
     * @param  string $id
     * @param  mixed  $file
     */
    public function setFile($id, $file)
    {
        $this->client->request('post', "element/{$id}/upload", ['form_params' => ['value' => $file]]);
    }

    /**
     * @param  string $id
     */
    public function click($id)
    {
        $this->client->request('post', "element/{$id}/click");
    }

    /**
     * @param  string $id
     */
    public function select($id)
    {
        $this->client->request('post', "element/{$id}/select");
    }

    /**
     * @param  Query\AbstractQuery $query
     * @return array
     */
    public function getElementIds(Query\AbstractQuery $query)
    {
        return $this->jsonResponse(
            $this->client->request('post',
                'elements',
                ['form_params' => ['value' => '.'.$query->getXPath()]]
            )
        );
    }

    /**
     * @param  Query\AbstractQuery $query
     * @param  string              $parentId
     * @return array
     */
    public function getChildElementIds(Query\AbstractQuery $query, $parentId)
    {
        return $this->jsonResponse(
            $this->client->request('post',
                "element/$parentId/elements",
                ['form_params' => ['value' => '.'.$query->getXPath()]]
            )
        );
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
