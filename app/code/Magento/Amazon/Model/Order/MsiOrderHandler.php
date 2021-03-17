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
use Magento\Amazon\Api\Data\OrderItemInterface;
use Magento\Amazon\Api\ListingRepositoryInterface;
use Magento\Amazon\Api\OrderManagementInterface;
use Magento\Amazon\Api\OrderRepositoryInterface;
use Magento\Amazon\Cache\StoresWithOrdersThatCannotBeImported;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Amazon\Model\Amazon\Listing\ListingRuleRepository;
use Magento\Amazon\Model\ResourceModel\Amazon\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Amazon\Model\ResourceModel\Amazon\Order\Item\CollectionFactory as OrderItemCollectionFactory;
use Magento\Amazon\Msi\MsiApi;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class MsiOrderHandler extends AbstractOrderHandler
{
    /**
     * @var MsiApi
     */
    private $msiApi;
    /**
     * @var OrderItemCollectionFactory
     */
    private $orderItemCollectionFactory;
    /**
     * @var ListingRepositoryInterface
     */
    private $listingRepository;
    /**
     * @var WebsiteRepositoryInterface
     */
    private $websiteRepository;

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
        MsiApi $msiApi,
        OrderItemCollectionFactory $orderItemCollectionFactory,
        ListingRepositoryInterface $listingRepository,
        WebsiteRepositoryInterface $websiteRepository
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
        $this->msiApi = $msiApi;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->listingRepository = $listingRepository;
        $this->websiteRepository = $websiteRepository;
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
        $magentoOrder = $this->orderManagement->create($amazonOrder, $orderSetting, false);

        if ($magentoOrder) {
            $fulfillmentChannel = $magentoOrder->getFulfillmentChannel();
            if ($fulfillmentChannel === Definitions::ORDER_FULFILLED_BY_AMAZON) {
                $this->compensateReservation($magentoOrder, $orderSetting);
            } else {
                $magentoOrder->setReserved(true);
                try {
                    $magentoOrder = $this->orderRepository->save($magentoOrder);
                } catch (CouldNotSaveException $e) {
                    return false;
                }
            }
        }

        return $magentoOrder;
    }

    /**
     * Places compensating reservations to nullify reservations made by MSI for FBA orders
     *
     * @param OrderInterface $order
     * @param AccountOrderInterface $orderSetting
     */
    private function compensateReservation(OrderInterface $order, AccountOrderInterface $orderSetting)
    {
        $merchantId = (int)$order->getMerchantId();
        $orderId = $order->getOrderId();
        if (!$order->getSalesOrderId()) {
            return;
        }

        $magentoOrderId = (int)$order->getSalesOrderId();
        $orderItems = $this->orderItemCollectionFactory->create();
        $orderItems->addFieldToFilter('order_id', $orderId);

        /** @var OrderItemInterface[] $orderItems */
        if (!$orderItems) {
            return;
        }

        $websiteCode = $this->getWebsiteCode($orderSetting);
        if (empty($websiteCode)) {
            return;
        }

        $salesChannel = $this->msiApi->createSalesChannel($websiteCode);
        $salesEvent = $this->msiApi->createSalesEvent($magentoOrderId);

        $compensationItems = [];
        foreach ($orderItems as $orderItem) {
            $sellerSku = $orderItem->getSku();
            $sku = $this->listingRepository->getCatalogSkuBySellerSku($sellerSku, $merchantId);
            $compensationItems[] = $this->msiApi->createItemsToSell($sku, (float)$orderItem->getQtyOrdered());
        }

        try {
            $this->msiApi->placeReservationsForSalesEvent($compensationItems, $salesChannel, $salesEvent);
        } catch (\Exception $e) {
            return;
        }
    }

    /**
     * @param AccountOrderInterface $orderSetting
     * @return string
     */
    private function getWebsiteCode(AccountOrderInterface $orderSetting): string
    {
        $store = $this->orderManagement->getStoreForOrder($orderSetting);

        $websiteId = (int)$store->getWebsiteId();

        try {
            return $this->websiteRepository->getById($websiteId)->getCode();
        } catch (NoSuchEntityException $e) {
            return '';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function cancel(string $orderId)
    {
        /** @var \Magento\Amazon\Model\ResourceModel\Amazon\Order\Collection $collection */
        $collection = $this->orderCollectionFactory->create();
        /** @var OrderInterface[] $orders */
        $orders = $collection->addFieldToFilter('order_id', $orderId)->getItems();
        if (!$orders) {
            return;
        }

        foreach ($orders as $order) {
            $orderSetting = $this->accountOrderRepository->getByMerchantId($order->getMerchantId());

            $order->setStatus(Definitions::CANCELED_ORDER_STATUS);
            $order->setItemsUnshipped(0);
            $order->setNotes(__('Status updated to canceled.'));

            // release reserve (if applicable)
            if ($order->getReserved()) {
                $this->compensateReservation($order, $orderSetting);
                $order->setReserved(false);
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

    protected function shouldCompleteShipment(): bool
    {
        return false;
    }
}
