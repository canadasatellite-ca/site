<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon\Account;

use Magento\Amazon\Api\Data\AccountOrderInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Order
 */
class Order extends AbstractModel implements AccountOrderInterface
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\Amazon\Model\ResourceModel\Amazon\Account\Order::class
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
     * Get merchant id
     *
     * @return int|null
     */
    public function getMerchantId()
    {
        return $this->getData('merchant_id');
    }

    /**
     * Set merchant id
     *
     * @param int $id
     * @return $this
     */
    public function setMerchantId($id)
    {
        return $this->setData('merchant_id', $id);
    }

    /**
     * Get default store for order import
     *
     * @return int|null
     */
    public function getDefaultStore()
    {
        return $this->getData('default_store');
    }

    /**
     * Set default store for order import
     *
     * @param int $value
     * @return $this
     */
    public function setDefaultStore($value)
    {
        return $this->setData('default_store', $value);
    }

    /**
     * Get Amazon account order creation flag
     *
     * @return int|null
     */
    public function getOrderIsActive()
    {
        return $this->getData('order_is_active');
    }

    /**
     * Set Amazon account order creation flag
     *
     * @param int $value
     * @return $this
     */
    public function setOrderIsActive($value)
    {
        return $this->setData('order_is_active', $value);
    }

    /**
     * Get Amazon account customer creation flag
     *
     * @return int|null
     */
    public function getCustomerIsActive()
    {
        return $this->getData('customer_is_active');
    }

    /**
     * Set Amazon account customer creation flag
     *
     * @param int $value
     * @return $this
     */
    public function setCustomerIsActive($value)
    {
        return $this->setData('customer_is_active', $value);
    }

    /**
     * Get does use external increment id flag
     *
     * @return int|null
     */
    public function getIsExternalOrderId()
    {
        return $this->getData('is_external_order_id');
    }

    /**
     * Set does use external increment id flag
     *
     * @param int $value
     * @return $this
     */
    public function setIsExternalOrderId($value)
    {
        return $this->setData('is_external_order_id', $value);
    }

    /**
     * Get Amazon account reserve flag
     *
     * @return bool|null
     */
    public function getReserve()
    {
        return $this->getData('reserve');
    }

    /**
     * Set Amazon account reserve flag
     *
     * @param bool $flag
     * @return $this
     */
    public function setReserve($flag)
    {
        return $this->setData('reserve', $flag);
    }

    /**
     * Get Amazon account order status flag
     *
     * @return int|null
     */
    public function getCustomStatusIsActive()
    {
        return $this->getData('custom_status_is_active');
    }

    /**
     * Set Amazon account order status flag
     *
     * @param int $value
     * @return $this
     */
    public function setCustomStatusIsActive($value)
    {
        return $this->setData('custom_status_is_active', $value);
    }

    /**
     * Get custom status for imported Amazon orders
     *
     * Only applies to orders awaiting shipment (i.e. unshipped)
     *
     * @return string|null
     */
    public function getCustomStatus()
    {
        return $this->getData('custom_status');
    }

    /**
     * Set custom status for imported Amazon orders
     *
     * Only applies to orders awaiting shipment (i.e. unshipped)
     *
     * @param string $value
     * @return $this
     */
    public function setCustomStatus($value)
    {
        return $this->setData('custom_status', $value);
    }
}
