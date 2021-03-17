<?php

namespace MW\Onestepcheckout\Model\System\Config\Source;

class Pagelayout implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 2, 'label' => __('2 Columns')],
            ['value' => 3, 'label' => __('3 Columns')]
        ];
    }
}
