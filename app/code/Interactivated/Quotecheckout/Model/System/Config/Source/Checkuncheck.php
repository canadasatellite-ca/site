<?php

namespace Interactivated\Quotecheckout\Model\System\Config\Source;

class Checkuncheck implements \Magento\Framework\Option\ArrayInterface
{
    function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Unchecked')],
            ['value' => 1, 'label' => __('Checked')]
        ];
    }
}
