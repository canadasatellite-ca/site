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

namespace Cart2Quote\Desk\Model\ResourceModel\Ticket;
use Magento\Framework\DB\Select;

/**
 * Ticket collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Collection model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cart2Quote\Desk\Model\Ticket', 'Cart2Quote\Desk\Model\ResourceModel\Ticket');
    }

    /**
     * Add customer filter
     *
     * @param int $customerId
     * @return $this
     */
    public function addCustomerFilter($customerId)
    {
        $this->getSelect()->where('customer_id = ?', $customerId);
        return $this;
    }

    /**
     * Add store filter
     *
     * @param int $storeId
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        $this->getSelect()->where('store_id = ?', $storeId);
        return $this;
    }

    /**
     * Add assignee filter
     *
     * @param int $assigneeId
     * @return $this
     */
    public function addAssigneeFilter($assigneeId)
    {
        $this->getSelect()->where('assignee_id = ?', $assigneeId);
        return $this;
    }

    /**
     * Set date order
     *
     * @param string $dir
     * @return $this
     */
    public function setDateOrder($dir = 'DESC')
    {
        $this->setOrder('created_at', $dir);
        return $this;
    }

    /**
     * Join the status code to the collection
     * @return $this
     */
    public function innerJoinStatus()
    {
        $this->join(
            ['hts' => $this->getTable('desk_ticket_status')],
            'hts.status_id = main_table.status_id',
            'code AS status_code');
        return $this;
    }

    /**
     * Join the priority code to the collection
     * @return $this
     */
    public function innerJoinPriority()
    {
        $this->join(
            ['htp' => $this->getTable('desk_ticket_priority')],
            'htp.priority_id = main_table.priority_id',
            'code AS priority_code');
        return $this;
    }

    /**
     * Add assignee information to the ticket
     * @return $this
     */
    public function innerJoinUser()
    {
        $this->getSelect()->joinLeft(
            ['au' => $this->getTable('admin_user')],
            'au.user_id = main_table.assignee_id',
            [
                'assignee_firstname' => 'au.firstname',
                'assignee_lastname' => 'au.lastname',
                'assignee_email' => 'au.email'
            ]
        );
        return $this;
    }

    /**
     * Add assignee information to the ticket
     * @return $this
     */
    public function excludeDeleted()
    {
        $this->getSelect()->where('deleted = false');
        return $this;
    }

    /**
     * Add priority and status code to the ticket models.
     * @return $this
     */
    protected function _beforeLoad()
    {
        $this
            ->innerJoinPriority()
            ->innerJoinStatus()
            ->innerJoinUser()
            ->excludeDeleted();

        return parent::_beforeLoad();
    }

    /**
     * Trigger after load for each record
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->walk('afterload');
        return parent::_afterLoad();
    }

    /**
     * Get result sorted ids
     *
     * @return array
     */
    public function getResultingIds()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Select::LIMIT_COUNT);
        $idsSelect->reset(Select::LIMIT_OFFSET);
        $idsSelect->reset(Select::COLUMNS);
        $idsSelect->reset(Select::ORDER);
        $idsSelect->columns('ticket_id');
        return $this->getConnection()->fetchCol($idsSelect);
    }

    /**
     * Updates the collection with an array of \Cart2Quote\Desk\Api\Data\TicketInterface
     *
     * @param \Magento\Framework\Api\SearchResultsInterface $resultsInterface
     * @return $this
     */
    public function loadByResultsInterface(\Magento\Framework\Api\SearchResultsInterface $resultsInterface)
    {
        foreach ($resultsInterface->getItems() as $itemData) {
            if ($itemData instanceof \Cart2Quote\Desk\Api\Data\TicketInterface) {
                $item = $this->getNewEmptyItem();
                $item->updateData($itemData);
                $this->_addItem($item);
            }
        }
        if (count($this->_items)) {
            $this->_setIsLoaded(true);
        }
        return $this;
    }
}
