<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Source;

class NonDeliveryHandling implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'RASE', 'label' => __('Return at Sender’s Expense')],
            ['value' => 'RTS', 'label' => __('Return to Sender')],
            ['value' => 'ABAN', 'label' => __('Abandon')]
        ];
    }
}
