<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon;

use Magento\Amazon\Api\AccountListingRepositoryInterface;
use Magento\Amazon\Api\CustomerManagementInterface;
use Magento\Amazon\Api\Data\AccountOrderInterface;
use Magento\Amazon\Api\Data\OrderInterface;
use Magento\Amazon\Api\Data\OrderItemInterface;
use Magento\Amazon\Api\OrderItemRepositoryInterface;
use Magento\Amazon\Api\OrderManagementInterface;
use Magento\Amazon\Api\OrderRepositoryInterface;
use Magento\Amazon\Api\ProductManagementInterface;
use Magento\Amazon\Model\CurrencyConversion;
use Magento\Amazon\Model\DeadlockRetriesTrait;
use Magento\Amazon\Model\ResourceModel\Amazon\Order as ResourceModel;
use Magento\CatalogInventory\Observer\ItemsForReindex;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\DB\Adapter\DeadlockException;
use Magento\Framework\DB\Adapter\LockWaitException;
use Magento\Framework\DB\Transaction;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Sales\Api\Data\CreditmemoItemCreationInterfaceFactory;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface as SalesOrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface as SalesOrderItemInterface;
use Magento\Sales\Api\OrderRepositoryInterface as SalesOrderRepositoryInterface;
use Magento\Sales\Api\RefundOrderInterface;
use Magento\Sales\Exception\CouldNotRefundException;
use Magento\Sales\Exception\DocumentValidationException;
use Magento\Sales\Model\Convert\Order as OrderConverter;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class OrderManagement
 */
class OrderManagement implements OrderManagementInterface
{
    use DeadlockRetriesTrait;

    /** @var ResourceModel $resourceModel */
    private $resourceModel;
    /** @var OrderRepositoryInterface $orderRepository */
    private $orderRepository;
    /** @var SalesOrderRepositoryInterface $salesOrderRepository */
    private $salesOrderRepository;
    /** @var AccountListingRepositoryInterface $accountListingRepository */
    private $accountListingRepository;
    /** @var OrderItemRepositoryInterface $orderItemRepository */
    private $orderItemRepository;
    /** @var StoreManagerInterface $storeManager */
    private $storeManager;
    /** @var CustomerManagementInterface $customerManagement */
    private $customerManagement;
    /** @var CartManagementInterface $cartManagement */
    private $cartManagement;
    /** @var CartRepositoryInterface $cartRepository */
    private $cartRepository;
    /** @var OrderConverter $orderConverter */
    private $orderConverter;
    /** @var CurrencyConversion $currencyConversion */
    private $currencyConversion;
    /** @var DataPersistorInterface $dataPersistor */
    private $dataPersistor;
    /** @var ProductManagementInterface $productManagement */
    private $productManagement;
    /** @var InvoiceService $invoiceService */
    private $invoiceService;
    /** @var Transaction $transaction */
    private $transaction;
    /** @var ItemsForReindex */
    private $itemsForReindex;
    /** @var CreditmemoItemCreationInterfaceFactory $creditmemoFactory */
    private $creditmemoFactory;
    /** @var RefundOrderInterface $refundOrder */
    private $refundOrder;
    /**
     * @var OrderRegionResolver
     */
    private $orderRegionResolver;
    /**
     * @var AddressRepositoryInterface
     */
    private $customerAddressRepository;

