<?php

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon\UpdateHandler;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\ResourceModel\Amazon\Order\Item as OrderItemResourceModel;

class OrderItem implements HandlerInterface
{
    /**
     * @var ChunkedHandler
     */
    private $chunkedHandler;
    /**
     * @var OrderItemResourceModel
     */
    private $orderItemResourceModel;

    public function __construct(
        ChunkedHandler $chunkedHandler,
        OrderItemResourceModel $orderItemResourceModel
    ) {
        $this->chunkedHandler = $chunkedHandler;
        $this->orderItemResourceModel = $orderItemResourceModel;
    }

    public function handle(array $updates, AccountInterface $account): array
    {
        $merchantId = (int)$account->getMerchantId();
        $orderItems = [];
        foreach ($updates as $logId => $orderItem) {
            $orderItems[$logId] = [
                'merchant_id' => $merchantId,
                'order_id' => $orderItem['order_id'] ?? '',
                'order_item_id' => $orderItem['order_item_id'] ?? '',
                'qty_ordered' => $orderItem['qty_ordered'] ?? 0,
                'qty_shipped' => $orderItem['qty_shipped'] ?? 0,
                'title' => $orderItem['title'] ?? '',
                'sku' => $orderItem['sku'] ?? '',
                'asin' => $orderItem['asin'] ?? '',
                'condition' => $orderItem['condition'] ?? '',
                'subcondition' => $orderItem['subcondition'] ?? '',
                'item_price' => $orderItem['item_price'] ?? 0.00,
                'item_tax' => $orderItem['item_tax'] ?? 0.00,
                'shipping_price' => $orderItem['shipping_price'] ?? 0.00,
                'promotional_discount' => $orderItem['promotional_discount'] ?? 0.00,
            ];
        }
        return $this->chunkedHandler->handleUpdatesWithChunks(
            function ($chunkData): void {
                $this->orderItemResourceModel->insert($chunkData);
            },
            $orderItems,
            $account,
            'Cannot process logs with order items. Please report an error.'
        );
    }
}
