<?php
/**
 * User: blorenz
 * Date: 11.03.14
 * Time: 11:39
 */

namespace Soprado\MyraSecurityBundle\Authentication;

/**
 * Class ISignature
 *
 * @package Soprado\MyraRestBundle\Authentication
 */
interface ISignature
{
    /**
     * Returns a generated signature for a request / user combination.
     *
     * @return string
     */
    public function retrieveSignature();

    /**
     * Returns the string that will be signed.
     *
     * @return string
     */
    public function getStringToSign();

    /**
     * @param string $method
     * @param string $uri
     * @param string $secret
     * @param array $headers
     * @param string $content
     * @return ISignature
     */
    public static function create($method = null,
                                  $uri = null,
                                  $secret = null,
                                  array $headers = null,
                                  $content = null);
}
