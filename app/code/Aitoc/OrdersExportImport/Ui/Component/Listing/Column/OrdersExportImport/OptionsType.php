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
class OptionsType extends StoreOptions
{
    /**
     * All Store Views value
     */
    const XML = 'XML File';
    const CSV = 'CSV / Tab Separated';
    const ADVANCED_CSV = 'Advanced CSV / Tab Separated';


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
                'label' => __(self::XML),
                'value' => 0
            ],
            '1' => [
                'label' => __(self::CSV),
                'value' => 1
            ],
            '2' => [
                'label' => __(self::ADVANCED_CSV),
                'value' => 2
            ],
        ];
        $this->options = $options;

        return $this->options;
    }
}
