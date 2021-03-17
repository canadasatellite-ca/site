<?php

declare(strict_types=1);

namespace Magento\Amazon\Model\Order;

use Magento\Amazon\Api\AccountOrderRepositoryInterface;
use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Api\Data\AccountOrderInterface;
use Magento\Amazon\Api\OrderManagementInterface;
use Magento\Amazon\Api\OrderRepositoryInterface;
use Magento\Amazon\Cache\StoresWithOrdersThatCannotBeImported;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Amazon\Model\Amazon\Listing\ListingRuleRepository;
use Magento\Amazon\Model\Amazon\Order;
use Magento\Amazon\Model\ResourceModel\Amazon\Order\Collection as OrderCollection;
use Magento\Amazon\Model\ResourceModel\Amazon\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\StoreManagerInterface;

abstract class AbstractOrderHandler implements OrderHandlerInterface
{
    /**
     * @var AscClientLogger
     */
    protected $logger;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var ListingRuleRepository
     */
    protected $listingRuleRepository;
    /**
     * @var StoresWithOrdersThatCannotBeImported
     */
    protected $storesWithOrdersThatCannotBeImported;
    /**
     * @var AccountRepositoryInterface
     */
    protected $accountRepository;
    /**
     * @var AccountOrderRepositoryInterface
     */
    protected $accountOrderRepository;
    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;
    /**
     * @var OrderManagementInterface
     */
    protected $orderManagement;
    /**
     * @var OrderFactory
     */
    protected $salesOrderFactory;

