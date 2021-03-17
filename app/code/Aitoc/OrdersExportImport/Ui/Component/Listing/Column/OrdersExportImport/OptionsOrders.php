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
class OptionsOrders extends StoreOptions
{
    /**
     * All Store Views value
     */
    const ORDER_ITEMS = 'Order Items';
    const ORDER_ADDRESSES = 'Order Addresses';
    const ORDER_PAYMENTS = 'Order Payments';
    const ORDER_PAYMENT_TRANSACTIONS = 'Order Payment Transactions';
    const ORDER_STATUS_HISTORY = 'Order Status History';
    
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
                'label' => __(self::ORDER_ITEMS),
                'value' => 'items'
            ],
            '1' => [
                'label' => __(self::ORDER_ADDRESSES),
                'value' => 'addresses'
            ],
            '2' => [
                'label' => __(self::ORDER_PAYMENTS),
                'value' => 'payments'
            ],
            '3' => [
                'label' => __(self::ORDER_PAYMENT_TRANSACTIONS),
                'value' => 'paymentstransaction'
            ],
            '4' => [
                'label' => __(self::ORDER_STATUS_HISTORY),
                'value' => 'statuseshistory'
            ],
        ];
        $this->options = $options;

        return $this->options;
    }
}
