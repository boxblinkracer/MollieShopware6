<?php
declare(strict_types=1);

namespace Kiener\MolliePayments\Subscriber;

use Kiener\MolliePayments\Factory\MollieApiFactory;
use Kiener\MolliePayments\Service\OrderService;
use Kiener\MolliePayments\Service\SettingsService;
use Kiener\MolliePayments\Struct\Order\OrderAttributes;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Types\OrderStatus;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Api\Context\SalesChannelApiSource;
use Shopware\Core\System\StateMachine\Aggregation\StateMachineTransition\StateMachineTransitionActions;
use Shopware\Core\System\StateMachine\Event\StateMachineStateChangeEvent;
use Shopware\Storefront\Event\RouteRequest\CancelOrderRouteRequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CancelOrderSubscriber implements EventSubscriberInterface
{
    /**
     * These Shopware actions will automatically trigger
     * our cancellation (if enabled in the config).
     */
    public const AUTOMATIC_TRIGGER_ACTIONS = [
        StateMachineTransitionActions::ACTION_CANCEL,
    ];

    /**
     * Cancellations are only done for these Mollie states.
     */
    public const ALLOWED_CANCELLABLE_MOLLIE_STATES = [
        OrderStatus::STATUS_CREATED,
        OrderStatus::STATUS_AUTHORIZED,
        OrderStatus::STATUS_SHIPPING,
    ];

    /**
     * @var OrderService
     */
    private $orderService;

    /**
     * @var MollieApiFactory
     */
    private $apiFactory;

    /**
     * @var SettingsService
     */
    private $settingsService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(MollieApiFactory $apiFactory, OrderService $orderService, SettingsService $settingsService, LoggerInterface $loggerService)
    {
        $this->orderService = $orderService;
        $this->apiFactory = $apiFactory;
        $this->settingsService = $settingsService;
        $this->logger = $loggerService;
    }

    /**
     * @return array<mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'state_machine.order.state_changed' => ['onOrderStateChanges'],
            CancelOrderRouteRequestEvent::class => ['onAccountOrderCancelledRoute'],
        ];
    }

    public function onOrderStateChanges(StateMachineStateChangeEvent $event): void
    {
        if ($event->getTransitionSide() !== StateMachineStateChangeEvent::STATE_MACHINE_TRANSITION_SIDE_ENTER) {
            return;
        }

        $apiSource = $event->getContext()->getSource();

        if ($apiSource instanceof SalesChannelApiSource) {
            // do NOT cancel directly within the context of a Storefront
            // the user might retry the payment if the first one is cancelled
            // and we must never cancel the full order, because then he cannot retry the payment.
            return;
        }

        $transitionName = $event->getTransition()->getTransitionName();

        try {
            // if we don't have at least one of our
            // actions that automatically trigger this feature, continue
            if (! in_array($transitionName, self::AUTOMATIC_TRIGGER_ACTIONS, true)) {
                return;
            }

            // get order and extract our Mollie Order ID
            $order = $this->orderService->getOrder($event->getTransition()->getEntityId(), $event->getContext());

            // -----------------------------------------------------------------------------------------------------------------------

            $this->cancelOrder($order, 'state-machine');
        } catch (ApiException $e) {
            $this->logger->error(
                'Error when executing auto-cancellation of an order after transition: ' . $transitionName,
                [
                    'error' => $e,
                ]
            );
        }
    }

    /**
     * This function is called when the customer clicks on the "Cancel Order" button in the account/orders.
     * This means the customer wants to force cancelling the order.
     * If the automatic cancellation is enabled, we will cancel the order in Mollie too.
     */
    public function onAccountOrderCancelledRoute(CancelOrderRouteRequestEvent $event): void
    {
        try {
            $apiSource = $event->getContext()->getSource();

            if ($apiSource instanceof SalesChannelApiSource) {
                $request = $event->getStoreApiRequest();
                $scope = 'storefront';
            } else {
                $request = $event->getStorefrontRequest();
                $scope = 'store-api';
            }

            $orderId = $request->get('orderId');

            $order = $this->orderService->getOrder($orderId, $event->getContext());

            $this->cancelOrder($order, $scope);
        } catch (\Throwable $ex) {
            $this->logger->error(
                'Error when executing auto-cancellation of an order after CancelOrderRouteRequestEvent',
                [
                    'error' => $ex,
                ]
            );
        }
    }

    /**
     * @throws ApiException
     */
    private function cancelOrder(OrderEntity $order, string $scope): void
    {
        // check if we have activated this feature in our plugin configuration
        $settings = $this->settingsService->getSettings($order->getSalesChannelId());

        if (! $settings->isAutomaticCancellation()) {
            return;
        }

        $orderAttributes = new OrderAttributes($order);

        $mollieOrderId = $orderAttributes->getMollieOrderId();

        // if we don't have a Mollie Order ID continue
        // this can also happen for subscriptions where we only have a tr_xxx Transaction ID.
        // but cancellation only works on orders anyway
        if (empty($mollieOrderId)) {
            return;
        }

        $apiClient = $this->apiFactory->getClient($order->getSalesChannelId());

        $mollieOrder = $apiClient->orders->get($mollieOrderId);

        if (! in_array($mollieOrder->status, self::ALLOWED_CANCELLABLE_MOLLIE_STATES, true)) {
            $this->logger->debug('Skipping auto-cancellation of order: ' . $order->getOrderNumber() . ', ' . $mollieOrderId . '. Scope: ' . $scope);
            return;
        }

        $this->logger->debug('Starting auto-cancellation of order: ' . $order->getOrderNumber() . ', ' . $mollieOrderId . '. Scope: ' . $scope);

        $apiClient->orders->cancel($mollieOrderId);

        $this->logger->info('Auto-cancellation of order: ' . $order->getOrderNumber() . ', ' . $mollieOrderId . ' successfully executed. Scope: ' . $scope);
    }
}
