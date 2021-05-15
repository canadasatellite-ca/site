<?php

namespace Interactivated\Quotecheckout\Model\System\Config\Source;

class Disablefield implements \Magento\Framework\Option\ArrayInterface
{
    function toOptionArray()
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
