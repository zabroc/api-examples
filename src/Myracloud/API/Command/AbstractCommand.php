<?php

namespace Myracloud\API\Command;

use Myracloud\API\Authentication\MyraSignature;
use Myracloud\API\Service\MyracloudService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbstractCommand
 *
 * @package Myracloud\API\Command
 */
abstract class AbstractCommand extends Command
{
    const DEFAULT_API_ENDPOINT = 'https://api.myracloud.com';
    const DEFAULT_LANGUAGE = 'en';

    /** @var array */
    protected $options = [];

    /** @var OptionsResolver */
    protected $resolver;

    /** @var MyracloudService */
    protected $service;

    /**
     * Resolve given options
     *
     * @return void
     */
    protected function resolveOptions(InputInterface $input, OutputInterface $output)
    {
        $data = array_merge($input->getArguments(), $input->getOptions());
        $data = array_intersect_key($data, array_flip($this->resolver->getDefinedOptions()));

        $this->options = $this->resolver->resolve($data);

        $this->service = new MyracloudService([
            'apiEndpoint' => $this->options['apiEndpoint'],
            'apiKey'      => $this->options['apiKey'],
            'secret'      => $this->options['secret'],
            'language'    => $this->options['language'],
            'verbose'     => $output->isVeryVerbose(),
            'noCheckCert' => $this->options['noCheckCert'],
        ], $output);
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->resolver = new OptionsResolver();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addOption('language', 'l', InputOption::VALUE_REQUIRED, 'Api language to use.', self::DEFAULT_LANGUAGE);
        $this->addOption('apiEndpoint', null, InputOption::VALUE_REQUIRED, 'Api endpoint to use.', self::DEFAULT_API_ENDPOINT);
        $this->addOption('noCheckCert', null, InputOption::VALUE_NONE, 'Disable curl pear verification.');
    }
}
