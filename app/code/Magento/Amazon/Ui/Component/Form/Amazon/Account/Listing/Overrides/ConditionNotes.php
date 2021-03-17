<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Account\Listing\Overrides;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class ConditionNotes
 */
class ConditionNotes implements OptionSourceInterface
{
    /**
     * Get Magento product attribute options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('No Change To Seller Notes')],
            ['value' => 1, 'label' => __('Change Seller Notes')],
            ['value' => 2, 'label' => __('Remove Seller Notes')]
        ];
    }
}
