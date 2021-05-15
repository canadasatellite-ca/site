<?php

namespace Interactivated\Quotecheckout\Model\System\Config\Source;

class Pagelayout implements \Magento\Framework\Option\ArrayInterface
{
    function toOptionArray()
    {
        return [
            ['value' => 2, 'label' => __('2 Columns')],
            ['value' => 3, 'label' => __('3 Columns')]
        ];
    }
}
