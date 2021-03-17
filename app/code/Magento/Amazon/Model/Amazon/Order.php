<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon;

use Magento\Amazon\Api\Data\OrderInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Order
 */
class Order extends AbstractModel implements OrderInterface
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\Amazon\Model\ResourceModel\Amazon\Order::class
        );
    }

    /**
     * Get order id
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
     * @param int $merchantId
     * @return $this
     */
    public function setMerchantId($merchantId)
    {
        return $this->setData('merchant_id', $merchantId);
    }

    /**
     * Get order id
     *
     * @return string|null
     */
    public function getOrderId()
    {
        return $this->getData('order_id');
    }

    /**
     * Set order id
     *
     * @param string $orderId
     * @return $this
     */
    public function setOrderId($orderId)
    {
        return $this->setData('order_id', $orderId);
    }

    /**
     * Get order id
     *
     * @return string|null
     */
    public function getSalesOrderId()
    {
        return $this->getData('sales_order_id');
    }

    /**
     * Set order id
     *
     * @param string $id
     * @return $this
     */
    public function setSalesOrderId($id)
    {
        return $this->setData('sales_order_id', $id);
    }

    /**
     * Get order number
     *
     * @return string|null
     */
    public function getSalesOrderNumber()
    {
        return $this->getData('sales_order_number');
    }

    /**
     * Set order number
     *
     * @param string $orderNumber
     * @return $this
     */
    public function setSalesOrderNumber($orderNumber)
    {
        return $this->setData('sales_order_number', $orderNumber);
    }

    /**
     * Get order status
     * @return string|null
     */
    public function getStatus()
    {
        return $this->getData('status');
    }

    /**
     * Set order status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData('status', $status);
    }

    /**
     * Get order buyer email
     *
     * @return string|null
     */
    public function getBuyerEmail()
    {
        return $this->getData('buyer_email');
    }

    /**
     * Set order buyer email
     *
     * @param string $email
     * @return $this
     */
    public function setBuyerEmail($email)
    {
        return $this->setData('buyer_email', $email);
    }

    /**
     * Get order ship service level
     *
     * @return string|null
     */
    public function getShipServiceLevel()
    {
        return $this->getData('ship_service_level');
    }

    /**
     * Set order ship service level
     *
     * @param string $value
     * @return $this
     */
    public function setShipServiceLevel($value)
    {
        return $this->setData('ship_service_level', $value);
    }

    /**
     * Get order sales channel
     *
     * @return string|null
     */
    public function getSalesChannel()
    {
        return $this->getData('sales_channel');
    }

    /**
     * Set order sales channel
     *
     * @param string $value
     * @return $this
     */
    public function setSalesChannel($value)
    {
        return $this->setData('sales_channel', $value);
    }

    /**
     * Get shipped by amazon
     *
     * @return int|null
     */
    public function getShippedByAmazon()
    {
        return $this->getData('shipped_by_amazon');
    }

    /**
     * Set shipped by amazon
     *
     * @param int $value
     * @return $this
     */
    public function setShippedByAmazon($value)
    {
        return $this->setData('shipped_by_amazon', $value);
    }

    /**
     * Get is business flag
     *
     * @return int|null
     */
    public function getIsBusiness()
    {
        return $this->getData('is_business');
    }

    /**
     * Set is business flag
     *
     * @param int $flag
     * @return $this
     */
    public function setIsBusiness($flag)
    {
        return $this->setData('is_business', $flag);
    }

    /**
     * Get items shipped
     *
     * @return int|null
     */
    public function getItemsShipped()
    {
        return $this->getData('items_shipped');
    }

    /**
     * Set items shipped
     *
     * @param int $value
     * @return $this
     */
    public function setItemsShipped($value)
    {
        return $this->setData('items_shipped', $value);
    }

    /**
     * Get items unshipped
     *
     * @return int|null
     */
    public function getItemsUnshipped()
    {
        return $this->getData('items_unshipped');
    }

    /**
     * Set items unshipped
     *
     * @param int $value
     * @return $this
     */
    public function setItemsUnshipped($value)
    {
        return $this->setData('items_unshipped', $value);
    }

    /**
     * Get buyer name
     *
     * @return string|null
     */
    public function getBuyerName()
    {
        return $this->getData('buyer_name');
    }

    /**
     * Set buyer name
     *
     * @param string $name
     * @return $this
     */
    public function setBuyerName($name)
    {
        return $this->setData('buyer_name', $name);
    }

    /**
     * Get currency
     *
     * @return string|null
     */
    public function getCurrency()
    {
        return $this->getData('currency');
    }

    /**
     * Set currency
     *
     * @param string $value
     * @return $this
     */
    public function setCurrency($value)
    {
        return $this->setData('currency', $value);
    }

    /**
     * Get order total
     *
     * @return float|null
     */
    public function getTotal()
    {
        return $this->getData('total');
    }

    /**
     * Set order total
     *
     * @param float $total
     * @return $this
     */
    public function setTotal($total)
    {
        return $this->setData('total', $total);
    }

    /**
     * Get is premium flag
     *
     * @return int|null
     */
    public function getIsPremium()
    {
        return $this->getData('is_premium');
    }

    /**
     * Set is premium flag
     *
     * @param int $flag
     * @return $this
     */
    public function setIsPremium($flag)
    {
        return $this->setData('is_premium', $flag);
    }

    /**
     * Get is prime flag
     *
     * @return int|null
     */
    public function getIsPrime()
    {
        return $this->getData('is_prime');
    }

    /**
     * Set is prime flag
     *
     * @param int $flag
     * @return $this
     */
    public function setIsPrime($flag)
    {
        return $this->setData('is_prime', $flag);
    }

    /**
     * Get purchase order number
     *
     * @return string|null
     */
    public function getPurchaseOrderNumber()
    {
        return $this->getData('purchase_order_number');
    }

    /**
     * Set purchase order number
     *
     * @param string $purchaseOrderNumber
     * @return $this
     */
    public function setPurchaseOrderNumber($purchaseOrderNumber)
    {
        return $this->setData('purchase_order_number', $purchaseOrderNumber);
    }

    /**
     * Get is replacement order flag
     *
     * @return int|null
     */
    public function getIsReplacement()
    {
        return $this->getData('is_replacement');
    }

    /**
     * Set is replacement order flag
     *
     * @param int $flag
     * @return $this
     */
    public function setIsReplacement($flag)
    {
        return $this->setData('is_replacement', $flag);
    }

    /**
     * Get fulfillment channel
     *
     * @return string|null
     */
    public function getFulfillmentChannel()
    {
        return $this->getData('fulfillment_channel');
    }

    /**
     * Set fulfillment channel
     *
     * @param string $value
     * @return $this
     */
    public function setFulfillmentChannel($value)
    {
        return $this->setData('fulfillment_channel', $value);
    }

    /**
     * Get payment method
     *
     * @return string|null
     */
    public function getPaymentMethod()
    {
        return $this->getData('payment_method');
    }

    /**
     * Set payment method
     *
     * @param string $value
     * @return $this
     */
    public function setPaymentMethod($value)
    {
        return $this->setData('payment_method', $value);
    }

    /**
     * Get service level
     *
     * @return string|null
     */
    public function getServiceLevel()
    {
        return $this->getData('service_level');
    }

    /**
     * Set service level
     *
     * @param string $value
     * @return $this
     */
    public function setServiceLevel($value)
    {
        return $this->setData('service_level', $value);
    }

    /**
     * Get ship name
     *
     * @return string|null
     */
    public function getShipName()
    {
        return $this->getData('ship_name');
    }

    /**
     * Set ship name
     *
     * @param string $value
     * @return $this
     */
    public function setShipName($value)
    {
        return $this->setData('ship_name', $value);
    }

    /**
     * Get ship address one
     *
     * @return string|null
     */
    public function getShipAddressOne()
    {
        return $this->getData('ship_address_one');
    }

    /**
     * Set ship address one
     *
     * @param string $value
     * @return $this
     */
    public function setShipAddressOne($value)
    {
        return $this->setData('ship_address_one', $value);
    }

    /**
     * Get ship address two
     *
     * @return string|null
     */
    public function getShipAddressTwo()
    {
        return $this->getData('ship_address_two');
    }

    /**
     * Set ship address two
     *
     * @param string $value
     * @return $this
     */
    public function setShipAddressTwo($value)
    {
        return $this->setData('ship_address_two', $value);
    }

    /**
     * Get ship address three
     *
     * @return string|null
     */
    public function getShipAddressThree()
    {
        return $this->getData('ship_address_three');
    }

    /**
     * Set ship address three
     *
     * @param string $value
     * @return $this
     */
    public function setShipAddressThree($value)
    {
        return $this->setData('ship_address_three', $value);
    }

    /**
     * Get ship city
     *
     * @return string|null
     */
    public function getShipCity()
    {
        return $this->getData('ship_city');
    }

    /**
     * Set ship city
     *
     * @param string $value
     * @return $this
     */
    public function setShipCity($value)
    {
        return $this->setData('ship_city', $value);
    }

    /**
     * Get ship region
     *
     * @return string|null
     */
    public function getShipRegion()
    {
        return $this->getData('ship_region');
    }

    /**
     * Set ship region
     *
     * @param string $value
     * @return $this
     */
    public function setShipRegion($value)
    {
        return $this->setData('ship_region', $value);
    }

    /**
     * Get ship postal code
     *
     * @return string|null
     */
    public function getShipPostalCode()
    {
        return $this->getData('ship_postal_code');
    }

    /**
     * Set ship postal code
     *
     * @param string $value
     * @return $this
     */
    public function setShipPostalCode($value)
    {
        return $this->setData('ship_postal_code', $value);
    }

    /**
     * Get ship country
     *
     * @return string|null
     */
    public function getShipCountry()
    {
        return $this->getData('ship_country');
    }

    /**
     * Set ship country
     *
     * @param string $value
     * @return $this
     */
    public function setShipCountry($value)
    {
        return $this->setData('ship_country', $value);
    }

    /**
     * Get ship phone
     *
     * @return string|null
     */
    public function getShipPhone()
    {
        return $this->getData('ship_phone');
    }

    /**
     * Set ship phone
     *
     * @param string $value
     * @return $this
     */
    public function setShipPhone($value)
    {
        return $this->setData('ship_phone', $value);
    }

    /**
     * Get purchase date
     *
     * @return \DateTime|null
     */
    public function getPurchaseDate()
    {
        return $this->getData('purchase_date');
    }

    /**
     * Set purchase date
     *
     * @param string $date
     * @return $this
     */
    public function setPurchaseDate($date)
    {
        return $this->setData('purchase_date', $date);
    }

    /**
     * Get latest ship date
     *
     * @return \DateTime|null
     */
    public function getLatestShipDate()
    {
        return $this->getData('latest_ship_date');
    }

    /**
     * Set latest ship date
     *
     * @param string $date
     * @return $this
     */
    public function setLatestShipDate($date)
    {
        return $this->setData('latest_ship_date', $date);
    }

    /**
     * Get reserved flag
     *
     * @return int|null
     */
    public function getReserved()
    {
        return $this->getData('reserved');
    }

    /**
     * Set reserved flag
     *
     * @param string $flag
     * @return $this
     */
    public function setReserved($flag)
    {
        return $this->setData('reserved', $flag);
    }

    /**
     * Get order notes
     *
     * @return string|null
     */
    public function getNotes()
    {
        return $this->getData('notes');
    }

    /**
     * Set order notes
     *
     * @param string $notes
     * @return $this
     */
    public function setNotes($notes)
    {
        return $this->setData('notes', $notes);
    }

    /**
     * Update amazon order status
     *
     * @param string $status
     * @param string $notes
     * @return void
     */
    public function updateStatus($status, $notes = null)
    {
        if (!$status) {
            return;
        }

        $this->setStatus($status);

        if ($notes) {
            $this->setNotes($notes);
        }

        try {
            $this->save();
        } catch (\Exception $e) {
            return;
        }
    }
}
