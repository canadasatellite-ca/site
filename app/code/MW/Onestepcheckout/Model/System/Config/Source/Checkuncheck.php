<?php

namespace MW\Onestepcheckout\Model\System\Config\Source;

class Checkuncheck implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Unchecked')],
            ['value' => 1, 'label' => __('Checked')]
        ];
    }
}
