<?php

namespace Bandwidth\HttpClient;

use Guzzle\Common\Event;

/**
 * AuthHandler takes care of devising the auth type and using it
 */
class AuthHandler
{
    private $auth;

    const HTTP_PASSWORD = 0;

    const HTTP_HEADER = 1;

    public function __construct(array $auth = array())
    {
        $this->auth = $auth;
    }

    /**
     * Calculating the Authentication Type
     */
    public function getAuthType()
    {

        if (isset($this->auth['username']) && isset($this->auth['password'])) {
            return self::HTTP_PASSWORD;
        }

        if (isset($this->auth['http_header'])) {
            return self::HTTP_HEADER;
        }

        return -1;
    }

    public function onRequestBeforeSend(Event $event)
    {
        if (empty($this->auth)) {
            throw new \ErrorException('Server requires authentication to proceed further. Please check');
        }

        $auth = $this->getAuthType();
        $flag = false;

        if ($auth == self::HTTP_PASSWORD) {
            $this->httpPassword($event);
            $flag = true;
        }

        if ($auth == self::HTTP_HEADER) {
            $this->httpHeader($event);
            $flag = true;
        }

        if (!$flag) {
            throw new \ErrorException('Unable to calculate authorization method. Please check.');
        }
    }

    /**
     * Basic Authorization with username and password
     */
    public function httpPassword(Event $event)
    {
        $event['request']->setHeader('Authorization', sprintf('Basic %s', base64_encode($this->auth['username'] . ':' . $this->auth['password'])));
    }

    /**
     * Authorization with HTTP header
     */
    public function httpHeader(Event $event)
    {
        $event['request']->setHeader('Authorization', sprintf('token %s', $this->auth['http_header']));
    }

}
