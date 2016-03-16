<?php

namespace Myracloud\API\Exception;

/**
 * Class ApiCallException
 *
 * @package Myracloud\API
 */
class ApiCallException extends \Exception
{
    /** @var array */
    private $data;

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * ApiCallException constructor.
     *
     * @param string $message
     * @param array $data
     */
    public function __construct($message, array $data = [])
    {
        parent::__construct($message, 0, null);

        $this->data = $data;
    }
}
