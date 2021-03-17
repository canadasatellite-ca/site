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
class OptionsExport extends StoreOptions
{
    /**
     * All Store Views value
     */
    const MTB = 'Manual export from order grid';
    const AAC = 'Automatically after checkout';
    const AOIFAPIAOAC = 'Automatically once invoices for all products in an order are created';
    const ABC = 'Automatically by cron';


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
                'label' => __(self::MTB),
                'value' => 0
            ],
            '1' => [
                'label' => __(self::AAC),
                'value' => 1
            ],
            '2' => [
                'label' => __(self::AOIFAPIAOAC),
                'value' => 2
            ],
            '3' => [
                'label' => __(self::ABC),
                'value' => 3
            ]
        ];
        $this->options = $options;

        return $this->options;
    }
}
