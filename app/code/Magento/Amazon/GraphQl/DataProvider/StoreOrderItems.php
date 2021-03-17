<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\DataProvider;

use Magento\Amazon\GraphQl\Context;
use Magento\Amazon\Model\ResourceModel\Amazon\Order\Item\Collection;
use Magento\Framework\Exception\LocalizedException;

class StoreOrderItems
{
    /**
     * @var Collection
     */
    private $orderItemCollection;

    /**
     * @var array
     *
     */
    private $fieldsMap = [
        'id' => 'id',
        'merchantId' => 'merchant_id',
        'orderId' => 'order_id',
        'orderItemId' => 'order_item_id',
        'qtyOrdered' => 'qty_ordered',
        'qtyShipped' => 'qty_shipped',
        'title' => 'title',
        'sku' => 'sku',
        'asin' => 'asin',
        'condition' => 'condition',
        'subcondition' => 'subcondition',
        'itemPrice' => 'item_price',
        'itemTax' => 'item_tax',
        'shippingPrice' => 'shipping_price',
        'promotionalDiscount' => 'promotional_discount',
    ];

    /**
     * @var array
     */
    private $results;

    public function __construct(
        Collection $orderItemCollection
    ) {
        $this->orderItemCollection = $orderItemCollection;
    }

    public function getOrderItemsByOrderId(Context $context, string $orderId): array
    {
        if (!$this->results) {
            $orderIds = $context->storeOrderItems()->ids()->getAll();
            if (empty($orderIds)) {
                throw new LocalizedException(__('Order ids must be provided to resolve order items'));
            }
            $fields = $context->storeOrderItems()->fields()->getAll();
            $this->orderItemCollection->addFieldToFilter('order_id', ['in' => $orderIds]);
            $mappedFields = $this->mapFields($fields);
            $this->orderItemCollection->addFieldToSelect($mappedFields);
            $result = $this->orderItemCollection->getData();
            foreach ($result as $orderItem) {
                $this->results[$orderItem['order_id']][] = $orderItem;
            }
        }
        return isset($this->results[$orderId]) ? $this->results[$orderId] : [];
    }

    private function mapFields($fields): array
    {
        return array_merge(['order_id' => 'order_id'], array_intersect_key($this->fieldsMap, $fields));
    }
}
