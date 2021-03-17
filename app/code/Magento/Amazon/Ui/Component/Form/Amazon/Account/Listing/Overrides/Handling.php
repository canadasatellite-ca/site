<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Account\Listing\Overrides;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Handling
 */
class Handling implements OptionSourceInterface
{
    /**
     * Get Magento product attribute options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('No Change To Handling Time')],
            ['value' => 1, 'label' => __('Change Handling Time')],
            ['value' => 2, 'label' => __('Remove Handling Time Override (if exists)')]
        ];
    }
}
