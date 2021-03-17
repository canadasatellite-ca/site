<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Account\Listing\Incomplete;

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
            ['value' => Definitions::MISSING_CONDITION_LIST_STATUS, 'label' => __('Missing Condition')],
            ['value' => Definitions::NOMATCH_LIST_STATUS, 'label' => __('Unable To Assign To Amazon Listing')],
            ['value' => Definitions::MULTIPLE_LIST_STATUS, 'label' => __('Multiple Matches Found')],
            ['value' => Definitions::VARIANTS_LIST_STATUS, 'label' => __('Has Variants')]
        ];
    }
}
