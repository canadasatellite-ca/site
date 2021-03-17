<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Account\Listing\Overrides;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Condition
 */
class Condition implements OptionSourceInterface
{
    /**
     * Get Magento product attribute options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('No Change To Condition')],
            ['value' => 1, 'label' => __('Change Condition')],
            ['value' => 2, 'label' => __('Remove Condition Override')]
        ];
    }
}
