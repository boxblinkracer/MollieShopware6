<?php

namespace Kiener\MolliePayments\Handler\Method;

use Kiener\MolliePayments\Components\Mollie\Mollie;
use Kiener\MolliePayments\Factory\MollieFactory;
use Shopware\Core\Checkout\Order\Aggregate\OrderCustomer\OrderCustomerEntity;
use Shopware\Core\Checkout\Payment\Cart\AsyncPaymentTransactionStruct;
use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\AsynchronousPaymentHandlerInterface;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class P24Payment implements AsynchronousPaymentHandlerInterface
{

    /**
     * @var MollieFactory
     */
    private $mollieFactory;


    /**
     * @param MollieFactory $mollieFactory
     */
    public function __construct(MollieFactory $mollieFactory)
    {
        $this->mollieFactory = $mollieFactory;
    }

    /**
     * @param AsyncPaymentTransactionStruct $transaction
     * @param RequestDataBag $dataBag
     * @param SalesChannelContext $salesChannelContext
     * @throws \Mollie\Api\Exceptions\ApiException
     * @return RedirectResponse
     */
    public function pay(AsyncPaymentTransactionStruct $transaction, RequestDataBag $dataBag, SalesChannelContext $salesChannelContext): RedirectResponse
    {
        $amount = $transaction->getOrder()->getAmountTotal();

        $customer = $transaction->getOrder()->getOrderCustomer();

        $billingEmail = '';
        if ($customer instanceof OrderCustomerEntity) {
            $billingEmail = $customer->getEmail();
        }


        $mollie = $this->mollieFactory->buildMollie();

        $paymentURL = $mollie->createPayment('przelewy24', $amount, $billingEmail);

        return new RedirectResponse($paymentURL);
    }

    /**
     * @param AsyncPaymentTransactionStruct $transaction
     * @param Request $request
     * @param SalesChannelContext $salesChannelContext
     * @return void
     */
    public function finalize(AsyncPaymentTransactionStruct $transaction, Request $request, SalesChannelContext $salesChannelContext): void
    {
    }
}
