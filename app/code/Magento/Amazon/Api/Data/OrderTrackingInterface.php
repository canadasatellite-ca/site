<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Api\Data;

/**
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface OrderTrackingInterface
{
    /**
     * Get id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Amazon merchant id
     *
     * @return int|null
     */
    public function getMerchantId();

    /**
     * Set Amazon merchant id
     *
     * @param int $merchantId
     * @return $this
     */
    public function setMerchantId($merchantId);

    /**
     * Get Amazon order id
     *
     * @return string|null
     */
    public function getOrderId();

    /**
     * Set Amazon order id
     *
     * @param string $orderId
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * Get Amazon order item id
     *
     * @return string|null
     */
    public function getOrderItemId();

    /**
     * Set Amazon order item id
     *
     * @param string $orderItemId
     * @return $this
     */
    public function setOrderItemId($orderItemId);

    /**
     * Get Amazon carrier type
     *
     * @return string|null
     */
    public function getCarrierType();

    /**
     * Set Amazon carrier type
     *
     * @param string $carrierType
     * @return void
     */
    public function setCarrierType(string $carrierType);

    /**
     * Get Amazon carrier name
     *
     * @return string|null
     */
    public function getCarrierName();

    /**
     * Set Amazon carrier name
     *
     * @param string $carrierName
     * @return $this
     */
    public function setCarrierName($carrierName);

    /**
     * Get Amazon shipping method
     *
     * @return string|null
     */
    public function getShippingMethod();

    /**
     * Set Amazon shipping method
     *
     * @param string $shippingMethod
     * @return $this
     */
    public function setShippingMethod($shippingMethod);

    /**
     * Get Amazon tracking number
     *
     * @return string|null
     */
    public function getTrackingNumber();

    /**
     * Set Amazon tracking number
     *
     * @param string $trackingNumber
     * @return $this
     */
    public function setTrackingNumber($trackingNumber);

    /**
     * Get Amazon order item quantity
     *
     * @return int|null
     */
    public function getQuantity();

    /**
     * Set Amazon order item quantity
     *
     * @param string $qty
     * @return $this
     */
    public function setQuantity($qty);
}
