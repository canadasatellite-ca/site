<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Menu\Activity;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Actions
 */
class Actions implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'Ended', 'label' => __('Ended Listing')],
            ['value' => 'HandlingTime', 'label' => __('Handling Time Update')],
            ['value' => 'ListingCondition', 'label' => __('Listing Condition Update')],
            ['value' => 'Pricing', 'label' => __('Pricing Update')],
            ['value' => 'PublishListing', 'label' => __('Publish Listing')],
            ['value' => 'Eligibility', 'label' => __('Eligibility')],
            ['value' => 'SellerNotes', 'label' => __('Seller Notes Update')],
            ['value' => 'Quantity', 'label' => __('Quantity Update')]
        ];
    }
}
