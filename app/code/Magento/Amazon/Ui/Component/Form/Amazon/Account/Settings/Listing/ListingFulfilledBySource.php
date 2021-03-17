<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Account\Settings\Listing;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class ListingFulfilledBySource
 */
class ListingFulfilledBySource implements OptionSourceInterface
{
    /**
     * Get Magento product attribute options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Fulfilled By Merchant')],
            ['value' => 2, 'label' => __('Fulfilled By Amazon')],
            ['value' => 0, 'label' => __('Assign Fulfilled By Using Magento Product Attribute')]
        ];
    }
}
