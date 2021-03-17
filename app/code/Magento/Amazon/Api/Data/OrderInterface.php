<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api\Data;

/**
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface OrderInterface
{
    /**
     * Get order id
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
     * @param int $merchantId
     * @return $this
     */
    public function setMerchantId($merchantId);

    /**
     * Get order id
     *
     * @return string|null
     */
    public function getOrderId();

    /**
     * Set order id
     *
     * @param string $orderId
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * Get order id
     *
     * @return string|null
     */
    public function getSalesOrderId();

    /**
     * Set order id
     *
     * @param string $id
     * @return $this
     */
    public function setSalesOrderId($id);

    /**
     * Get order number
     *
     * @return string|null
     */
    public function getSalesOrderNumber();

    /**
     * Set order number
     *
     * @param string $orderNumber
     * @return $this
     */
    public function setSalesOrderNumber($orderNumber);

    /**
     * Get order status
     *
     * @return string|null
     */
    public function getStatus();

    /**
     * Set order status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get order buyer email
     *
     * @return string|null
     */
    public function getBuyerEmail();

    /**
     * Set order buyer email
     *
     * @param string $email
     * @return $this
     */
    public function setBuyerEmail($email);

    /**
     * Get order ship service level
     *
     * @return string|null
     */
    public function getShipServiceLevel();

    /**
     * Set order ship service level
     *
     * @param string $value
     * @return $this
     */
    public function setShipServiceLevel($value);

    /**
     * Get order sales channel
     *
     * @return string|null
     */
    public function getSalesChannel();

    /**
     * Set order sales channel
     *
     * @param string $value
     * @return $this
     */
    public function setSalesChannel($value);

    /**
     * Get shipped by amazon
     *
     * @return int|null
     */
    public function getShippedByAmazon();

    /**
     * Set shipped by amazon
     *
     * @param int $value
     * @return $this
     */
    public function setShippedByAmazon($value);

    /**
     * Get is business flag
     *
     * @return int|null
     */
    public function getIsBusiness();

    /**
     * Set is business flag
     *
     * @param int $flag
     * @return $this
     */
    public function setIsBusiness($flag);

    /**
     * Get items shipped
     *
     * @return int|null
     */
    public function getItemsShipped();

    /**
     * Set items shipped
     *
     * @param int $value
     * @return $this
     */
    public function setItemsShipped($value);

    /**
     * Get items unshipped
     *
     * @return int|null
     */
    public function getItemsUnshipped();

    /**
     * Set items unshipped
     *
     * @param int $value
     * @return $this
     */
    public function setItemsUnshipped($value);

    /**
     * Get buyer name
     *
     * @return string|null
     */
    public function getBuyerName();

    /**
     * Set buyer name
     *
     * @param string $name
     * @return $this
     */
    public function setBuyerName($name);

    /**
     * Get currency
     *
     * @return string|null
     */
    public function getCurrency();

    /**
     * Set currency
     *
     * @param string $value
     * @return $this
     */
    public function setCurrency($value);

    /**
     * Get order total
     *
     * @return float|null
     */
    public function getTotal();

    /**
     * Set order total
     *
     * @param float $total
     * @return $this
     */
    public function setTotal($total);

    /**
     * Get is premium flag
     *
     * @return int|null
     */
    public function getIsPremium();

    /**
     * Set is premium flag
     *
     * @param int $flag
     * @return $this
     */
    public function setIsPremium($flag);

    /**
     * Get is prime flag
     *
     * @return int|null
     */
    public function getIsPrime();

    /**
     * Set is prime flag
     *
     * @param int $flag
     * @return $this
     */
    public function setIsPrime($flag);

    /**
     * Get purchase order number
     *
     * @return string|null
     */
    public function getPurchaseOrderNumber();

    /**
     * Set purchase order number
     *
     * @param string $purchaseOrderNumber
     * @return $this
     */
    public function setPurchaseOrderNumber($purchaseOrderNumber);

    /**
     * Get is replacement order flag
     *
     * @return int|null
     */
    public function getIsReplacement();

    /**
     * Set is replacement order flag
     *
     * @param int $flag
     * @return $this
     */
    public function setIsReplacement($flag);

    /**
     * Get fulfillment channel
     *
     * @return string|null
     */
    public function getFulfillmentChannel();

    /**
     * Set fulfillment channel
     *
     * @param string $value
     * @return $this
     */
    public function setFulfillmentChannel($value);

    /**
     * Get payment method
     *
     * @return string|null
     */
    public function getPaymentMethod();

    /**
     * Set payment method
     *
     * @param string $value
     * @return $this
     */
    public function setPaymentMethod($value);

    /**
     * Get service level
     *
     * @return string|null
     */
    public function getServiceLevel();

    /**
     * Set service level
     *
     * @param string $value
     * @return $this
     */
    public function setServiceLevel($value);

    /**
     * Get ship name
     *
     * @return string|null
     */
    public function getShipName();

    /**
     * Set ship name
     *
     * @param string $value
     * @return $this
     */
    public function setShipName($value);

    /**
     * Get ship address one
     *
     * @return string|null
     */
    public function getShipAddressOne();

    /**
     * Set ship address one
     *
     * @param string $value
     * @return $this
     */
    public function setShipAddressOne($value);

    /**
     * Get ship address two
     *
     * @return string|null
     */
    public function getShipAddressTwo();

    /**
     * Set ship address two
     *
     * @param string $value
     * @return $this
     */
    public function setShipAddressTwo($value);

    /**
     * Get ship address three
     *
     * @return string|null
     */
    public function getShipAddressThree();

    /**
     * Set ship address three
     *
     * @param string $value
     * @return $this
     */
    public function setShipAddressThree($value);

    /**
     * Get ship city
     *
     * @return string|null
     */
    public function getShipCity();

    /**
     * Set ship city
     *
     * @param string $value
     * @return $this
     */
    public function setShipCity($value);

    /**
     * Get ship region
     *
     * @return string|null
     */
    public function getShipRegion();

    /**
     * Set ship region
     *
     * @param string $value
     * @return $this
     */
    public function setShipRegion($value);

    /**
     * Get ship postal code
     *
     * @return string|null
     */
    public function getShipPostalCode();

    /**
     * Set ship postal code
     *
     * @param string $value
     * @return $this
     */
    public function setShipPostalCode($value);

    /**
     * Get ship country
     *
     * @return string|null
     */
    public function getShipCountry();

    /**
     * Set ship country
     *
     * @param string $value
     * @return $this
     */
    public function setShipCountry($value);

    /**
     * Get ship phone
     *
     * @return string|null
     */
    public function getShipPhone();

    /**
     * Set ship phone
     *
     * @param string $value
     * @return $this
     */
    public function setShipPhone($value);

    /**
     * Get purchase date
     *
     * @return \DateTime|null
     */
    public function getPurchaseDate();

    /**
     * Set purchase date
     *
     * @param string $date
     * @return $this
     */
    public function setPurchaseDate($date);

    /**
     * Get latest ship date
     *
     * @return \DateTime|null
     */
    public function getLatestShipDate();

    /**
     * Set latest ship date
     *
     * @param string $date
     * @return $this
     */
    public function setLatestShipDate($date);

    /**
     * Get reserved flag
     *
     * @return int|null
     */
    public function getReserved();

    /**
     * Set reserved flag
     *
     * @param string $flag
     * @return $this
     */
    public function setReserved($flag);

    /**
     * Get order notes
     *
     * @return string|null
     */
    public function getNotes();

    /**
     * Set order notes
     *
     * @param string $notes
     * @return $this
     */
    public function setNotes($notes);
}
