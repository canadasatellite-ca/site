<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model\Order;

use Magento\Amazon\Api\AccountOrderRepositoryInterface;
use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Api\Data\AccountOrderInterface;
use Magento\Amazon\Api\Data\OrderInterface;
use Magento\Amazon\Api\ListingRepositoryInterface;
use Magento\Amazon\Api\OrderManagementInterface;
use Magento\Amazon\Api\OrderRepositoryInterface;
use Magento\Amazon\Cache\StoresWithOrdersThatCannotBeImported;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Amazon\Model\Amazon\Listing\ListingRuleRepository;
use Magento\Amazon\Model\Amazon\Order;
use Magento\Amazon\Model\Amazon\Order\ReserveFactory;
use Magento\Amazon\Model\ResourceModel\Amazon\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Amazon\Model\ResourceModel\Amazon\Order\Item\CollectionFactory as OrderItemCollectionFactory;
use Magento\Amazon\Model\ResourceModel\Amazon\Order\Reserve;
use Magento\Amazon\Model\ResourceModel\Amazon\Order\Reserve\CollectionFactory as ReserveCollectionFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\StoreManagerInterface;

class LegacyOrderHandler extends AbstractOrderHandler
{
    /**
     * @var OrderItemCollectionFactory
     */
    private $orderItemCollectionFactory;
    /**
     * @var ListingRepositoryInterface
     */
    private $listingRepository;
    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;
    /**
     * @var ReserveFactory
     */
    private $reserveFactory;
    /**
     * @var Reserve
     */
    private $reserveResourceModel;
    /**
     * @var ReserveCollectionFactory
     */
    private $reserveCollectionFactory;

    public function __construct(
        AscClientLogger $logger,
        StoreManagerInterface $storeManager,
        ListingRuleRepository $listingRuleRepository,
        StoresWithOrdersThatCannotBeImported $storesWithOrdersThatCannotBeImported,
        AccountRepositoryInterface $accountRepository,
        AccountOrderRepositoryInterface $accountOrderRepository,
        OrderCollectionFactory $orderCollectionFactory,
        OrderRepositoryInterface $orderRepository,
        OrderManagementInterface $orderManagement,
        OrderFactory $salesOrderFactory,
        OrderItemCollectionFactory $orderItemCollectionFactory,
        ListingRepositoryInterface $listingRepository,
        StockRegistryInterface $stockRegistry,
        ReserveFactory $reserveFactory,
        Reserve $reserveResourceModel,
        ReserveCollectionFactory $reserveCollectionFactory
    ) {
        parent::__construct(
            $logger,
            $storeManager,
            $listingRuleRepository,
            $storesWithOrdersThatCannotBeImported,
            $accountRepository,
            $accountOrderRepository,
            $orderCollectionFactory,
            $orderRepository,
            $orderManagement,
            $salesOrderFactory
        );
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->listingRepository = $listingRepository;
        $this->stockRegistry = $stockRegistry;
        $this->reserveFactory = $reserveFactory;
        $this->reserveResourceModel = $reserveResourceModel;
        $this->reserveCollectionFactory = $reserveCollectionFactory;
    }

    /**
     * @param Order $order
     * @param AccountOrderInterface $orderSetting
     */
    protected function handlePreconditions(Order $order, AccountOrderInterface $orderSetting): void
    {
        $status = $order->getStatus();
        $reserved = $order->getReserved();
        $fulfillmentChannel = $order->getFulfillmentChannel();

        if ($orderSetting->getReserve()
            && $status === Definitions::PENDING_ORDER_STATUS
            && Definitions::ORDER_FULFILLED_BY_MERCHANT === $fulfillmentChannel
            && !$reserved
        ) {
            $this->buildStockReserve($order);
        }
    }

    protected function getAllowedStatuses(): array
    {
        return array_merge(parent::getAllowedStatuses(), [Definitions::PENDING_ORDER_STATUS]);
    }

    protected function canBuildOrder(Order $order): bool
    {
        return !in_array(
            $order->getStatus(),
            [
                Definitions::PENDING_ORDER_STATUS,
                Definitions::PENDING_AVAILABILITY_ORDER_STATUS
            ],
            true
        );
    }

    /**
     * Create magento order for order imported from Amazon
     *
     * @param OrderInterface $amazonOrder
     * @param AccountOrderInterface $orderSetting
     * @return bool|OrderInterface
     */
    public function createMagentoOrder(OrderInterface $amazonOrder, AccountOrderInterface $orderSetting)
    {
        $magentoOrder = $this->orderManagement->create($amazonOrder, $orderSetting, true);

        if ($magentoOrder) {
            $fulfillmentChannel = $magentoOrder->getFulfillmentChannel();
            if (($fulfillmentChannel !== Definitions::ORDER_FULFILLED_BY_AMAZON) && !$magentoOrder->getReserved()) {
                // reserve inventory (if not reserved)
                $this->buildStockReserve($magentoOrder);
            }
        } else {
            return false;
        }

        return $magentoOrder;
    }

