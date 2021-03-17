<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Account\Listing;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class FulfilledBy
 */
class FulfilledBy implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'DEFAULT', 'label' => __('Merchant')],
            ['value' => 'AMAZON_NA', 'label' => __('Amazon North America')],
            ['value' => 'AMAZON_EU', 'label' => __('Amazon Europe')],
            ['value' => 'AMAZON_JP', 'label' => __('Amazon Japan')],
            ['value' => 'AMAZON_CN', 'label' => __('Amazon China')],
            ['value' => 'AMAZON_IN', 'label' => __('Amazon India')],
            ['value' => 'AMAZON_AU', 'label' => __('Amazon Australia')]
        ];
    }
}
