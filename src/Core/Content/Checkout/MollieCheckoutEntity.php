<?php

namespace Kiener\MolliePayments\Core\Content\Checkout;


use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;


class MollieCheckoutEntity extends Entity
{
    use EntityIdTrait;


    /**
     * @var string
     */
    protected $transactionId;

    /**
     * @var string
     */
    protected $finalizeUrl;


    /**
     * @return string
     */
    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    /**
     * @param string $transactionId
     */
    public function setTransactionId(string $transactionId): void
    {
        $this->transactionId = $transactionId;
    }

    /**
     * @return string
     */
    public function getFinalizeUrl(): string
    {
        return $this->finalizeUrl;
    }

    /**
     * @param string $finalizeUrl
     */
    public function setFinalizeUrl(string $finalizeUrl): void
    {
        $this->finalizeUrl = $finalizeUrl;
    }

}
