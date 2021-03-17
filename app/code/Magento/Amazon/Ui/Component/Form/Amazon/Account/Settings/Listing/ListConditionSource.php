<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Account\Settings\Listing;

use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class ListConditionSource
 */
class ListConditionSource implements OptionSourceInterface
{
    /**
     * Get Magento product attribute options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => Definitions::NEW_CONDITION_CODE, 'label' => __('New')],
            ['value' => Definitions::REFURBISHED_CONDITION_CODE, 'label' => __('Refurbished')],
            ['value' => Definitions::USEDLIKENEW_CONDITION_CODE, 'label' => __('Used; Like New')],
            ['value' => Definitions::USEDVERYGOOD_CONDITION_CODE, 'label' => __('Used; Very Good')],
            ['value' => Definitions::USEDGOOD_CONDITION_CODE, 'label' => __('Used; Good')],
            ['value' => Definitions::USEDACCEPTABLE_CONDITION_CODE, 'label' => __('Used; Acceptable')],
            ['value' => Definitions::COLLECTIBLELIKENEW_CONDITION_CODE, 'label' => __('Collectible; Like New')],
            ['value' => Definitions::COLLECTIBLEVERYGOOD_CONDITION_CODE, 'label' => __('Collectible; Very Good')],
            ['value' => Definitions::COLLECTIBLEGOOD_CONDITION_CODE, 'label' => __('Collectible; Good')],
            ['value' => Definitions::COLLECTIBLEACCEPTABLE_CONDITION_CODE, 'label' => __('Collectible; Acceptable')],
            ['value' => 0, 'label' => __('Assign Condition Using Product Attribute')]
        ];
    }
}
