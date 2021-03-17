<?php

namespace Interactivated\Quotecheckout\Model\System\Config\Source;

class Style implements \Magento\Framework\Option\ArrayInterface
{
    const STYLE_FLAT  = 2;
    const STYLE_CLASSIC = 3;

    public function toOptionArray()
    {
        return [
            [
                'value' => self::STYLE_FLAT,
                'label' => __('Flat')
            ],
            [
                'value' => self::STYLE_CLASSIC,
                'label' => __('Classic')
            ],
        ];
    }
}