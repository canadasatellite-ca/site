<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Account\Listing\Published;

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
            ['value' => Definitions::VALIDATE_ASIN_LIST_STATUS, 'label' => __('Validating ASIN')],
            ['value' => Definitions::READY_LIST_STATUS, 'label' => __('Ready To List')],
            ['value' => Definitions::LIST_IN_PROGRESS_LIST_STATUS, 'label' => __('List In Progress')],
            ['value' => Definitions::GENERAL_SEARCH_LIST_STATUS, 'label' => __('General Search Query')]
        ];
    }
}
