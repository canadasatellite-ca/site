<?php

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon\UpdateHandler;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Amazon\Model\Order\OrderHandlerResolver;
use Magento\Amazon\Model\ResourceModel\Amazon\Order as OrderResourceModel;

class Order implements HandlerInterface
{
    /**
     * @var ChunkedHandler
     */
    private $chunkedHandler;
    /**
     * @var OrderHandlerResolver
     */
    private $orderHandlerResolver;
    /**
     * @var OrderResourceModel
     */
    private $orderResourceModel;

    public function __construct(
        ChunkedHandler $chunkedHandler,
        OrderHandlerResolver $orderHandlerResolver,
        OrderResourceModel $orderResourceModel
    ) {
        $this->chunkedHandler = $chunkedHandler;
        $this->orderHandlerResolver = $orderHandlerResolver;
        $this->orderResourceModel = $orderResourceModel;
    }

    public function handle(array $updates, AccountInterface $account): array
    {
        $orders = [];
        $merchantId = (int)$account->getMerchantId();
        foreach ($updates as $logId => $log) {
            if ($log['status'] === Definitions::CANCELED_ORDER_STATUS) {
                $orderHandler = $this->orderHandlerResolver->resolve();
                $orderHandler->cancel($log['order_id']);
            }
            $orders[$logId] = [
                'merchant_id' => $merchantId,
                'order_id' => $log['order_id'] ?? '',
                'status' => $log['status'] ?? '',
                'buyer_email' => $log['buyer_email'] ?? '',
                'ship_service_level' => $log['ship_service_level'] ?? '',
                'sales_channel' => $log['sales_channel'] ?? '',
                'shipped_by_amazon' => $log['shipped_by_amazon'] ?? 0,
                'is_business' => $log['is_business'] ?? 0,
                'items_shipped' => $log['items_shipped'] ?? 0,
                'items_unshipped' => $log['items_unshipped'] ?? 0,
                'buyer_name' => $log['buyer_name'] ?? '',
                'currency' => $log['currency'] ?? '',
                'total' => $log['total'] ?? 0.00,
                'is_premium' => $log['is_premium'] ?? 0,
                'is_prime' => $log['is_prime'] ?? 0,
                'is_replacement' => $log['is_replacement'] ?? 0,
                'fulfillment_channel' => $log['fulfillment_channel'] ?? '',
                'payment_method' => $log['payment_method'] ?? '',
                'service_level' => $log['service_level'] ?? '',
                'ship_name' => $log['ship_name'] ?? '',
                'ship_address_one' => $log['ship_address_one'] ?? '',
                'ship_address_two' => $log['ship_address_two'] ?? '',
                'ship_address_three' => $log['ship_address_three'] ?? '',
                'ship_city' => $log['ship_city'] ?? '',
                'ship_region' => $log['ship_region'] ?? '',
                'ship_postal_code' => $log['ship_postal_code'] ?? '',
                'ship_country' => $log['ship_country'] ?? '',
                'ship_phone' => $log['ship_phone'] ?? '',
                'purchase_date' => $log['purchase_date'] ?? '',
                'latest_ship_date' => $log['latest_ship_date'] ?? '',
            ];
        }
        return $this->chunkedHandler->handleUpdatesWithChunks(
            function ($chunkData): void {
                $this->orderResourceModel->insert($chunkData);
            },
            $orders,
            $account,
            'Cannot process logs with orders. Please report an error.'
        );
    }
}
