<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon\Order;

use Magento\Amazon\Api\Data\OrderTrackingInterface;
use Magento\Amazon\Api\OrderTrackingRepositoryInterface;
use Magento\Amazon\Model\ResourceModel\Amazon\Order\Tracking as ResourceModel;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class OrderTrackingRepository
 */
class OrderTrackingRepository implements OrderTrackingRepositoryInterface
{
    /** @var ResourceModel $resourceModel */
    protected $resourceModel;

    /**
     * @param ResourceModel $resourceModel
     */
    public function __construct(
        ResourceModel $resourceModel
    ) {
        $this->resourceModel = $resourceModel;
    }

    /**
     * {@inheritdoc}
     */
    public function save(OrderTrackingInterface $tracking)
    {
        try {
            // save tracking data
            $this->resourceModel->save($tracking);
        } catch (\Exception $e) {
            $phrase = __('Unable to save the tracking data. Please try again.');
            throw new CouldNotSaveException($phrase);
        }

        return $tracking;
    }
}
