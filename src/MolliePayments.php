<?php declare(strict_types=1);

namespace Kiener\MolliePayments;

use Exception;
use Kiener\MolliePayments\Components\Installer\PaymentInstaller;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MolliePayments extends Plugin
{

    /**
     * @param ContainerBuilder $container
     * @throws Exception
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $this->container = $container;
    }


    /**
     * @param InstallContext $context
     * @return void
     */
    public function install(InstallContext $context): void
    {
        parent::install($context);

        $this->installMethods($context->getContext());
    }

    /**
     * @param ActivateContext $context
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function activate(ActivateContext $context): void
    {
        parent::activate($context);

        $this->installMethods($context->getContext());
    }


    /**
     * @return void
     */
    private function installMethods(Context $context)
    {
        /** @var PaymentInstaller $installer * */
        $installer = $this->container->get(PaymentInstaller::class);

        $installer->installPaymentMethods($context);
    }

}