    /**
     * @param AscClientLogger $logger
     * @param StoreManagerInterface $storeManager
     * @param ListingRuleRepository $listingRuleRepository
     * @param StoresWithOrdersThatCannotBeImported $storesWithOrdersThatCannotBeImported
     * @param AccountRepositoryInterface $accountRepository
     * @param AccountOrderRepositoryInterface $accountOrderRepository
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderManagementInterface $orderManagement
     * @param OrderFactory $salesOrderFactory
     */
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
        OrderFactory $salesOrderFactory
    ) {
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->listingRuleRepository = $listingRuleRepository;
        $this->storesWithOrdersThatCannotBeImported = $storesWithOrdersThatCannotBeImported;
        $this->accountRepository = $accountRepository;
        $this->accountOrderRepository = $accountOrderRepository;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderRepository = $orderRepository;
        $this->orderManagement = $orderManagement;
        $this->salesOrderFactory = $salesOrderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function synchronizeOrders(int $merchantId)
    {
        $account = $this->accountRepository->getByMerchantId($merchantId);
        $this->synchronizeOrdersForMerchant($account);
    }

    public function synchronizeOrdersForMerchant(AccountInterface $account): void
    {
        $merchantId = (int)$account->getMerchantId();
        $defaultStoreId = $this->storeManager->getStore()->getId();

        $this->updateWebsiteContextForMerchant($merchantId);
        $orderSetting = $this->accountOrderRepository->getByMerchantId($merchantId);

        if (!$orderSetting->getOrderIsActive()) {
            return;
        }

        $ordersCollection = $this->getOrdersCollection($merchantId);

        /** @var Order[] $orders */
        $orders = $ordersCollection->getItems();
        foreach ($orders as $order) {
            try {
                // because Magento thinks that an object has changes right after load
                $order->setHasDataChanges(false);

                // doing these dances with hashes to avoid extra upserts into database
                $originalDataHash = $this->getOrderHash($order);

                $this->handlePreconditions($order, $orderSetting);

                if (!$this->canBuildOrder($order)) {
                    continue;
                }

                $order->setNotes(__('Status updated to ' . $order->getStatus())->render());
                $this->handleMagentoOrderCreation($order, $orderSetting, $account);
                $order->setHasDataChanges($this->getOrderHash($order) !== $originalDataHash && !$order->isDeleted());

                try {
                    if ($order->hasDataChanges()) {
                        $order = $this->orderRepository->save($order);
                    }
                } catch (CouldNotSaveException $exception) {
                    $this->logger->warning(
                        'Could not save order',
                        [
                            'amazon_order' => $order->getOrderId(),
                            'status' => $order->getStatus(),
                            'exception' => $exception
                        ]
                    );
                    continue;
                }
                $this->handleOrderPostProcessing($order);
            } catch (\Throwable $exception) {
                $this->logger->critical(
                    'Exception occurred during order creation',
                    ['exception' => $exception, 'account' => $account, 'order_id' => $order->getOrderId()]
                );
            }
        }

        $this->storeManager->setCurrentStore($defaultStoreId);
    }

    abstract protected function shouldCompleteShipment(): bool;

    /**
     * @param Order $order
     * @return string
     */
    private function getOrderHash(Order $order): string
    {
        $data = $order->getData();
        return hash('sha256', json_encode($data));
    }

    /**
     * Used to emulate website context for a specific website.
     *
     * Since we use a cart to create orders, MSI relies on a website to resolve correct stock
     * and check for product salability status.
     * If we don't update website context, MSI would look for a stock status on a default stock only.
     * Which would cause issues when there are more than one stock used.
     *
     * @param int $merchantId
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function updateWebsiteContextForMerchant(int $merchantId): void
    {
        $rule = $this->listingRuleRepository->getByMerchantId($merchantId);
        if ($rule->getWebsiteId()) {
            $website = $this->storeManager->getWebsite($rule->getWebsiteId());
            $group = $this->storeManager->getGroup($website->getDefaultGroupId());
            $this->storeManager->setCurrentStore($group->getDefaultStoreId());
        }
    }

    /**
     * @param int $merchantId
     */
    protected function getOrdersCollection(int $merchantId): OrderCollection
    {
        $orders = $this->orderCollectionFactory->create();
        $orders->addFieldToFilter('merchant_id', $merchantId);
        $orders->addFieldToFilter(
            'status',
            [
                'in' => $this->getAllowedStatuses(),
            ]
        );
        $orders->addFieldToFilter(
            [
                'status' => 'status',
                'fulfillment_channel' => 'fulfillment_channel',
            ],
            [
                'status' => ['eq' => Definitions::SHIPPED_ORDER_STATUS],
                'fulfillment_channel' => ['eq' => Definitions::ORDER_FULFILLED_BY_MERCHANT],
            ]
        );

        $orders->setOrder('sales_order_id', 'asc');
        $orders->setOrder('id', 'desc');

        return $orders;
    }

    /**
     * @param Order $order
     * @param AccountOrderInterface $orderSetting
     */
    protected function handlePreconditions(Order $order, AccountOrderInterface $orderSetting): void
    {
    }

    /**
     * @param Order $order
     * @param AccountOrderInterface $orderSetting
     * @param AccountInterface $account
     */
    protected function handleMagentoOrderCreation(
        Order $order,
        AccountOrderInterface $orderSetting,
        AccountInterface $account
    ) {
        try {
            $salesOrderId = $order->getSalesOrderId();
            if (!$salesOrderId || !$this->salesOrderFactory->create()->load($salesOrderId)->getId()) {
                $this->createMagentoOrder($order, $orderSetting);
            }
        } catch (LocalizedException $exception) {
            $orderId = $order->getOrderId();
            $this->storesWithOrdersThatCannotBeImported->add($account);
            $this->logger->debug(
                'An error occurred during magento order creation',
                [
                    'amazon_order' => $orderId,
                    'status' => $order->getStatus(),
                    'exception' => $exception,
                ]
            );
            $order->setNotes(
                sprintf("Cannot create sales order: %s", $exception->getMessage())
            );
        }
    }

    /**
     * @param Order $order
     */
    protected function handleOrderPostProcessing(Order $order): void
    {
        if ($order->getStatus() === Definitions::SHIPPED_ORDER_STATUS && $order->getSalesOrderId()) {
            $this->orderManagement->setCompleted($order, $this->shouldCompleteShipment());
        }
    }

    protected function canBuildOrder(Order $order): bool
    {
        return true;
    }

    /**
     * @return string[]
     */
    protected function getAllowedStatuses(): array
    {
        return Definitions::PROCESSABLE_STATUSES;
    }
}
