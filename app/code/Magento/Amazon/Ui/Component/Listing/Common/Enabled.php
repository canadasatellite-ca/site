<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Common;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Enabled
 */
class Enabled implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => __('Disabled')],
            ['value' => '1', 'label' => __('Enabled')]
        ];
    }
}
