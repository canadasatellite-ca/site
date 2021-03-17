<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Account\Settings\Orders;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory;

/**
 * Class OrderState
 */
class OrderState implements OptionSourceInterface
{
    /** @var CollectionFactory $collectionFactory */
    protected $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Returns option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var array */
        $data = [];
        /** @var CollectionFactory */
        $statusCollection = $this->collectionFactory->create();

        foreach ($statusCollection as $status) {
            if ($status->getStatus() == 'complete') {
                continue;
            }

            if ($status->getStatus() == 'canceled') {
                continue;
            }

            if ($status->getStatus() == 'closed') {
                continue;
            }

            $data[] = [
                'value' => $status->getStatus(),
                'label' => $status->getLabel()
            ];
        }

        return $data;
    }
}
