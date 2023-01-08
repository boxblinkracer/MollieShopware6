<?php

namespace Kiener\MolliePayments\Components\Installer;


use Kiener\MolliePayments\Handler\Method\P24Payment;
use Kiener\MolliePayments\MolliePayments;
use Shopware\Core\Checkout\Payment\PaymentMethodEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin\Util\PluginIdProvider;

class PaymentInstaller
{

    /**
     * @var EntityRepositoryInterface
     */
    private $repoPaymentMethods;

    /**
     * @var PluginIdProvider
     */
    private $pluginIdProvider;

    /**
     * @param EntityRepositoryInterface $paymentRepository
     * @param PluginIdProvider $pluginIdProvider
     */
    public function __construct(EntityRepositoryInterface $paymentRepository, PluginIdProvider $pluginIdProvider)
    {
        $this->repoPaymentMethods = $paymentRepository;
        $this->pluginIdProvider = $pluginIdProvider;
    }


    /**
     * @param Context $context
     * @return void
     */
    public function installPaymentMethods(Context $context): void
    {
        $this->addPaymentMethods(P24Payment::class, 'P24', $context);
    }

    /**
     * @param string $className
     * @param string $name
     * @param Context $context
     * @return void
     */
    public function addPaymentMethods(string $className, string $name, Context $context): void
    {
        die($className);
        $identifier = get_class($className);

        $pluginId = $this->pluginIdProvider->getPluginIdByBaseClass(MolliePayments::class, $context);

        $upsertData = [];


        try {
            $existingPaymentMethod = $this->getPaymentMethod($identifier, $context);
        } catch (InconsistentCriteriaIdsException $e) {
            $existingPaymentMethod = null;
        }

        if ($existingPaymentMethod instanceof PaymentMethodEntity) {
            $paymentMethodData = [
                'id' => $existingPaymentMethod->getId(),
                'handlerIdentifier' => $identifier,
                'pluginId' => $pluginId,
                'name' => $existingPaymentMethod->getName(),
            ];

            $upsertData[] = $paymentMethodData;
        } else {
            $paymentMethodData = [
                'handlerIdentifier' => $identifier,
                'pluginId' => $pluginId,
                # ------------------------------------------
                'name' => $name,
                'description' => '',
                'afterOrderEnabled' => true,
            ];

            $upsertData[] = $paymentMethodData;
        }

        $this->repoPaymentMethods->upsert($upsertData, $context);
    }

    /**
     * @param string $handlerIdentifier
     * @param Context $context
     * @return PaymentMethodEntity|null
     */
    private function getPaymentMethod(string $handlerIdentifier, Context $context): ?PaymentMethodEntity
    {
        $paymentCriteria = new Criteria();
        $paymentCriteria->addFilter(new EqualsFilter('handlerIdentifier', $handlerIdentifier));

        $paymentMethods = $this->repoPaymentMethods->search($paymentCriteria, $context);

        if ($paymentMethods->getTotal() === 0) {
            return null;
        }

        return $paymentMethods->first();
    }

}
