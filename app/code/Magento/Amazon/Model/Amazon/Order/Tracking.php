<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model\Amazon\Order;

use Magento\Amazon\Api\Data\OrderTrackingInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Tracking
 */
class Tracking extends AbstractModel implements OrderTrackingInterface
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\Amazon\Model\ResourceModel\Amazon\Order\Tracking::class
        );
    }

    /**
     * Get id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * Get Amazon merchant id
     *
     * @return int|null
     */
    public function getMerchantId()
    {
        return $this->getData('merchant_id');
    }

    /**
     * Set Amazon merchant id
     *
     * @param int
     * @return $this
     */
    public function setMerchantId($merchantId)
    {
        return $this->setData('merchant_id', $merchantId);
    }

    /**
     * Get Amazon order id
     *
     * @return string|null
     */
    public function getOrderId()
    {
        return $this->getData('order_id');
    }

    /**
     * Set Amazon order id
     *
     * @param string $orderId
     * @return $this
     */
    public function setOrderId($orderId)
    {
        return $this->setData('order_id', $orderId);
    }

    /**
     * Get Amazon order item id
     *
     * @return string|null
     */
    public function getOrderItemId()
    {
        return $this->getData('order_item_id');
    }

    /**
     * Set Amazon order item id
     *
     * @param string $orderItemId
     * @return $this
     */
    public function setOrderItemId($orderItemId)
    {
        return $this->setData('order_item_id', $orderItemId);
    }

    /**
     * Get Amazon carrier type
     *
     * @return string|null
     */
    public function getCarrierType()
    {
        return $this->getData('carrier_type');
    }

    /**
     * Set Amazon carrier type
     *
     * @param string $carrierType
     * @return void
     */
    public function setCarrierType(string $carrierType)
    {
        $this->setData('carrier_type', $carrierType);
    }

    /**
     * Get Amazon carrier name
     *
     * @return string|null
     */
    public function getCarrierName()
    {
        return $this->getData('carrier_name');
    }

    /**
     * Set Amazon carrier name
     *
     * @param string $carrierName
     * @return $this
     */
    public function setCarrierName($carrierName)
    {
        return $this->setData('carrier_name', $carrierName);
    }

    /**
     * Get Amazon shipping method
     *
     * @return string|null
     */
    public function getShippingMethod()
    {
        return $this->getData('shipping_method');
    }

    /**
     * Set Amazon shipping method
     *
     * @param string $shippingMethod
     * @return $this
     */
    public function setShippingMethod($shippingMethod)
    {
        return $this->setData('shipping_method', $shippingMethod);
    }

    /**
     * Get Amazon tracking number
     *
     * @return string|null
     */
    public function getTrackingNumber()
    {
        return $this->getData('tracking_number');
    }

    /**
     * Set Amazon tracking number
     *
     * @param string $trackingNumber
     * @return $this
     */
    public function setTrackingNumber($trackingNumber)
    {
        return $this->setData('tracking_number', $trackingNumber);
    }

    /**
     * Get Amazon order item quantity
     *
     * @return int|null
     */
    public function getQuantity()
    {
        return $this->getData('quantity');
    }

    /**
     * Set Amazon order item quantity
     *
     * @param string $qty
     * @return $this
     */
    public function setQuantity($qty)
    {
        return $this->setData('quantity', $qty);
    }
}
