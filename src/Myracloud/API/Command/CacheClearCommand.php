<?php
/**
 * User: blorenz
 * Date: 15.03.16
 * Time: 10:57
 */

namespace Myracloud\API\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CacheClear
 *
 * @package Myracloud\API\Command
 */
class CacheClearCommand extends AbstractCommand
{
    /** @var array */
    private $options;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('myracloud:api:cache');
        $this->addArgument('apiKey', InputArgument::REQUIRED, 'Api key to authenticate against Myracloud API', null);
        $this->addArgument('fqdn', InputArgument::REQUIRED, 'Domain that should be used to clear the cache.');

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $content = [
            'fqdn'      => 'www.example.com',
            'resource'  => '/*.jpg',
            'recursive' => true
        ];

        $curl = curl_init();


        //PUT /{language}/rapi/cacheClear/{domain} HTTP/1.1
        //Host: api.myracloud.com
        //Date: 2014-05-02T07:17+0200
        //Authorization: MYRA {apiKey}:{signature}
        //{
        //"fqdn"
        //"resource"
        //"recursive"
        //: "www.example.com",
        //: "/*.jpg",
        //: true
        //}
    }


}
