<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\DataProvider;

use Magento\Amazon\GraphQl\Context;
use Magento\Amazon\Model\ResourceModel\Amazon\Order\Tracking\Collection;

class StoreOrderTracking
{
    /**
     * @var Collection
     */
    private $orderTrackingCollection;

    /**
     * @var array
     *
     */
    private $fieldsMap = [
        'id' => 'id',
        'merchantId' => 'merchant_id',
        'orderId' => 'order_id',
        'orderItemId' => 'order_item_id',
        'carrierType' => 'carrier_type',
        'carrierName' => 'carrier_name',
        'shippingMethod' => 'shipping_method',
        'trackingNumber' => 'tracking_number',
        'quantity' => 'quantity'
    ];

    /**
     * @var array
     */
    private $results;

    public function __construct(
        Collection $orderTrackingCollection
    ) {
        $this->orderTrackingCollection = $orderTrackingCollection;
    }

    public function getOrderTrackingByOrderId(Context $context, string $orderId): array
    {
        if (!$this->results) {
            $orderIds = $context->storeOrderTracking()->ids()->getAll();
            $fields = $context->storeOrderTracking()->fields()->getAll();
            $this->orderTrackingCollection->addFieldToFilter('order_id', ['in' => $orderIds]);
            $mappedFields = $this->mapFields($fields);
            $this->orderTrackingCollection->addFieldToSelect($mappedFields);
            $result = $this->orderTrackingCollection->getData();
            foreach ($result as $orderTracking) {
                $this->results[$orderTracking['order_id']][] = $orderTracking;
            }
        }
        return isset($this->results[$orderId]) ? $this->results[$orderId] : [];
    }

    private function mapFields($fields): array
    {
        return array_merge(['order_id' => 'order_id'], array_intersect_key($this->fieldsMap, $fields));
    }
}
