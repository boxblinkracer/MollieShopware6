<?php declare(strict_types=1);

namespace Kiener\MolliePayments\Core\Content\Checkout;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;


class MollieCheckoutCollection extends EntityCollection
{

    /**
     * @return string
     */
    public function getApiAlias(): string
    {
        return 'mollie_checkout_collection';
    }

    /**
     * @return string
     */
    protected function getExpectedClass(): string
    {
        return MollieCheckoutEntity::class;
    }

}
