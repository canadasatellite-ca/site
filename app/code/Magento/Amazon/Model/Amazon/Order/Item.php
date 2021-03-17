<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon\Order;

use Magento\Amazon\Api\Data\OrderItemInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Item
 */
class Item extends AbstractModel implements OrderItemInterface
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\Amazon\Model\ResourceModel\Amazon\Order\Item::class
        );
    }

    /**
     * Get order item id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * Get order item merchant id
     *
     * @return int|null
     */
    public function getMerchantId()
    {
        return $this->getData('merchant_id');
    }

    /**
     * Set order item merchant id
     *
     * @param int $merchantId
     * @return $this
     */
    public function setMerchantId($merchantId)
    {
        return $this->setData('merchant_id', $merchantId);
    }

    /**
     * Get order item order id
     *
     * @return string|null
     */
    public function getOrderId()
    {
        return $this->getData('order_id');
    }

    /**
     * Set order item order id
     *
     * @param string $orderId
     * @return $this
     */
    public function setOrderId($orderId)
    {
        return $this->setData('order_id', $orderId);
    }

    /**
     * Get order item order item id
     *
     * @return string|null
     */
    public function getOrderItemId()
    {
        return $this->getData('order_item_id');
    }

    /**
     * Set order item order item id
     *
     * @param int $orderItemId
     * @return $this
     */
    public function setOrderItemId($orderItemId)
    {
        return $this->setData('order_item_id', $orderItemId);
    }

    /**
     * Get order item qty ordered
     *
     * @return int|null
     */
    public function getQtyOrdered()
    {
        return $this->getData('qty_ordered');
    }

    /**
     * Set order item qty ordered
     *
     * @param int $qty
     * @return $this
     */
    public function setQtyOrdered($qty)
    {
        return $this->setData('qty_ordered', $qty);
    }

    /**
     * Get order item qty shipped
     *
     * @return int|null
     */
    public function getQtyShipped()
    {
        return $this->getData('qty_shipped');
    }

    /**
     * Set order item qty shipped
     *
     * @param int $qty
     * @return void
     */
    public function setQtyShipped(int $qty)
    {
        $this->setData('qty_shipped', $qty);
    }

    /**
     * Get order item title
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->getData('title');
    }

    /**
     * Set order item title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        return $this->setData('title', $title);
    }

    /**
     * Get order item sku
     *
     * @return string|null
     */
    public function getSku()
    {
        return $this->getData('sku');
    }

    /**
     * Set order item sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku)
    {
        return $this->setData('sku', $sku);
    }

    /**
     * Get order item asin
     *
     * @return string|null
     */
    public function getAsin()
    {
        return $this->getData('asin');
    }

    /**
     * Set order item asin
     *
     * @param string $asin
     * @return $this
     */
    public function setAsin($asin)
    {
        return $this->setData('asin', $asin);
    }

    /**
     * Get order item condition
     *
     * @return string|null
     */
    public function getCondition()
    {
        return $this->getData('condition');
    }

    /**
     * Set order item condition
     *
     * @param string $condition
     * @return $this
     */
    public function setCondition($condition)
    {
        return $this->setData('condition', $condition);
    }

    /**
     * Get order item subcondition
     *
     * @return string|null
     */
    public function getSubcondition()
    {
        return $this->getData('subcondition');
    }

    /**
     * Set order item subcondition
     *
     * @param string $subcondition
     * @return $this
     */
    public function setSubcondition($subcondition)
    {
        return $this->setData('subcondition', $subcondition);
    }

    /**
     * Get order item price
     *
     * @return float|null
     */
    public function getItemPrice()
    {
        return $this->getData('item_price');
    }

    /**
     * Set order item price
     *
     * @param float $price
     * @return $this
     */
    public function setItemPrice($price)
    {
        return $this->setData('item_price', $price);
    }

    /**
     * Get order item tax
     *
     * @return float|null
     */
    public function getItemTax()
    {
        return $this->getData('item_tax');
    }

    /**
     * Set order item tax
     *
     * @param float $tax
     * @return $this
     */
    public function setItemTax($tax)
    {
        return $this->setData('item_tax', $tax);
    }

    /**
     * Get order item shipping price
     *
     * @return float|null
     */
    public function getShippingPrice()
    {
        return $this->getData('shipping_price');
    }

    /**
     * Set order item shipping price
     *
     * @param float $price
     * @return $this
     */
    public function setShippingPrice($price)
    {
        return $this->setData('shipping_price', $price);
    }

    /**
     * Get order item promotional discount
     *
     * @return float|null
     */
    public function getPromotionalDiscount()
    {
        return $this->getData('promotional_discount');
    }

    /**
     * Set order item promotional discount
     *
     * @param float $discount
     * @return $this
     */
    public function setPromotionalDiscount($discount)
    {
        return $this->setData('promotional_discount', $discount);
    }
}