    /**
     * @param ResourceModel $resourceModel
     * @param OrderRepositoryInterface $orderRepository
     * @param SalesOrderRepositoryInterface $salesOrderRepository
     * @param StoreManagerInterface $storeManager
     * @param CustomerManagementInterface $customerManagement
     * @param CartManagementInterface $cartManagement
     * @param CartRepositoryInterface $cartRepository
     * @param AccountListingRepositoryInterface $accountListingRepository
     * @param OrderItemRepositoryInterface $orderItemRepository
     * @param OrderConverter $orderConverter
     * @param CurrencyConversion $currencyConversion
     * @param DataPersistorInterface $dataPersistor
     * @param ProductManagementInterface $productManagement
     * @param InvoiceService $invoiceService
     * @param Transaction $transaction
     * @param ItemsForReindex $itemsForReindex
     * @param CreditmemoItemCreationInterfaceFactory $creditmemoFactory
     * @param RefundOrderInterface $refundOrderInterface
     * @param OrderRegionResolver $orderRegionResolver
     * @param AddressRepositoryInterface $customerAddressRepository
     */
    public function __construct(
        ResourceModel $resourceModel,
        OrderRepositoryInterface $orderRepository,
        SalesOrderRepositoryInterface $salesOrderRepository,
        StoreManagerInterface $storeManager,
        CustomerManagementInterface $customerManagement,
        CartManagementInterface $cartManagement,
        CartRepositoryInterface $cartRepository,
        AccountListingRepositoryInterface $accountListingRepository,
        OrderItemRepositoryInterface $orderItemRepository,
        OrderConverter $orderConverter,
        CurrencyConversion $currencyConversion,
        DataPersistorInterface $dataPersistor,
        ProductManagementInterface $productManagement,
        InvoiceService $invoiceService,
        Transaction $transaction,
        ItemsForReindex $itemsForReindex,
        CreditmemoItemCreationInterfaceFactory $creditmemoFactory,
        RefundOrderInterface $refundOrderInterface,
        OrderRegionResolver $orderRegionResolver,
        AddressRepositoryInterface $customerAddressRepository
    ) {
        $this->resourceModel = $resourceModel;
        $this->orderRepository = $orderRepository;
        $this->salesOrderRepository = $salesOrderRepository;
        $this->accountListingRepository = $accountListingRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->storeManager = $storeManager;
        $this->customerManagement = $customerManagement;
        $this->cartManagement = $cartManagement;
        $this->cartRepository = $cartRepository;
        $this->orderConverter = $orderConverter;
        $this->currencyConversion = $currencyConversion;
        $this->dataPersistor = $dataPersistor;
        $this->productManagement = $productManagement;
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        $this->itemsForReindex = $itemsForReindex;
        $this->creditmemoFactory = $creditmemoFactory;
        $this->refundOrder = $refundOrderInterface;
        $this->orderRegionResolver = $orderRegionResolver;
        $this->customerAddressRepository = $customerAddressRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getRevenueByMerchantId($merchantId)
    {
        $revenue = [];

        for ($i = 0; $i < 30; $i++) {
            $revenue[$i] = 0.00;
        }

        if (!$data = $this->resourceModel->getRevenueByMerchantId($merchantId)) {
            return $revenue;
        }

        foreach ($data as $daily) {
            $dailyRevenue = $daily['revenue'];
            $orderAge = $daily['order_age'];
            if (isset($revenue[$orderAge])) {
                $revenue[$orderAge] = $dailyRevenue;
            }
        }
        return $revenue;
    }

    /**
     * {@inheritdoc}
     * @deprecated this logic moved to database filters
     */
    public function canBuildOrder(OrderInterface $order): bool
    {
        $status = $order->getStatus();
        $fulfillmentChannel = $order->getFulfillmentChannel();

        if (in_array($status, Definitions::DO_NOT_BUILD_ORDER_STATUS, true)) {
            return false;
        }

        return $fulfillmentChannel !== Definitions::ORDER_FULFILLED_BY_AMAZON ||
            $status === Definitions::SHIPPED_ORDER_STATUS;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderNotes(OrderInterface $order, string $notes)
    {
        $order->setNotes(__($notes));

        try {
            $this->orderRepository->save($order);
        } catch (CouldNotSaveException $e) {
            return;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setCompleted(OrderInterface $order, bool $completeShipment)
    {
        // set amazon order as completed
        $order->setNotes(__('Status updated to completed.'));
        $order->setStatus(Definitions::COMPLETED_ORDER_STATUS);
        $order->setItemsShipped($order->getItemsShipped() + $order->getItemsUnshipped());
        $order->setItemsUnshipped(0);

        try {
            /** @var SalesOrderInterface */
            $salesOrder = $this->salesOrderRepository->get($order->getSalesOrderId());
            // set Magento order to complete
            $salesOrder->setState(\Magento\Sales\Model\Order::STATE_COMPLETE);
            $salesOrder->setStatus(
                $salesOrder->getConfig()->getStateDefaultStatus(\Magento\Sales\Model\Order::STATE_COMPLETE)
            );

            if ($completeShipment) {
                $this->setCompletedShipment($salesOrder);
            }

            $this->salesOrderRepository->save($salesOrder);
        } catch (InputException $e) {
            // no magento order, continue
            return;
        } catch (NoSuchEntityException $e) {
            // no magento order, continue
            return;
        } catch (\Exception $e) {
            // failed to save
            $order->setStatus(Definitions::ERROR_ORDER_STATUS);
            $order->setNotes('Failed to complete order. Please close the order manually.');
        }

        try {
            // save amazon order
            $this->resourceModel->save($order);
        } catch (\Exception $e) {
            // the order has been deleted, continue
        }
    }

    /**
     * @param SalesOrderInterface $order
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    private function setCompletedShipment(SalesOrderInterface $order)
    {
        if ($order->canShip()) {
            // Initialize the order shipment object
            $shipment = $this->orderConverter->toShipment($order);

            // Loop through order items
            foreach ($order->getAllItems() as $orderItem) {
                // Check if order item has qty to ship or is virtual
                if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                    continue;
                }
                $qtyShipped = $orderItem->getQtyToShip();
                // Create shipment item with qty
                $shipmentItem = $this->orderConverter->itemToShipmentItem($orderItem)->setQty($qtyShipped);
                // Add shipment item to shipment
                $shipment->addItem($shipmentItem);
            }

            // Register shipment
            $shipment->register();
            $shipment->getOrder()->setIsInProcess(true);

            // Save created shipment and order
            $shipment->save();
            $shipment->getOrder()->save();
        }
    }

    /**
     * {@inheritdoc}
     * @throws LocalizedException
     */
    public function create(OrderInterface $amazonOrder, AccountOrderInterface $account, bool $stockReserved)
    {
        $merchantId = $amazonOrder->getMerchantId();

        $store = $this->getStoreForOrder($account);

        /** @var CartInterface */
        $cart = $this->buildCart($account, $amazonOrder, $store);
        try {
            /** @var CartItemInterface[] */
            $cartItems = $this->productManagement->addProductToCart($account, $amazonOrder, $cart);

            /** @var bool */
            $customerFlag = $account->getCustomerIsActive();
            /** @var array */
            $address = $this->createAddress($amazonOrder, $customerFlag);
            /** @var AddressInterface */
            $shippingAddress = $cart->getShippingAddress()->addData($address);
            /** @var string */
            $orderId = $amazonOrder->getOrderId();
            /** @var array */
            $address = $this->createAddress($amazonOrder, $customerFlag, true);

            /** @var AddressInterface */
            $cart->getBillingAddress()->addData($address);

            $this->dataPersistor->set('marketplace_order_id', $orderId);

            $shippingAddress->setCollectShippingRates(true);
            $shippingAddress->collectShippingRates();
            $shippingAddress->setShippingMethod('amazonshipping_amazonshipping');
            $cart->getPayment()->importData(['method' => 'amazonpayment'])->setPoNumber($orderId);

            // set external order id (if applicable)
            if ($account->getIsExternalOrderId()) {
                $cart->setReservedOrderId($orderId);
            }

            // set cart variables
            $cart->setInventoryProcessed(true);
            $cart->setTotalsCollectedFlag(false);
            $cart->collectTotals();

            // edit order items (if applicable)
            $shippingAddress = $this->editCartItems($merchantId, $store, $cartItems, $shippingAddress);

            // set quote to Amazon reported totals
            $cart->setSubtotalWithDiscount($shippingAddress->getSubtotalWithDiscount());
            $cart->setBaseSubtotalWithDiscount($shippingAddress->getBaseSubtotalWithDiscount());
            $cart->setGrandTotal($shippingAddress->getGrandTotal());
            $cart->setBaseGrandTotal($shippingAddress->getBaseGrandTotal());
            // create order
            $magentoOrder = $this->convertCartToOrder($cart, $account);
        } catch (\Throwable $exception) {
            // we should clean up so we won't leave excessive data in the database
            if (!$cart->getCustomerIsGuest()) {
                $customer = $cart->getCustomer();
                if ($customer) {
                    $customerAddresses = $customer->getAddresses();
                    if ($customerAddresses) {
                        foreach ($customerAddresses as $customerAddress) {
                            try {
                                $this->doWithDeadlockRetries(function () use ($customerAddress) {
                                    $this->customerAddressRepository->delete($customerAddress);
                                });
                            } catch (DeadlockException | LockWaitException | NoSuchEntityException $exception) {
                                // whatever happened, it's not too bad
                            }
                        }
                    }
                }
            }
            try {
                $this->doWithDeadlockRetries(function () use ($cart) {
                    $this->cartRepository->delete($cart);
                });
            } catch (DeadlockException | LockWaitException | NoSuchEntityException $exception) {
                // whatever happened, it's not too bad
            }
            throw $exception;
        }

        $this->dataPersistor->clear('marketplace_order_id');

        // update results
        $amazonOrder->setSalesOrderId($magentoOrder->getEntityId());
        $amazonOrder->setSalesOrderNumber($magentoOrder->getIncrementId());

        $amazonOrder = $this->orderRepository->save($amazonOrder);

        // invoice order if needed
        $this->createMagentoInvoice($amazonOrder, $magentoOrder, $account);

        return $amazonOrder;
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreForOrder(AccountOrderInterface $account): StoreInterface
    {
        try {
            /** @var StoreInterface */
            return $this->storeManager->getStore($account->getDefaultStore());
        } catch (NoSuchEntityException $e) {
            return $this->storeManager->getDefaultStoreView();
        }
    }

    /**
     * @param AccountOrderInterface $account
     * @param OrderInterface $order
     * @param StoreInterface $store
     * @return CartInterface
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    private function buildCart(AccountOrderInterface $account, OrderInterface $order, StoreInterface $store)
    {
        /** @var boolean */
        $isCreateMagentoCustomer = (bool)$account->getCustomerIsActive();
        /** @var string */
        $email = $order->getOrderId() . '@amazon.com';
        /** @var int */
        $cartId = null;

        if ($isCreateMagentoCustomer) {
            /** @var CustomerInterface */
            $customer = $this->customerManagement->create($order, $store);
            /** @var int */
            $customerId = $customer->getId();

            try {
                $cartId = $this->cartManagement->createEmptyCartForCustomer($customerId);
            } catch (NoSuchEntityException $e) {
                $cartId = null;
            }
        }

        // create empty cart
        if (!$cartId) {
            $cartId = $this->cartManagement->createEmptyCart();
        }

        /** @var CartInterface */
        $cart = $this->cartRepository->get($cartId);

        // In case there were any leftovers for the customer — we definitely don't want to put them in the order
        $cart->removeAllItems();
        $cart->removeAllAddresses();

        $cart->setCustomerIsGuest(!$isCreateMagentoCustomer);
        $cart->setCustomerEmail($order->getBuyerEmail() ?: $email);
        $cart->setStoreId($store->getStoreId());
        $cart->setIgnoreOldQty(true);

        return $cart;
    }

    /**
     * Create order address
     *
     * $isBillingFlag is used to specify whether the requested
     * address is for shipping or billing
     *
     * @param OrderInterface $order
     * @param boolean $customerFlag
     * @param boolean $isBillingFlag
     * @return array
     */
    private function createAddress(OrderInterface $order, $customerFlag, $isBillingFlag = false): array
    {
        /** @var string */
        $name = ($isBillingFlag) ? $order->getBuyerName() : $order->getShipName();
        $name = explode(' ', $name, 2);
        /** @var string */
        $firstname = $name[0] ?? __('Undefined');
        /** @var string */
        $lastname = $name[1] ?? __('Undefined');
        /** @var string */
        $street = $order->getShipAddressOne();
        $street .= ($order->getShipAddressTwo()) ? '; ' . $order->getShipAddressTwo() : '';
        $street .= ($order->getShipAddressThree()) ? '; ' . $order->getShipAddressThree() : '';
        /** @var string */
        $phone = ($order->getShipPhone()) ? $order->getShipPhone() : '111-111-1111';

        /** @var array */
        return [
            'firstname' => $firstname,
            'customer_firstname' => $firstname,
            'lastname' => $lastname,
            'customer_lastname' => $lastname,
            'street' => $street,
            'city' => $order->getShipCity(),
            'country_id' => $order->getShipCountry(),
            'region' => $this->orderRegionResolver->resolveRegion($order->getShipCountry(), $order->getShipRegion()),
            'postcode' => $order->getShipPostalCode(),
            'telephone' => $phone,
            'save_in_address_book' => $customerFlag
        ];
    }

    /**
     * Edit cart order items to match the Amazon reported sales totals
     * to include Amazon promotional discounts and Amazon sales tax
     *
     * @param int $merchantId
     * @param StoreInterface $store
     * @param array $items
     * @param AddressInterface $address
     * @return AddressInterface
     * @throws NoSuchEntityException
     */
    private function editCartItems(
        int $merchantId,
        StoreInterface $store,
        array $items,
        AddressInterface $address
    ): AddressInterface {
        try {
            $account = $this->accountListingRepository->getByMerchantId($merchantId);
        } catch (NoSuchEntityException $e) {
            return $address;
        }

        /** @var float */
        $totalDiscountAmount = 0.00;
        /** @var float */
        $totalBaseDiscountAmount = 0.00;
        /** @var float */
        $totalTax = 0.00;
        /** @var float */
        $totalBaseTax = 0.00;
        /** @var string */
        $conversionCode = ($account->getCcIsActive()) ? $account->getCcRate() : '';
        /** @var string */
        $storeCurrencyCode = $store->getDefaultCurrencyCode();

        foreach ($items as $id => $item) {
            /** @var OrderItemInterface */
            $amazonOrderItem = $this->orderItemRepository->getById($id);

            if (!$amazonOrderItem->getId()) {
                continue;
            }

            /** @var float */
            $rowTotal = ($item->getRowTotal()) ? $item->getRowTotal() : 1.00;
            $discount = 0.00;

            // edit discount amount(s)
            if ($baseDiscount = $amazonOrderItem->getPromotionalDiscount()) {
                if ($conversionCode) {
                    $baseDiscount = $this->currencyConversion->convert($baseDiscount, $conversionCode);
                }

                $discount = (float)$this->currencyConversion->convert($baseDiscount, null, $storeCurrencyCode);
                $discountPercent = round((float)($discount / $rowTotal), 4, PHP_ROUND_HALF_UP);

                $item->setDiscountAmount($discount);
                $item->setBaseDiscountAmount($baseDiscount);
                $item->setDiscountPercent($discountPercent);
                $item->setRowTotalWithDiscount($item->getRowTotalWithDiscount() - $discount);

                $totalDiscountAmount += $discount;
                $totalBaseDiscountAmount += $baseDiscount;
            }

            // edit sales tax amount(s)
            if ($baseTax = $amazonOrderItem->getItemTax()) {
                if ($conversionCode) {
                    $baseTax = $this->currencyConversion->convert($baseTax, $conversionCode);
                }

                $tax = $this->currencyConversion->convert($baseTax, null, $storeCurrencyCode);
                $taxPercent = round((float)($tax / ($rowTotal - $discount)) * 100, 4, PHP_ROUND_HALF_UP);

                $item->setTaxAmount($tax);
                $item->setBaseTaxAmount($baseTax);
                $item->setTaxPercent($taxPercent);
                $item->setRowTotalInclTax($item->getRowTotalInclTax() + $tax);
                $item->setBaseRowTotalInclTax($item->getBaseRowTotalInclTax() + $baseTax);

                $totalTax += $tax;
                $totalBaseTax += $baseTax;
            }
        }

        // update quote shipping address with adjusted discount amount(s)
        if ($totalDiscountAmount) {
            $adjustment = -($totalDiscountAmount) - $address->getDiscountAmount();
            $baseAdjustment = -($totalBaseDiscountAmount) - $address->getBaseDiscountAmount();
            $address->setDiscountAmount(-($totalDiscountAmount));
            $address->setBaseDiscountAmount(-($totalBaseDiscountAmount));
            $address->setSubtotalWithDiscount(-($address->getSubtotalWithDiscount() - $adjustment));
            $address->setBaseSubtotalWithDiscount($address->getBaseSubtotalWithDiscount() - $baseAdjustment);
            $address->setGrandTotal($address->getGrandTotal() + $adjustment);
            $address->setBaseGrandTotal($address->getBaseGrandTotal() + $baseAdjustment);
            $address->setDiscountDescription(_('Amazon Applied Discount'));
        }

        // update quote shipping address with adjusted tax
        if ($totalTax) {
            $adjustment = $totalTax - $address->getTaxAmount();
            $baseAdjustment = $totalBaseTax - $address->getBaseTaxAmount();
            $address->setTaxAmount($totalTax);
            $address->setBaseTaxAmount($totalBaseTax);
            $address->setGrandTotal($address->getGrandTotal() + $adjustment);
            $address->setBaseGrandTotal($address->getBaseGrandTotal() + $baseAdjustment);
        }

        return $address;
    }

    /**
     * Convert cart to order object
     * Also sets a customer order status (if applicable)
     *
     * @param CartInterface $cart
     * @param AccountOrderInterface $account
     * @param bool $stockReserved
     * @return SalesOrderInterface
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    private function convertCartToOrder(CartInterface $cart, AccountOrderInterface $account)
    {
        $this->cartRepository->save($cart);
        $this->itemsForReindex->setItems([]);

        if (!$cart->getAllVisibleItems()) {
            throw new LocalizedException(__('No items in the order. Please check data consistency or report a bug.'));
        }
        $order = $this->cartManagement->submit($cart);

        $order->setEmailSent(false);
        $order->setSendEmail(false);

        /** @var string */
        $status = $account->getCustomStatusIsActive();
        /** @var string */
        $processingState = $account->getCustomStatus();

        // if custom status
        if ($status) {
            if ($status = !Definitions::SHIPPED_ORDER_STATUS) {
                if ($processingState) {
                    $order->setStatus($processingState);
                }
            }
        }

        return $order;
    }

    /**
     * Invoice Magento order
     *
     * @param OrderInterface $amazonOrder
     * @param SalesOrderInterface $order
     * @param AccountOrderInterface $account
     * @throws \Exception
     */
    private function createMagentoInvoice(
        OrderInterface $amazonOrder,
        SalesOrderInterface $order,
        AccountOrderInterface $account
    ): void {
        /** @var string */
        $errorMsg = __('Error creating invoice:  ');

        if (!$order->canInvoice()) {
            $amazonOrder->updateStatus('Error', 'Unable to invoice the order.');
            return;
        }

        try {
            /** @var InvoiceInterface */
            $invoice = $this->invoiceService->prepareInvoice($order);
        } catch (LocalizedException $e) {
            $amazonOrder->updateStatus('Error', $errorMsg . $e->getMessage() . '.');
            return;
        }

        $invoice->setRequestedCaptureCase(Invoice::CAPTURE_OFFLINE);

        try {
            $invoice->register();
        } catch (LocalizedException $e) {
            $amazonOrder->updateStatus('Error', $errorMsg . $e->getMessage() . '.');
            return;
        }

        try {
            $invoice->save();
        } catch (\Exception $e) {
            $amazonOrder->updateStatus('Error', $errorMsg . $e->getMessage() . '.');
            return;
        }

        try {
            $transactionSave = $this->transaction->addObject($invoice)->addObject($invoice->getOrder());
            $transactionSave->save();
        } catch (\Exception $e) {
            $amazonOrder->updateStatus('Error', $errorMsg . $e->getMessage() . '.');
            return;
        }

        /** @var string */
        $state = Order::STATE_PROCESSING;
        /** @var string */
        $status = $order->getConfig()->getStateDefaultStatus($state);

        // assign custom status
        if ($account->getCustomStatusIsActive()) {
            if ($customStatus = $account->getCustomStatus()) {
                $status = $customStatus;
            }
        }

        $order->setState($state);

        try {
            $order->addStatusHistoryComment(
                __('Invoice created for Amazon marketplace order.'),
                $status
            )
                ->setIsCustomerNotified(false);
            // save order
            $order->save();
        } catch (\Exception $e) {
            $amazonOrder->updateStatus('Error', $errorMsg . $e->getMessage() . '.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function refundOrder(int $orderId): bool
    {
        try {
            /** @var SalesOrderInterface */
            $order = $this->salesOrderRepository->get($orderId);
        } catch (NoSuchEntityException $e) {
            return false;
        } catch (InputException $e) {
            return false;
        }

        /** @var array */
        $itemIds = [];

        /** @var SalesOrderItemInterface[] */
        if (!$items = $order->getAllItems()) {
            return false;
        }

        foreach ($items as $item) {
            /** CreditmemoInterfaceFactory */
            $creditmemoItem = $this->creditmemoFactory->create();
            $creditmemoItem->setQty($item->getQtyOrdered() - $item->getQtyRefunded());
            $creditmemoItem->setOrderItemId($item->getItemId());

            $itemIds[] = $creditmemoItem;
        }

        try {
            $this->refundOrder->execute($orderId, $itemIds);
        } catch (DocumentValidationException | CouldNotRefundException $e) {
            return false;
        }

        return true;
    }
}
