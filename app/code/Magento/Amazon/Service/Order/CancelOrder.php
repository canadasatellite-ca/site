<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Service\Order;

use Magento\Amazon\Api\Data\OrderInterface;
use Magento\Amazon\Api\OrderRepositoryInterface;
use Magento\Amazon\Domain\Command\CancelOrderFactory;
use Magento\Amazon\Domain\Command\CommandDispatcher;
use Magento\Amazon\GraphQl\RemoteApiException;
use Magento\Amazon\Model\Order\OrderHandlerResolver;
use Magento\Amazon\Model\ResourceModel\Amazon\Order\Item\Collection;
use Magento\Amazon\Model\ResourceModel\Amazon\Order\Item\CollectionFactory as OrderItemCollectionFactory;

class CancelOrder
{
    /** @var OrderRepositoryInterface $orderRepository */
    protected $orderRepository;
    /** @var OrderItemCollectionFactory $orderItemCollectionFactory */
    protected $orderItemCollectionFactory;
    /** @var OrderHandlerResolver */
    protected $orderHandlerResolver;
    /** @var CancelOrderFactory $cancelOrderFactory */
    private $cancelOrderFactory;
    /** @var CommandDispatcher $commandDispatcher */
    private $commandDispatcher;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderItemCollectionFactory $orderItemCollectionFactory
     * @param OrderHandlerResolver $orderHandlerResolver
     * @param CancelOrderFactory $cancelOrderFactory
     * @param CommandDispatcher $commandDispatcher
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderItemCollectionFactory $orderItemCollectionFactory,
        OrderHandlerResolver $orderHandlerResolver,
        CancelOrderFactory $cancelOrderFactory,
        CommandDispatcher $commandDispatcher
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->orderHandlerResolver = $orderHandlerResolver;
        $this->cancelOrderFactory = $cancelOrderFactory;
        $this->commandDispatcher = $commandDispatcher;
    }

    /**
     * @param string $orderId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws RemoteApiException
     */
    public function cancelOrder(string $orderId, string $reason): void
    {
        /** @var OrderInterface */
        $order = $this->orderRepository->getOrderByMarketplaceOrderId($orderId);
        $merchantId = (int)$order->getMerchantId();

        // cancel amazon order
        $orderHandler = $this->orderHandlerResolver->resolve();
        $orderHandler->cancel($orderId);

        // schedule api call to cancel order
        $isSuccessful = $this->cancelAmazonOrder($merchantId, $orderId, $reason);

        if (!$isSuccessful) {
            throw new RemoteApiException('Unable to cancel Amazon order');
        }
    }

    /**
     * Submits Amazon order cancellation to request to Amazon
     * and updates the order status to "Canceled"
     *
     * @param int $merchantId
     * @param int $orderId
     * @param string $reason
     * @return bool
     */
    private function cancelAmazonOrder($merchantId, $orderId, $reason)
    {
        /** @var Collection $collection */
        $collection = $this->orderItemCollectionFactory->create();
        $collection->addFieldToFilter('order_id', $orderId);

        $orderItemIds = [];
        foreach ($collection as $item) {
            $orderItemIds[] = $item->getOrderItemId();
        }

        if (empty($orderItemIds)) {
            return false;
        }

        $commandData = [
            'body' => [
                'order_id' => $orderId,
                'reason' => $reason,
                'order_item_ids' => $orderItemIds
            ],
            'identifier' => (string)$orderId
        ];

        $command = $this->cancelOrderFactory->create($commandData);
        $this->commandDispatcher->dispatch($merchantId, $command);

        return true;
    }
}
