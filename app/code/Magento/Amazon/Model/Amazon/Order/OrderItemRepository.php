<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon\Order;

use Magento\Amazon\Api\OrderItemRepositoryInterface;
use Magento\Amazon\Model\Amazon\Order\ItemFactory as OrderItemFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class OrderItemRepository
 */
class OrderItemRepository implements OrderItemRepositoryInterface
{
    /** @var OrderItemFactory $orderItemFactory */
    protected $orderItemFactory;

    /**
     * @param OrderItemFactory $orderItemFactory
     */
    public function __construct(
        OrderItemFactory $orderItemFactory
    ) {
        $this->orderItemFactory = $orderItemFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id)
    {
        /** @var OrderItemFactory $orderItem */
        $orderItem = $this->orderItemFactory->create();

        $orderItem->load($id);

        if (!$orderItem->getId()) {
            // if return empty is not set
            $phrase = __('The requested order item does not exist.');
            throw new NoSuchEntityException($phrase);
        }

        return $orderItem;
    }
}
