<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api\Data;

/**
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface OrderItemInterface
{
    /**
     * Get order item id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get order item merchant id
     *
     * @return int|null
     */
    public function getMerchantId();

    /**
     * Set order item merchant id
     *
     * @param int $merchantId
     * @return $this
     */
    public function setMerchantId($merchantId);

    /**
     * Get order item order id
     *
     * @return string|null
     */
    public function getOrderId();

    /**
     * Set order item order id
     *
     * @param string $orderId
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * Get order item order item id
     *
     * @return string|null
     */
    public function getOrderItemId();

    /**
     * Set order item order item id
     *
     * @param int $orderItemId
     * @return $this
     */
    public function setOrderItemId($orderItemId);

    /**
     * Get order item qty ordered
     *
     * @return int|null
     */
    public function getQtyOrdered();

    /**
     * Set order item qty ordered
     *
     * @param int $qty
     * @return $this
     */
    public function setQtyOrdered($qty);

    /**
     * Get order item qty shipped
     *
     * @return int|null
     */
    public function getQtyShipped();

    /**
     * Set order item qty shipped
     *
     * @param int $qty
     * @return void
     */
    public function setQtyShipped(int $qty);

    /**
     * Get order item title
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Set order item title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * Get order item sku
     *
     * @return string|null
     */
    public function getSku();

    /**
     * Set order item sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku);

    /**
     * Get order item asin
     *
     * @return string|null
     */
    public function getAsin();

    /**
     * Set order item asin
     *
     * @param string $asin
     * @return $this
     */
    public function setAsin($asin);

    /**
     * Get order item condition
     *
     * @return string|null
     */
    public function getCondition();

    /**
     * Set order item condition
     *
     * @param string $condition
     * @return $this
     */
    public function setCondition($condition);

    /**
     * Get order item subcondition
     *
     * @return string|null
     */
    public function getSubcondition();

    /**
     * Set order item subcondition
     *
     * @param string $subcondition
     * @return $this
     */
    public function setSubcondition($subcondition);

    /**
     * Get order item price
     *
     * @return float|null
     */
    public function getItemPrice();

    /**
     * Set order item price
     *
     * @param float $price
     * @return $this
     */
    public function setItemPrice($price);

    /**
     * Get order item tax
     *
     * @return float|null
     */
    public function getItemTax();

    /**
     * Set order item tax
     *
     * @param float $tax
     * @return $this
     */
    public function setItemTax($tax);

    /**
     * Get order item shipping price
     *
     * @return float|null
     */
    public function getShippingPrice();

    /**
     * Set order item shipping price
     *
     * @param float $price
     * @return $this
     */
    public function setShippingPrice($price);

    /**
     * Get order item promotional discount
     *
     * @return float|null
     */
    public function getPromotionalDiscount();

    /**
     * Set order item promotional discount
     *
     * @param float $discount
     * @return $this
     */
    public function setPromotionalDiscount($discount);
}
