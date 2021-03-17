<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Source;

class PrintOutputFormat implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => '4x6', 'label' => __('4x6 thermal')],
            ['value' => '8.5x11', 'label' => __('8.5x11 paper')],
        ];
    }
}
