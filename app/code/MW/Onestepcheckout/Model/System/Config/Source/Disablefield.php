<?php

namespace MW\Onestepcheckout\Model\System\Config\Source;

class Disablefield implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
            	'value' => 0,
            	'label' => __('Disable')
            ],
            [
            	'value' => 1,
            	'label' => __('Enable')
            ],
        ];
    }
}
