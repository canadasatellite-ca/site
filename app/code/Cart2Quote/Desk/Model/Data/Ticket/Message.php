<?php
/**
 *
 * CART2QUOTE CONFIDENTIAL
 * __________________
 *
 *  [2009] - [2016] Cart2Quote B.V.
 *  All Rights Reserved.
 *
 * NOTICE OF LICENSE
 *
 * All information contained herein is, and remains
 * the property of Cart2Quote B.V. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to Cart2Quote B.V.
 * and its suppliers and may be covered by European and Foreign Patents,
 * patents in process, and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Cart2Quote B.V.
 *
 * @category    Cart2Quote
 * @package     Desk
 * @copyright   Copyright (c) 2016 Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */

namespace Cart2Quote\Desk\Model\Data\Ticket;

/**
 * Class Ticket
 */
class Message extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Cart2Quote\Desk\Api\Data\MessageInterface
{
    /**
     * Get message id
     *
     * @api
     * @return int
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Set message id
     *
     * @param int $id
     * @api
     *
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get ticket id
     *
     * @api
     * @return int|null
     */
    public function getTicketId()
    {
        return $this->_get(self::TICKET_ID);
    }

    /**
     * Set ticket id
     *
     * @param int $ticketId
     * @api
     *
     * @return $this
     */
    public function setTicketId($ticketId)
    {
        return $this->setData(self::TICKET_ID, $ticketId);
    }

    /**
     * Get customer id
     *
     * @api
     * @return int
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * Set customer id
     *
     * @param int $customerId
     * @api
     *
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Get priority id
     *
     * @api
     * @return bool
     */
    public function getIsPrivate()
    {
        return $this->_get(self::IS_PRIVATE);
    }

    /**
     * Set priority id
     *
     * @param bool $isPrivate
     * @api
     *
     * @return $this
     */
    public function setIsPrivate($isPrivate)
    {
        return $this->setData(self::IS_PRIVATE, $isPrivate);
    }

    /**
     * Get assignee id
     *
     * @api
     * @return string
     */
    public function getMessage()
    {
        return $this->_get(self::MESSAGE);
    }

    /**
     * Set assignee id
     *
     * @param string $message
     * @api
     *
     * @return $this
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * Get created at time
     *
     * @api
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    /**
     * Set created at time
     *
     * @param string $createdAt
     * @api
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get updated at time
     *
     * @api
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->_get(self::UPDATED_AT);
    }

    /**
     * Set updated at time
     *
     * @param string $updatedAt
     * @api
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * Get user id
     *
     * @api
     * @return string|null
     */
    public function getUserId()
    {
        return $this->_get(self::USER_ID);
    }

    /**
     * Set user id
     *
     * @param string $userId
     * @api
     *
     * @return $this
     */
    public function setUserId($userId)
    {
        return $this->setData(self::USER_ID, $userId);
    }

    /**
     * Get user user name
     *
     * Notice:
     * This data is loaded from the admin_user or customer_entity table and cannot be saved via the message.
     *
     * @api
     * @return string|null
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * Set user user name
     *
     * Notice:
     * This data is loaded from the admin_user or customer_entity table and cannot be saved via the message.
     *
     * @param string $name
     * @api
     *
     * @return $this
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get user email
     *
     * Notice:
     * This data is loaded from the admin_user or customer_entity table and cannot be saved via the message.
     *
     * @api
     * @return string|null
     */
    public function getEmail()
    {
        return $this->_get(self::EMAIL);
    }

    /**
     * Set user email
     *
     * Notice:
     * This data is loaded from the admin_user or customer_entity table and cannot be saved via the message.
     *
     * @param string $email
     * @api
     *
     * @return $this
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }
}
