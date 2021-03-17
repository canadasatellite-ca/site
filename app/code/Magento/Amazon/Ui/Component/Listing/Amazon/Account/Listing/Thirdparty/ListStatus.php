<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Account\Listing\Thirdparty;

use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class ListStatus
 */
class ListStatus implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => Definitions::THIRDPARTY_LIST_STATUS, 'label' => __('Awaiting Product Assignment')]
        ];
    }
}
