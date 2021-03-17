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
class OptionsImportBehavior extends StoreOptions
{
    /**
     * All Store Views value
     */
    const APPEND_COMPLEX_DATA = 'Append Complex Data';
    const REPLACE_CURRENT_DATA = 'Replace Existing Complex Data';
    const DELETE_ENTITIES = 'Delete Entities';

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
                'label' => __(self::APPEND_COMPLEX_DATA),
                'value' => 0
            ],
            '1' => [
                'label' => __(self::REPLACE_CURRENT_DATA),
                'value' => 1
            ],
            '2' => [
                'label' => __(self::DELETE_ENTITIES),
                'value' => 2
            ],
        ];
        $this->options = $options;

        return $this->options;
    }
}
