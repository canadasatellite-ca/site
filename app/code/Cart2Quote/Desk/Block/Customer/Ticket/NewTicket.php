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

namespace Cart2Quote\Desk\Block\Customer\Ticket;

use Magento\Catalog\Model\Product;
use Cart2Quote\Desk\Model\Ticket;

/**
 * New ticket block
 */
class NewTicket extends \Magento\Framework\View\Element\Template
{
    /**
     * Ticket model factory
     *
     * @var \Cart2Quote\Desk\Api\TicketRepositoryInterface
     */
    protected $_ticketRepositoryInterface;

    /**
     * Message model factory
     *
     * @var \Cart2Quote\Desk\Api\MessageRepositoryInterface
     */
    protected $_messageRepositoryInterface;

    /**
     * Search criteria
     *
     * @var \Magento\Framework\Api\SearchCriteria
     */
    protected $_searchCriteria;

    /**
     * Filter group
     *
     * @var \Magento\Framework\Api\Search\FilterGroup
     */
    protected $_filterGroup;

    /**
     * API Filter
     *
     * @var \Magento\Framework\Api\Filter
     */
    protected $_filter;

    /**
     * Current Customer
     *
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $_currentCustomer;

    /**
     * Ticket ID
     *
     * @var int
     */
    protected $_ticketId;

    /**
     * Class NewTicket constructor
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface
     * @param \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magento\Framework\Api\Filter $filter
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface,
        \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface,
        \Magento\Framework\Api\SearchCriteria $searchCriteria,
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Framework\Api\Filter $filter,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        array $data = []
    ) {
        $this->_ticketRepositoryInterface = $ticketRepositoryInterface;
        $this->_messageRepositoryInterface = $messageRepositoryInterface;
        $this->_searchCriteria = $searchCriteria;
        $this->_filterGroup = $filterGroup;
        $this->_filter = $filter;
        $this->_currentCustomer = $currentCustomer;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Initialize ticket id
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTicketId($this->getRequest()->getParam('id', false));
    }

    /**
     * Get ticket data
     *
     * @return \Cart2Quote\Desk\Api\Data\TicketInterface
     */
    public function getTicketData()
    {
        if ($this->getTicketId() && !$this->getTicketCachedData()) {
            $this->setTicketCachedData($this->_ticketRepositoryInterface->getById($this->getTicketId()));
        }
        return $this->getTicketCachedData();
    }

    /**
     * Return ticket customer url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('ticket/customer');
    }

    /**
     * Get formatted date
     *
     * @param string $date
     * @return string
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, \IntlDateFormatter::LONG);
    }

    /**
     * Get formatted time
     *
     * @param string $date
     * @return string
     */
    public function timeFormat($date)
    {
        return $this->formatTime($date);
    }

    /**
     * Block to HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->_currentCustomer->getCustomerId() ? parent::_toHtml() : '';
    }

    /**
     * Set the ticket ID
     *
     * @param int $id
     * @return $this
     */
    public function setTicketId($id)
    {
        $this->_ticketId = $id;
        return $this;
    }

    /**
     * Get the ticket ID
     *
     * @return int
     */
    public function getTicketId()
    {
        return $this->_ticketId;
    }

    /**
     * Get the assignee name. If no assignee is set then return unassigned.
     *
     * @return \Magento\Framework\Phrase|null|string
     */
    public function getAssignedTo()
    {
        $assigneeName = $this->getTicketData()->getAssigneeName();
        if (empty($assigneeName)) {
            return __("Unassigned");
        } else {
            return $assigneeName;
        }
    }

    /**
     * Get the formatted create date of the ticket.
     *
     * @return string
     */
    public function getCreatedAt()
    {
        $createdAt = $this->getTicketData()->getCreatedAt();
        return $this->dateFormat($createdAt).' '.$this->timeFormat($createdAt);
    }

    /**
     * Get the form URL
     *
     * @return string
     */
    public function getFormUrl()
    {
        return $this->getUrl('desk/customer/save');
    }
}
