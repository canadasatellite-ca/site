<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Account\Settings\Orders;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class IsSuper
 */
class IsSuper implements OptionSourceInterface
{
    /**
     * Get Magento product attribute options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Do Not Build Order')],
            ['value' => 1, 'label' => __('Build Order')]
        ];
    }
}
