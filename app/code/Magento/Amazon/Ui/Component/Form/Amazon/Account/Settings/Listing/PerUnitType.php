<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Account\Settings\Listing;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class PerUnitType
 */
class PerUnitType implements OptionSourceInterface
{
    /**
     * Get Magento product attribute options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Calculated Per Item')],
            ['value' => 2, 'label' => __('Calculated Per Weight')]
        ];
    }
}
