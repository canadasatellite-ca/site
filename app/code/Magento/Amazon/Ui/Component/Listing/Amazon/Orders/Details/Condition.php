<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Orders\Details;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Condition
 */
class Condition implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'New', 'label' => __('New')],
            ['value' => 'Refurbished', 'label' => __('Refurbished')],
            ['value' => 'Used', 'label' => __('Used')]
        ];
    }
}
