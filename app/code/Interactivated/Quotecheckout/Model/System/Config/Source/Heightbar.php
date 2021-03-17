<?php

namespace Interactivated\Quotecheckout\Model\System\Config\Source;

class Heightbar implements \Magento\Framework\Option\ArrayInterface
{
    const THIN = 1;
    const NORMAL  = 2;
    const BIGGER  = 3;
    const BIGGEST  = 4;

    public function toOptionArray()
    {
        return [
            [
                'value' => self::THIN,
                'label' => __('Thin')
            ],
            [
                'value' => self::NORMAL,
                'label' => __('Normal')
            ],
            [
                'value' => self::BIGGER,
                'label' => __('Bigger')
            ],
            [
                'value' => self::BIGGEST,
                'label' => __('Biggest')
            ]
        ];
    }
}
