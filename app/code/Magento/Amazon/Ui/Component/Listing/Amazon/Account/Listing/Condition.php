<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Account\Listing;

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
            ['value' => '0', 'label' => __('Not Selected')],
            ['value' => '11', 'label' => __('New')],
            ['value' => '10', 'label' => __('Refurbished')],
            ['value' => '1', 'label' => __('Used; Like New')],
            ['value' => '2', 'label' => __('Used; Very Good')],
            ['value' => '3', 'label' => __('Used; Good')],
            ['value' => '4', 'label' => __('Used; Acceptable')],
            ['value' => '5', 'label' => __('Collectible; Like New')],
            ['value' => '6', 'label' => __('Collectible; Very Good')],
            ['value' => '7', 'label' => __('Collectible; Good')],
            ['value' => '8', 'label' => __('Collectible; Acceptable')]
        ];
    }
}
