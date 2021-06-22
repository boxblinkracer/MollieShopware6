<?php declare(strict_types=1);

namespace Kiener\MolliePayments\Core\Content\Checkout;


use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;


class MollieCheckoutDefinition extends EntityDefinition
{

    /**
     *
     */
    public const ENTITY_NAME = 'mollie_checkout';


    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    /**
     * @return string
     */
    public function getCollectionClass(): string
    {
        return MollieCheckoutCollection::class;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return MollieCheckoutEntity::class;
    }

    /**
     * @return FieldCollection
     */
    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            new StringField('transaction_id', 'transactionId'),
            new LongTextField('finalize_url', 'finalizeUrl'),
        ]);
    }

}
