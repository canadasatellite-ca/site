<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Source;

class DeliveryInstructions implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('No')],
            ['value' => 'PA18', 'label' => __('Proof of age required - 18')],
            ['value' => 'PA19', 'label' => __('Proof of age required - 19')],
        ];
    }
}
