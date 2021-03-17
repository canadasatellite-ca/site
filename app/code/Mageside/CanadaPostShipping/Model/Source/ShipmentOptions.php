<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Source;

class ShipmentOptions implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('No')],
            ['value' => 'HFP', 'label' => __('Card for pickup')],
            ['value' => 'DNS', 'label' => __('Do not safe drop')],
            ['value' => 'LAD', 'label' => __('Leave at door - do not card')]
        ];
    }
}
