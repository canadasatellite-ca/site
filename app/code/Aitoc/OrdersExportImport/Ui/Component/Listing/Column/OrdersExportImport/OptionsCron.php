<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aitoc\OrdersExportImport\Ui\Component\Listing\Column\OrdersExportImport;

use Magento\Store\Ui\Component\Listing\Column\Store\Options as StoreOptions;

/**
 * Store Options for Cms Pages and Blocks
 */
class OptionsCron extends StoreOptions
{
    /**
     * All Store Views value
     */
    const MINUTES = 'minutes';
    const HOURS = 'hours';
    const HOUR = 'hour';
    const DAY = 'day';
    const DISABLE = 'Disable';

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }
        $options = [
            '0' => [
                'label' => __(self::DISABLE),
                'value' => 0
            ],
            '1' => [
                'label' => __('5 ' . self::MINUTES),
                'value' => 5
            ],
            '2' => [
                'label' => __('10 ' . self::MINUTES),
                'value' => 10
            ],
            '3' => [
                'label' => __('20 ' . self::MINUTES),
                'value' => 20
            ],
            '4' => [
                'label' => __('30 ' . self::MINUTES),
                'value' => 30
            ],
            '5' => [
                'label' => __('1 ' . self::HOUR),
                'value' => 60
            ],
            '6' => [
                'label' => __('2 ' . self::HOURS),
                'value' => 60 * 2
            ],
            '7' => [
                'label' => __('4 ' . self::HOURS),
                'value' => 60 * 4
            ],
            '8' => [
                'label' => __('8 ' . self::HOURS),
                'value' => 60 * 8
            ],
            '9' => [
                'label' => __('12 ' . self::HOURS),
                'value' => 60 * 12
            ],
            '10' => [
                'label' => __('1 ' . self::DAY),
                'value' => 60 * 24
            ],
        ];
        $this->options = $options;

        return $this->options;
    }
}
