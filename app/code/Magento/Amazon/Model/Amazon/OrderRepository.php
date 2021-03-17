<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon;

use Magento\Amazon\Api\Data\OrderInterface;
use Magento\Amazon\Api\OrderRepositoryInterface;
use Magento\Amazon\Model\ResourceModel\Amazon\Order as ResourceModel;
use Magento\Amazon\Model\ResourceModel\Amazon\Order\CollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class OrderRepository
 */
class OrderRepository implements OrderRepositoryInterface
{
    /** @var OrderFactory $orderFactory */
    protected $orderFactory;
    /** @var ResourceModel $resourceModel */
    protected $resourceModel;
    /** @var CollectionFactory $collectionFactory */
    protected $collectionFactory;

    /**
     * @param OrderFactory $orderFactory
     * @param ResourceModel $resourceModel
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        OrderFactory $orderFactory,
        ResourceModel $resourceModel,
        CollectionFactory $collectionFactory
    ) {
        $this->orderFactory = $orderFactory;
        $this->resourceModel = $resourceModel;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(OrderInterface $order)
    {
        try {
            // save amazon order
            $this->resourceModel->save($order);
        } catch (\Exception $e) {
            $phrase = __('Unable to save amazon order. Please try again.');
            throw new CouldNotSaveException($phrase);
        }

        return $order;
    }

    /**
     * {@inheritdoc}
     */
    public function getByOrderId($orderId)
    {
        /** @var OrderFactory $order */
        $order = $this->orderFactory->create();

        $order->load($orderId);

        if (!$order->getMerchantId()) {
            // if return empty is not set
            $phrase = __('The requested order does not exist.');
            throw new NoSuchEntityException($phrase);
        }

        return $order;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderBySalesOrderId($orderId)
    {
        /** @var OrderInterface */
        if ($order = $this->collectionFactory->create()->addFieldToFilter('sales_order_id', $orderId)->getFirstItem()) {
            if ($order->getId()) {
                return $order;
            }
        }

        $phrase = __('The requested order does not exist.');
        throw new NoSuchEntityException($phrase);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderByMarketplaceOrderId(string $orderId): OrderInterface
    {
        /** @var OrderInterface */
        if ($order = $this->collectionFactory->create()->addFieldToFilter('order_id', $orderId)->getFirstItem()) {
            if ($order->getId()) {
                return $order;
            }
        }

        $phrase = __('The requested order does not exist.');
        throw new NoSuchEntityException($phrase);
    }
}
