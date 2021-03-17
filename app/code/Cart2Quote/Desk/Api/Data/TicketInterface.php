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
namespace Cart2Quote\Desk\Api\Data;

/**
 * Ticket interface.
 */
interface TicketInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const STATUS_ID = 'status_id';
    const STATUS = 'status';
    const CUSTOMER_ID = 'customer_id';
    const PRIORITY_ID = 'priority_id';
    const PRIORITY = 'priority';
    const ASSIGNEE_ID = 'assignee_id';
    const STORE_ID = 'store_id';
    const UPDATED_AT = 'updated_at';
    const CREATED_AT = 'created_at';
    const SUBJECT = 'subject';
    const CUSTOMER_NAME = 'customer_name';
    const CUSTOMER_EMAIL = 'customer_email';
    const ASSIGNEE_NAME = 'assignee_name';
    const ASSIGNEE_EMAIL = 'assignee_email';
    const DELETED = 'deleted';

    /**
     * Get ticket id
     *
     * @api
     * @return int|null
     */
    public function getId();

    /**
     * Set ticket id
     *
     * @param int $id
     * @api
     *
     * @return $this
     */
    public function setId($id);

    /**
     * Get status id
     *
     * @api
     * @return int|null
     */
    public function getStatusId();

    /**
     * Set status id
     *
     * @param int $statusId
     * @api
     *
     * @return $this
     */
    public function setStatusId($statusId);

    /**
     * Get status
     *
     * @api
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     * @api
     *
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get customer id
     *
     * @api
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer id
     *
     * @param int $customerId
     * @api
     *
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get priority id
     *
     * @api
     * @return string|null
     */
    public function getPriorityId();

    /**
     * Set priority id
     *
     * @param int $priorityId
     * @api
     *
     * @return $this
     */
    public function setPriorityId($priorityId);

    /**
     * Get priority id
     *
     * @api
     * @return string|null
     */
    public function getPriority();

    /**
     * Set priority id
     *
     * @param string $priority
     * @api
     *
     * @return $this
     */
    public function setPriority($priority);

    /**
     * Get assignee id
     *
     * @api
     * @return string|null
     */
    public function getAssigneeId();

    /**
     * Set assignee id
     *
     * @param int $assigneeId
     * @api
     *
     * @return $this
     */
    public function setAssigneeId($assigneeId);

    /**
     * Get store id
     *
     * @api
     * @return int|null
     */
    public function getStoreId();

    /**
     * Set store id
     *
     * @param int $storeId
     * @api
     *
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * Get created at time
     *
     * @api
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created at time
     *
     * @param string $createdAt
     * @api
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated at time
     *
     * @api
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated at time
     *
     * @param string $updatedAt
     * @api
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get updated at time
     *
     * @api
     * @return string|null
     */
    public function getSubject();

    /**
     * Get customer name
     *
     * Notice:
     * This data is loaded from the customer_entity table and cannot be saved via the message.
     *
     * @api
     * @return string|null
     */
    public function getCustomerName();

    /**
     * Set customer name
     *
     * Notice:
     * This data is loaded from the customer_entity table and cannot be saved via the message.
     *
     * @param string $customerName
     * @api
     *
     * @return $this
     */
    public function setCustomerName($customerName);

    /**
     * Get customer email
     *
     * Notice:
     * This data is loaded from the customer_entity table and cannot be saved via the message.
     *
     * @api
     * @return string|null
     */
    public function getCustomerEmail();

    /**
     * Set customer email
     *
     * Notice:
     * This data is loaded from the customer_entity table and cannot be saved via the message.
     *
     * @param string $customerEmail
     * @api
     *
     * @return $this
     */
    public function setCustomerEmail($customerEmail);

    /**
     * Get assignee name
     *
     * Notice:
     * This data is loaded from the admin_user table and cannot be saved via the message.
     *
     * @api
     * @return string|null
     */
    public function getAssigneeName();

    /**
     * Set assignee name
     *
     * Notice:
     * This data is loaded from the admin_user table and cannot be saved via the message.
     *
     * @param string $assigneeName
     * @api
     *
     * @return $this
     */
    public function setAssigneeName($assigneeName);

    /**
     * Get assignee email
     *
     * Notice:
     * This data is loaded from the admin_user table and cannot be saved via the message.
     *
     * @api
     * @return string|null
     */
    public function getAssigneeEmail();

    /**
     * Set assignee email
     *
     * Notice:
     * This data is loaded from the admin_user table and cannot be saved via the message.
     *
     * @param string $assigneeEmail
     * @api
     *
     * @return $this
     */
    public function setAssigneeEmail($assigneeEmail);

    /**
     * Set updated at time
     *
     * @param string $subject
     * @api
     *
     * @return $this
     */
    public function setSubject($subject);

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @api
     *
     * @return $this
     */
    public function setDeleted($deleted);

    /**
     * Get deleted
     *
     * @api
     * @return boolean
     */
    public function getDeleted();
}
