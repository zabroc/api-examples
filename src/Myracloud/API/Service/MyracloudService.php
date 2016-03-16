<?php

namespace Myracloud\API\Service;

use Myracloud\API\Authentication\MyraSignature;
use Myracloud\API\Command\AbstractCommand;
use Myracloud\API\Exception\ApiCallException;
use Myracloud\API\Exception\PermissionDeniedException;
use Myracloud\API\Exception\UnknownErrorException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MyracloudService
 *
 * @package Myracloud\API\Service
 */
class MyracloudService
{
    const METHOD_CREATE = 'PUT';
    const METHOD_UPDATE = 'POST';
    const METHOD_DELETE = 'DELETE';
    const METHOD_LIST = 'GET';

    /** @var OutputInterface */
    private $output;

    /** @var OptionsResolver */
    private $requestResolver;

    /**
     * Prepares the header data for curl.
     * Also append the authentication part for the request that is created.
     *
     * @param string $url
     * @param array $options
     * @return array
     */
    private function prepareHeaderData($url, array $options)
    {
        $signature = MyraSignature::create(
            $options['method'],
            $url,
            $options['secret'],
            $options['header'],
            $options['content']
        );

        $tmp = array_merge($options['header'], [
            'Authorization' => 'MYRA ' . $options['apiKey'] . ':' . $signature->retrieveSignature()
        ]);

        $headerData = [];
        foreach ($tmp as $key => $value) {
            $headerData[] = sprintf("%s: %s", $key, $value);
        }

        return $headerData;
    }

    /**
     * Handles cache clear operations.
     *
     * @param string $method
     * @param string $fqdn
     * @param array $data
     * @return mixed
     * @throws ApiCallException
     */
    public function cacheClear($method = self::METHOD_LIST, $fqdn = '', array $data = [])
    {
        try {
            return $this->request([
                'method'  => $method,
                'url'     => 'cacheClear/' . trim($fqdn, '.'),
                'content' => json_encode($data),
            ]);
        } catch (ApiCallException $ex) {
            if (!$this->output) {
                throw $ex;
            }

            print_r($ex->getData());

            $retData = $ex->getData();
            $row     = $retData['targetObject'][0];

            foreach ($retData['violationList'] as $violation) {
                $this->output->writeln(sprintf(
                    '<fg=red;options=bold>%s [property="%s", givenValue="%s"]</>',
                    $violation['message'],
                    $violation['propertyPath'],
                    $row[$violation['propertyPath']]
                ));
            }
        }

        return null;
    }

    /**
     * Calls the given command
     *
     * @param array $options
     * @return mixed|null
     * @throws ApiCallException
     * @throws PermissionDeniedException
     * @throws UnknownErrorException
     */
    protected function request(array $options)
    {
        $options = $this->requestResolver->resolve($options);

        $url      = '/' . $options['language'] . '/rapi/' . ltrim($options['url'], '/');
        $endpoint = rtrim($options['apiEndpoint'], '/') . $url;

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST  => $options['method'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $this->prepareHeaderData($url, $options),
        ]);

        if (!empty($options['content']) && $options['method'] !== self::METHOD_LIST) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $options['content']);
        }

        $ret        = curl_exec($ch);
        $returnCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = null;

        switch ($returnCode) {
            case 403:
                throw new PermissionDeniedException();

            case 200:
                $data = json_decode($ret, true);

                if ($data['error']) {
                    throw new ApiCallException('There was an error in the last api call.', $data);
                }

                break;

            default:
                throw new UnknownErrorException();
        }

        return $data;
    }

    /**
     * MyracloudService constructor.
     *
     * @param array $defaults
     */
    public function __construct(array $defaults, OutputInterface $output = null)
    {
        $this->requestResolver = new OptionsResolver();
        $this->requestResolver->setDefaults(array_merge($defaults, [
            'apiEndpoint' => AbstractCommand::DEFAULT_API_ENDPOINT,
            'language'    => AbstractCommand::DEFAULT_LANGUAGE,
            'method'      => self::METHOD_LIST,
            'content'     => null,
            'header'      => [
                'Date'         => date('c'),
                'Content-Type' => 'application/json',
            ]
        ]));

        $this->requestResolver->setRequired(['method', 'url', 'apiKey', 'secret']);
        $this->output = $output;
    }
}
