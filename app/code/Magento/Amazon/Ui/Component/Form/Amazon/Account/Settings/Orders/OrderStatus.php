<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Account\Settings\Orders;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class OrderStatus
 */
class OrderStatus implements OptionSourceInterface
{
    /**
     * Get Magento product attribute options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Default Order Status')],
            ['value' => 1, 'label' => __('Custom Order Status')]
        ];
    }
}
