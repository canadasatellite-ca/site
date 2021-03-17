<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api\Data;

/**
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface AccountOrderInterface
{
    /**
     * Get id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get merchant id
     *
     * @return int|null
     */
    public function getMerchantId();

    /**
     * Set merchant id
     *
     * @param int $id
     * @return $this
     */
    public function setMerchantId($id);

    /**
     * Get default store for order import
     *
     * @return int|null
     */
    public function getDefaultStore();

    /**
     * Set default store for order import
     *
     * @param int $value
     * @return $this
     */
    public function setDefaultStore($value);

    /**
     * Get Amazon account order creation flag
     *
     * @return int|null
     */
    public function getOrderIsActive();

    /**
     * Set Amazon account order creation flag
     *
     * @param int $value
     * @return $this
     */
    public function setOrderIsActive($value);

    /**
     * Get Amazon account customer creation flag
     *
     * @return int|null
     */
    public function getCustomerIsActive();

    /**
     * Set Amazon account customer creation flag
     *
     * @param int $value
     * @return $this
     */
    public function setCustomerIsActive($value);

    /**
     * Get does use external increment id flag
     *
     * @return int|null
     */
    public function getIsExternalOrderId();

    /**
     * Set does use external increment id flag
     *
     * @param int $value
     * @return $this
     */
    public function setIsExternalOrderId($value);

    /**
     * Get Amazon account reserve flag
     *
     * @return bool|null
     */
    public function getReserve();

    /**
     * Set Amazon account reserve flag
     *
     * @param bool $flag
     * @return $this
     */
    public function setReserve($flag);

    /**
     * Get Amazon account order status flag
     *
     * @return int|null
     */
    public function getCustomStatusIsActive();

    /**
     * Set Amazon account order status flag
     *
     * @param int $value
     * @return $this
     */
    public function setCustomStatusIsActive($value);

    /**
     * Get custom status for imported Amazon orders
     *
     * Only applies to orders awaiting shipment (i.e. unshipped)
     *
     * @return string|null
     */
    public function getCustomStatus();

    /**
     * Set custom status for imported Amazon orders
     *
     * Only applies to orders awaiting shipment (i.e. unshipped)
     *
     * @param string $value
     * @return $this
     */
    public function setCustomStatus($value);
}
