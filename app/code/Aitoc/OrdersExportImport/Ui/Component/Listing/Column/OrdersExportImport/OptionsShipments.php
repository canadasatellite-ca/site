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
class OptionsShipments extends StoreOptions
{
    /**
     * All Store Views value
     */
    const SHIPMENTS = 'Shipments';
    const SHIPMENT_COMMENTS = 'Shipment Comments';
    const SHIPMENT_ITEMS = 'Shipped Items';
    const SHIPMENT_TRACKING_INFORMATION = 'Shipments Tracking Information';
    
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
                'label' => __(self::SHIPMENTS),
                'value' => 'shipments'
            ],
            '1' => [
                'label' => __(self::SHIPMENT_COMMENTS),
                'value' => 'comments'
            ],
            '2' => [
                'label' => __(self::SHIPMENT_ITEMS),
                'value' => 'items'
            ],
            '3' => [
                'label' => __(self::SHIPMENT_TRACKING_INFORMATION),
                'value' => 'trackingsinformation'
            ],
        ];
        $this->options = $options;

        return $this->options;
    }
}
