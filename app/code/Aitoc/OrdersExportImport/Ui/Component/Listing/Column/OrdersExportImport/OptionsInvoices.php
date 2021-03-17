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
class OptionsInvoices extends StoreOptions
{
    /**
     * All Store Views value
     */
    const INVOICES = 'Invoices';
    const INVOICE_COMMNETS = 'Invoice Comments';
    const INVOICE_ITEMS = 'Invoice Items';
    
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
                'label' => __(self::INVOICES),
                'value' => 'invoices'
            ],
            '1' => [
                'label' => __(self::INVOICE_COMMNETS),
                'value' => 'comments'
            ],
            '2' => [
                'label' => __(self::INVOICE_ITEMS),
                'value' => 'items'
            ],
        ];
        $this->options = $options;

        return $this->options;
    }
}
