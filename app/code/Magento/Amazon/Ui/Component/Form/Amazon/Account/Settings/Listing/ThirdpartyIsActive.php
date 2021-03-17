<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Account\Settings\Listing;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class ThirdpartyIsActive
 */
class ThirdpartyIsActive implements OptionSourceInterface
{
    /**
     * Get Magento product attribute options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Do Not Import Listing')],
            ['value' => 1, 'label' => __('Import Listing')]
        ];
    }
}
