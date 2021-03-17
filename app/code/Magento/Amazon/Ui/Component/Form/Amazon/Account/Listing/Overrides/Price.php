<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Account\Listing\Overrides;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Price
 */
class Price implements OptionSourceInterface
{
    /**
     * Get Magento product attribute options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('No Change To Listing Price')],
            ['value' => 1, 'label' => __('Change Listing Price')],
            ['value' => 2, 'label' => __('Remove Listing Price Override')]
        ];
    }
}
