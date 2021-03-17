<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Account\Listing\Active;

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
            ['value' => Definitions::REMOVE_IN_PROGRESS_LIST_STATUS, 'label' => __('Ended Listing In Progress')],
            ['value' => Definitions::CONDITION_OVERRIDE_LIST_STATUS, 'label' => __('Relist In Progress')],
            ['value' => Definitions::ACTIVE_LIST_STATUS, 'label' => __('Active')]
        ];
    }
}
