<?php

namespace Myracloud\API\Authentication;

/**
 * Class MyraSignature
 *
 * @package Soprado\MyraRestBundle\Authentication
 */
class MyraSignature implements ISignature
{

    /** @var array */
    private $headers;

    /** @var string */
    private $content;

    /** @var string */
    private $uri;

    /** @var string */
    private $secret;

    /** @var string */
    private $method = 'GET';

    /**
     * {@inheritDoc}
     */
    public static function create($method = 'GET',
                                  $uri = null,
                                  $secret = null,
                                  array $headers = null,
                                  $content = null)
    {
        $signature          = new self();
        $signature->content = $content;
        $signature->uri     = $uri;
        $signature->secret  = $secret;
        $signature->method  = $method;
        $signature->headers = $headers;

        return $signature;
    }

    /**
     * Returns always the last set header data.
     *
     * @param string $data Headername to look for.
     * @param mixed $default Default is returned when header is not set.
     * @return mixed
     */
    private function getHeaderData($data, $default = '')
    {
        if (!isset($this->headers[$data])) {
            return $default;
        }

        if (is_array($this->headers[$data])) {
            return $this->headers[$data][count($this->headers[$data]) - 1];
        }

        return $this->headers[$data];
    }

    /**
     * {@inheritdoc}
     */
    public function getStringToSign()
    {
        $signingString = md5($this->content);
        $signingString .= '#' . $this->method;
        $signingString .= '#' . $this->uri;
        $signingString .= '#' . $this->getHeaderData('Content-Type');
        $signingString .= '#' . $this->getHeaderData('Date');

        return $signingString;
    }

    /**
     * {@inheritDoc}
     */
    public function retrieveSignature()
    {
        $signingString = $this->getStringToSign();

        $key = hash_hmac('sha256', $this->getHeaderData('Date'), 'MYRA' . $this->secret);
        $key = hash_hmac('sha256', 'myra-api-request', $key);

        return base64_encode(hash_hmac('sha512', $signingString, $key, true));
    }

    /**
     * Constructor.
     */
    private function __construct()
    {
        $this->headers = array();
    }
}
