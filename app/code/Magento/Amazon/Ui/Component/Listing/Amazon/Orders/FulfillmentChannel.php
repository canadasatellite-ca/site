<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Orders;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class FulfillmentChannel
 */
class FulfillmentChannel implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var array */
        $data = [];

        array_push($data, ['value' => 'MFN', 'label' => __('Merchant')]);
        array_push($data, ['value' => 'AFN', 'label' => __('Amazon')]);

        return $data;
    }
}
