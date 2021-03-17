<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Source;

class NotificationOptions implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'on-shipment', 'label' => __('Upon shipment of the parcel')],
            ['value' => 'on-exception', 'label' => __('On exceptions')],
            ['value' => 'on-delivery', 'label' => __('Upon delivery of the parcel')]
        ];
    }
}
