<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Source;

class HandlingType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => \Mageside\CanadaPostShipping\Model\Carrier::HANDLING_TYPE_FIXED,
                'label' => __('Fixed'),
            ],
            [
                'value' => \Mageside\CanadaPostShipping\Model\Carrier::HANDLING_TYPE_PERCENT,
                'label' => __('Percent')
            ],
            [
                'value' => \Mageside\CanadaPostShipping\Model\Carrier::HANDLING_TYPE_FIXED_PERCENT,
                'label' => __('Fixed and Percent')
            ]
        ];
    }
}
