<?php

namespace Interactivated\Quotecheckout\Model\System\Config\Source;

class Formatdate implements \Magento\Framework\Option\ArrayInterface
{
    function toOptionArray()
    {
        return [
            ['value' => 'm/d/Y', 'label' => __('mm/dd/yyyy')],
            ['value' => 'd/m/Y', 'label' => __('dd/mm/yyyy')],
        ];
    }
}
