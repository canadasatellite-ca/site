<?php

namespace Interactivated\Quotecheckout\Model\System\Config\Source;

class Corner implements \Magento\Framework\Option\ArrayInterface
{
    const ROUND_CORNER = 1;
    const NOT_CORNER  = 2;

    public function toOptionArray()
    {
        return [
            [
                'value' => self::ROUND_CORNER,
                'label' => __('Enable')
            ],
            [
                'value' => self::NOT_CORNER,
                'label' => __('Disable')
            ],
        ];
    }
}