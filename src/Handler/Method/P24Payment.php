<?php

namespace Kiener\MolliePayments\Handler\Method;

use Kiener\MolliePayments\Components\Configuration\PluginConfiguration;
use Mollie\Api\MollieApiClient;
use Shopware\Core\Checkout\Payment\Cart\AsyncPaymentTransactionStruct;
use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\AsynchronousPaymentHandlerInterface;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class P24Payment implements AsynchronousPaymentHandlerInterface
{

    /**
     * @var PluginConfiguration
     */
    private $configuration;


    /**
     * @param PluginConfiguration $configuration
     */
    public function __construct(PluginConfiguration $configuration)
    {
        $this->configuration = $configuration;
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

        $params = [
            'amount' => [
                'value' => $this->formatValue($amount),
                'currency' => 'EUR',
            ],
            'method' => 'przelewy24',
            'redirectUrl' => 'https://mollie.com',
            'description' => 'Shopware Order',
        ];


        $client = new MollieApiClient();
        $client->setApiKey($this->configuration->getApiKey());

        $payment = $client->payments->create($params);

        $checkoutUrl = (string)$payment->getCheckoutUrl();

        return new RedirectResponse($checkoutUrl);
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


    /**
     * @param null|float $price
     * @return string
     */
    public function formatValue(?float $price)
    {
        if (is_null($price)) {
            $price = 0.0;
        }

        return number_format(round($price, 2), 2, '.', '');
    }
}
