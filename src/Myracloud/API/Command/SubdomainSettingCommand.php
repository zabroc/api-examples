<?php

namespace Myracloud\API\Command;

use Myracloud\API\Service\MyracloudService;
use Myracloud\API\Util\Normalizer;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Statistic
 *
 * @package Myracloud\API\Statistic
 */
class SubdomainSettingCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('myracloud:api:subdomainSetting');
        $this->addArgument('apiKey', InputArgument::REQUIRED, 'Api key to authenticate against Myra API.', null);
        $this->addArgument('secret', InputArgument::REQUIRED, 'Secret to authenticate against Myra API.', null);
        $this->addArgument('fqdn', InputArgument::REQUIRED, 'Domain used for listings.');


        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->resolver->setDefaults([
            'noCheckCert' => false,
            'apiKey' => null,
            'secret' => null,
            'fqdn' => null,
            'language' => self::DEFAULT_LANGUAGE,
            'apiEndpoint' => self::DEFAULT_API_ENDPOINT,
            'proxy' => null,
        ]);

        $this->resolver->setNormalizer('fqdn', Normalizer::normalizeFqdn());
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->resolveOptions($input, $output);

        $ret = $this->service->subdomainSetting(MyracloudService::METHOD_LIST, $this->options['fqdn']);

        if ($output->isVerbose()) {
            print_r($ret);
        }

        if ($ret) {
            $table = new Table($output);
            $table->setHeaders(['Key', 'Value']);
            foreach ($ret as $key => $value) {
                if (is_array($value)) {
                    $value = json_encode($value);
                }

                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                }
                $table->addRow([$key, $value]);
            }
            $table->render();
        }
    }
}
