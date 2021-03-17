<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Source;

class ReasonForExport implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'GIF', 'label' => __('Gift')],
            ['value' => 'DOC', 'label' => __('Document')],
            ['value' => 'SAM', 'label' => __('Commercial sample')],
            ['value' => 'REP', 'label' => __('Repair or warranty')],
            ['value' => 'SOG', 'label' => __('Sale of goods')],
            ['value' => 'OTH', 'label' => __('Other')],
        ];
    }
}
