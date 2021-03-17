<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Observer;

use Magento\Amazon\Api\Data\OrderInterface;
use Magento\Amazon\Api\Data\OrderTrackingInterface;
use Magento\Amazon\Api\OrderRepositoryInterface;
use Magento\Amazon\Api\OrderTrackingManagementInterface;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Shipment
 */
class Shipment implements ObserverInterface
{
    /** @var OrderRepositoryInterface $orderRepository */
    protected $orderRepository;
    /** @var OrderTrackingManagementInterface $orderTrackingManagement */
    protected $orderTrackingManagement;

    /**
     * @var \Magento\Amazon\Domain\Command\CommandDispatcher
     */
    private $commandDispatcher;

    /**
     * @var \Magento\Amazon\Domain\Command\OrderFulfillmentFactory
     */
    private $orderFulfillmentCommandFactory;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderTrackingManagementInterface $orderTrackingManagement
     * @param \Magento\Amazon\Domain\Command\CommandDispatcher $commandDispatcher
     * @param \Magento\Amazon\Domain\Command\OrderFulfillmentFactory $orderFulfillmentCommandFactory
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderTrackingManagementInterface $orderTrackingManagement,
        \Magento\Amazon\Domain\Command\CommandDispatcher $commandDispatcher,
        \Magento\Amazon\Domain\Command\OrderFulfillmentFactory $orderFulfillmentCommandFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderTrackingManagement = $orderTrackingManagement;
        $this->commandDispatcher = $commandDispatcher;
        $this->orderFulfillmentCommandFactory = $orderFulfillmentCommandFactory;
    }

    /**
     * Captures newly created fulfillment data, updates
     * local database with shipment, and creates a notification
     * request to Amazon with the fulfillment data
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $shipment = $observer->getShipment();

        try {
            /** @var OrderInterface */
            $marketplaceOrder = $this->orderRepository->getOrderBySalesOrderId($shipment->getOrderId());
        } catch (NoSuchEntityException $e) {
            return;
        }

        $track = $shipment->getTracksCollection()->getLastItem();

        foreach ($shipment->getItemsCollection() as $item) {
            if (!$item->getQty()) {
                continue;
            }

            try {
                /** @var OrderTrackingInterface */
                $orderTracking = $this->orderTrackingManagement->insert($track, $marketplaceOrder, $item);
            } catch (CouldNotSaveException $e) {
                // already logged
                continue;
            }

            try {
                $this->scheduleFulfillment($orderTracking);
                $marketplaceOrder->setNotes(__('Tracking information has been submitted to Amazon.'));
            } catch (CouldNotSaveException $e) {
                $marketplaceOrder->setStatus(Definitions::ERROR_ORDER_STATUS);
                $marketplaceOrder->setNotes(__('Transmission of tracking information failed. Please add tracking directly within Amazon Seller Central.'));
            }

            try {
                $this->orderRepository->save($marketplaceOrder);
            } catch (CouldNotSaveException $e) {
                // order no longer exists
                return;
            }
        }
    }

    /*
     * Schedules Amazon API action to upload order fulfillment data
     *
     * @param OrderTrackingInterface $orderTracking
     * @return void
     */
    private function scheduleFulfillment(OrderTrackingInterface $orderTracking)
    {
        $commandData = [
            'body' => $this->prepareCommandBody($orderTracking),
            'identifier' => (string)$orderTracking->getId()
        ];

        /** @var \Magento\Amazon\Domain\Command\OrderFulfillment $command */
        $command = $this->orderFulfillmentCommandFactory->create($commandData);
        $this->commandDispatcher->dispatch((int)$orderTracking->getMerchantId(), $command);
    }

    /**
     * Prepare Command Body
     *
     * @param OrderTrackingInterface $orderTracking
     * @return array
     */
    private function prepareCommandBody(OrderTrackingInterface $orderTracking): array
    {
        return [
            'order_id' => $orderTracking->getOrderId(),
            'fulfillment_date' => gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time()),
            'carrier_type' => $orderTracking->getCarrierType(),
            'carrier_name' => $orderTracking->getCarrierName(),
            'shipping_method' => $orderTracking->getShippingMethod(),
            'tracking' => $orderTracking->getTrackingNumber(),
            'order_item_id' => $orderTracking->getOrderItemId(),
            'quantity' => $orderTracking->getQuantity(),
        ];
    }
}
