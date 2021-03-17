<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Account\Listing\Active;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class BuyBoxWonOption
 */
class BuyBoxWonOption implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('Yes')],
            ['value' => '0', 'label' => __('No')],
            ['value' => null, 'label' => __('NA')]
        ];
    }
}