    /**
     * Builds a stock level reserve for pending orders (if enabled)
     *
     * @param OrderInterface $order
     * @return void
     */
    private function buildStockReserve(OrderInterface $order)
    {
        $orderId = $order->getOrderId();
        $orderItems = $this->orderItemCollectionFactory->create();
        $orderItems->addFieldToFilter('order_id', $orderId);

        if (!$orderItems) {
            return;
        }

        foreach ($orderItems as $orderItem) {
            $merchantId = $orderItem->getMerchantId();
            $sellerSku = $orderItem->getSku();
            $sku = $this->listingRepository->getCatalogSkuBySellerSku($sellerSku, $merchantId);

            try {
                $stockItem = $this->stockRegistry->getStockItemBySku($sku);
            } catch (NoSuchEntityException $e) {
                $notes = __(
                    'Unable to reserve inventory as we cannot find the product "%1" in the catalog',
                    [$sku]
                )->render();
                $this->setOrderNotesForPending($order, $notes);
                continue;
            }

            // if quantity available to reserve
            if ($stockItem->getQty() >= $orderItem->getQtyOrdered()) {
                $stockItem->setQty($stockItem->getQty() - $orderItem->getQtyOrdered());

                try {
                    $this->stockRegistry->updateStockItemBySku($sku, $stockItem);
                } catch (NoSuchEntityException $e) {
                    continue;
                } catch (CouldNotSaveException $e) {
                    continue;
                }

                $reserveData = [
                    'merchant_id' => $merchantId,
                    'order_id' => $orderItem->getOrderId(),
                    'order_item_id' => $orderItem->getOrderItemId(),
                    'qty' => $orderItem->getQtyOrdered(),
                    'title' => $orderItem->getTitle(),
                    'sku' => $sku,
                    'status' => 'Active'
                ];

                // record reserve
                $reserve = $this->reserveFactory->create();
                $reserve->addData($reserveData);

                try {
                    $this->reserveResourceModel->save($reserve);
                } catch (\Exception $e) {
                    $notes = 'Unable to reserve inventory.';
                    $this->setOrderNotesForPending($order, $notes);
                    return;
                }

                // update order to reserved
                $order->setReserved(true);

                try {
                    $this->orderRepository->save($order);
                } catch (CouldNotSaveException $e) {
                    return;
                }

                if ($order->getStatus() === Definitions::PENDING_ORDER_STATUS) {
                    $notes = 'Inventory reserved';
                    $this->orderManagement->setOrderNotes($order, $notes);
                }
            } else {
                $notes = 'Unable to reserve inventory as the quantity is not available.';
                $this->setOrderNotesForPending($order, $notes);
            }
        }
    }

    /**
     * Sets order notes if in pending status
     *
     * @param OrderInterface $order
     * @param string $notes
     * @return void
     */
    private function setOrderNotesForPending(OrderInterface $order, string $notes)
    {
        if ($order->getStatus() == Definitions::PENDING_ORDER_STATUS) {
            $order->setNotes(__($notes));

            try {
                $this->orderRepository->save($order);
            } catch (CouldNotSaveException $e) {
                return;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function cancel(string $orderId)
    {
        /** @var OrderInterface[] $collection */
        $collection = $this->orderCollectionFactory->create()->addFieldToFilter('order_id', $orderId);
        if (!$collection) {
            return;
        }

        foreach ($collection as $order) {
            $order->setStatus(Definitions::CANCELED_ORDER_STATUS);
            $order->setItemsUnshipped(0);
            $order->setNotes(__('Status updated to canceled.'));

            // release reserve (if applicable)
            if ($order->getReserved()) {
                $this->removeStockReserve($order);
            }

            try {
                $this->orderRepository->save($order);
            } catch (CouldNotSaveException $e) {
                continue;
            }

            $salesOrderId = $order->getSalesOrderId();
            if ($salesOrderId) {
                $this->orderManagement->refundOrder((int)$salesOrderId);
            }
        }
    }

    /**
     * Removes Amazon stock reserve
     *
     * @param OrderInterface $order
     * @return void
     */
    private function removeStockReserve(OrderInterface $order)
    {
        $order->setReserved(false);

        try {
            $this->orderRepository->save($order);
        } catch (CouldNotSaveException $e) {
            return;
        }

        /** @var ReserveCollectionFactory */
        $collection = $this->reserveCollectionFactory->create();
        $collection->addFieldToFilter('order_id', $order->getOrderId());

        foreach ($collection as $item) {
            try {
                $stockItem = $this->stockRegistry->getStockItemBySku($item->getSku());
            } catch (NoSuchEntityException $e) {
                $stockItem = null;
            }
            if ($stockItem) {
                $stockItem->setQty($stockItem->getQty() + $item->getQty());
                $stockItem->setIsInStock(true);

                try {
                    $this->stockRegistry->updateStockItemBySku($item->getSku(), $stockItem);
                } catch (NoSuchEntityException $e) {
                    $notes = 'Failed to release the reserved stock.';
                    $this->orderManagement->setOrderNotes($order, $notes);
                }
            }

            try {
                $item->delete();
            } catch (\Exception $e) {
                // already deleted
            }
        }
    }

    protected function shouldCompleteShipment(): bool
    {
        return true;
    }
}
