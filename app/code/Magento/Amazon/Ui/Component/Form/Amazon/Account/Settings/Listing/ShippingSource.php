<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Account\Settings\Listing;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class ShippingSource
 */
class ShippingSource implements OptionSourceInterface
{
    /**
     * Get Magento product attribute options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Free Economy (all items ship free)')],
            ['value' => 1, 'label' => __('Amazon Unit Rates')],
            ['value' => 2, 'label' => __('Amazon Shipping Templates')]
        ];
    }
}
