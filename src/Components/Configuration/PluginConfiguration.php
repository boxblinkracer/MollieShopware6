<?php

namespace Kiener\MolliePayments\Components\Configuration;

use Shopware\Core\System\SystemConfig\SystemConfigService;

class PluginConfiguration
{

    /**
     * @var SystemConfigService
     */
    private $configService;


    /**
     * @param SystemConfigService $configService
     */
    public function __construct(SystemConfigService $configService)
    {
        $this->configService = $configService;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->configService->getString('MolliePayments.config.apiKey');
    }
}
