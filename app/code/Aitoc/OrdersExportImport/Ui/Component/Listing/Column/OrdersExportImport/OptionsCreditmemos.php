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
class OptionsCreditmemos extends StoreOptions
{
    /**
     * All Store Views value
     */
    const CREDIT_MEMOS = 'Credit Memos';
    const CREDIT_MEMO_COMMNETS = 'Credit Memo Comments';
    const CREDIT_MEMO_ITEMS = 'Credit Memo Items';
    
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
        $options  = [
            '0' => [
                'label' => __(self::CREDIT_MEMOS),
                'value' => 'creditmemos'
            ],
            '1' => [
                'label' => __(self::CREDIT_MEMO_COMMNETS),
                'value' => 'comments'
            ],
            '2' => [
                'label' => __(self::CREDIT_MEMO_ITEMS),
                'value' => 'items'
            ],
        ];
        $this->options = $options;

        return $this->options;
    }
}
