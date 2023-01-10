<?php

namespace Kiener\MolliePayments\Tests\Components\Configuration;


use Kiener\MolliePayments\Components\Configuration\PluginConfiguration;
use PHPUnit\Framework\TestCase;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class PluginConfigurationTest extends TestCase
{


    /**
     * @return void
     */
    public function testGetApiKey()
    {
        $mockSystemConfigService = $this->getMockBuilder(SystemConfigService::class)->disableOriginalConstructor()->getMock();

        $mockSystemConfigService
            ->expects($this->any())
            ->method('get')
            ->with($this->equalTo('MolliePayments.config.apiKey'))
            ->willReturn('fake-key');

        /** @var SystemConfigService $mockSystemConfigService */
        $config = new PluginConfiguration($mockSystemConfigService);

        $apiKey = $config->getApiKey();

        $this->assertEquals('fake-key', $apiKey);
    }

}