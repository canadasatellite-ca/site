<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Orders;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 */
class Status implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'Pending', 'label' => __('Pending')],
            ['value' => 'Unshipped', 'label' => __('Unshipped')],
            ['value' => 'PartiallyShipped', 'label' => __('Partially Shipped')],
            ['value' => 'Shipped', 'label' => __('Shipped')],
            ['value' => 'Completed', 'label' => __('Completed')],
            ['value' => 'Canceled', 'label' => __('Canceled')],
            ['value' => 'Error', 'label' => __('Error')]
        ];
    }
}
