<?php

namespace Kiener\MolliePayments\Handler;

use Kiener\MolliePayments\Components\Mollie\Mollie;
use Shopware\Core\Checkout\Payment\Cart\AsyncPaymentTransactionStruct;
use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\AsynchronousPaymentHandlerInterface;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class P24Payment implements AsynchronousPaymentHandlerInterface
{

    /**
     * @var Mollie
     */
    private $mollie;


    /**
     * @param Mollie $mollie
     */
    public function __construct(Mollie $mollie)
    {
        $this->mollie = $mollie;
    }

    /**
     * @param AsyncPaymentTransactionStruct $transaction
     * @param RequestDataBag $dataBag
     * @param SalesChannelContext $salesChannelContext
     * @return RedirectResponse
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function pay(AsyncPaymentTransactionStruct $transaction, RequestDataBag $dataBag, SalesChannelContext $salesChannelContext): RedirectResponse
    {
        $amount = $transaction->getOrder()->getAmountTotal();

        $paymentURL = $this->mollie->createPayment('p24', $amount);

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
