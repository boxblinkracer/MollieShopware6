<?php

namespace Kiener\MolliePayments\Factory;

use Kiener\MolliePayments\Components\Configuration\PluginConfiguration;
use Kiener\MolliePayments\Components\Mollie\Api\MollieApi;
use Kiener\MolliePayments\Components\Mollie\Mollie;

class MollieFactory
{

    /**
     * @var PluginConfiguration
     */
    private $pluginConfig;


    /**
     * @param PluginConfiguration $configuration
     */
    public function __construct(PluginConfiguration $configuration)
    {
        $this->pluginConfig = $configuration;
    }

    /**
     * @return Mollie
     */
    public function buildMollie(): Mollie
    {
        $apiKey = $this->pluginConfig->getApiKey();

        $client = new MollieApi($apiKey);

        return new Mollie($client);
    }

}
