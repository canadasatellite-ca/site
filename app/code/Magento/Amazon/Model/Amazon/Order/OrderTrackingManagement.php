<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon\Order;

use Magento\Amazon\Api\Data\OrderInterface;
use Magento\Amazon\Api\Data\OrderTrackingInterface;
use Magento\Amazon\Api\Data\OrderTrackingInterfaceFactory;
use Magento\Amazon\Api\OrderRepositoryInterface;
use Magento\Amazon\Api\OrderTrackingManagementInterface;
use Magento\Amazon\Api\OrderTrackingRepositoryInterface;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Sales\Api\Data\ShipmentTrackInterface;
use Magento\Sales\Model\Order\Shipment\Item as ShipmentItem;

/**
 * Class OrderTrackingManagement
 */
class OrderTrackingManagement implements OrderTrackingManagementInterface
{
    /** @var OrderTrackingInterfaceFactory $orderTrackingFactory */
    private $orderTrackingFactory;
    /** @var OrderTrackingRepositoryInterface $orderTrackingRepository */
    private $orderTrackingRepository;
    /** @var OrderRepositoryInterface $orderRepository */
    private $orderRepository;

    /**
     * @param OrderTrackingInterfaceFactory $orderTrackingFactory
     * @param OrderTrackingRepositoryInterface $orderTrackingRepository
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        OrderTrackingInterfaceFactory $orderTrackingFactory,
        OrderTrackingRepositoryInterface $orderTrackingRepository,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderTrackingFactory = $orderTrackingFactory;
        $this->orderTrackingRepository = $orderTrackingRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function insert(ShipmentTrackInterface $track, OrderInterface $marketplaceOrder, ShipmentItem $orderItem)
    {
        /** @var string $carrierType */
        $carrierType = Definitions::CARRIER_TYPE;
        /** @var string $trackTitle */
        $trackTitle = $track->getTitle();
        /** @var string $trackNumber */
        $trackNumber = $track->getTrackNumber();
        /** @var string $carrierCode */
        $carrierCode = $track->getCarrierCode();
        /** @var string $carrierCode */
        $carrierName = $this->convertCarrierName($carrierCode, $trackTitle);

        // special handling for no carrier match
        if ($carrierName === $trackTitle) {
            $carrierType = Definitions::CARRIER_NAME;
        }

        /** @var OrderTrackingInterface */
        $orderTracking = $this->orderTrackingFactory->create();

        // set tracking data
        $orderTracking->setCarrierType($carrierType);
        $orderTracking->setCarrierName($carrierName);
        $orderTracking->setTrackingNumber($trackNumber);
        $orderTracking->setOrderId($marketplaceOrder->getOrderId());
        $orderTracking->setOrderItemId($orderItem->getData('description'));
        $orderTracking->setMerchantId($marketplaceOrder->getMerchantId());
        $orderTracking->setQuantity($orderItem->getData('qty'));
        $orderTracking->setShippingMethod($marketplaceOrder->getShipServiceLevel());

        // save order tracking details
        $this->orderTrackingRepository->save($orderTracking);

        /** @var int */
        $itemsShipped = $marketplaceOrder->getItemsShipped() + $orderItem->getQty();
        /** @var int */
        $itemsUnshipped = $marketplaceOrder->getItemsUnshipped() - $orderItem->getQty();
        $itemsUnshipped = ($itemsUnshipped < 0) ? 0 : $itemsUnshipped;
        $status = ($itemsUnshipped) ? Definitions::PARTIALLY_SHIPPED : Definitions::SHIPPED_ORDER_STATUS;

        $marketplaceOrder->setItemsShipped($itemsShipped);
        $marketplaceOrder->setItemsUnshipped($itemsUnshipped);
        $marketplaceOrder->setStatus($status);

        $this->orderRepository->save($marketplaceOrder);

        return $orderTracking;
    }

    /**
     * Converts carrier name into the respective Amazon carrier name
     *
     * @param string $carrierCode
     * @param string $trackTitle
     *
     * @return string
     */
    private function convertCarrierName($carrierCode, $trackTitle)
    {
        // assign carrier code
        switch ($carrierCode) {
            // fedex
            case Definitions::FEDEX_CODE:
                return Definitions::FEDEX_AMAZON_CODE;
            // ups
            case Definitions::UPS_CODE:
                return Definitions::UPS_AMAZON_CODE;
            // usps
            case Definitions::USPS_CODE:
                return Definitions::USPS_AMAZON_CODE;
            // default
            default:
                return $trackTitle;
        }
    }
}
