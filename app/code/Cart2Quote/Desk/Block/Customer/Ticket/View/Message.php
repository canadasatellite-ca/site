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

namespace Cart2Quote\Desk\Block\Customer\Ticket\View;

use Magento\Catalog\Model\Product;
use Cart2Quote\Desk\Model\Ticket;

/**
 * Cart2Quote ticket detailed view block
 */
class Message extends \Cart2Quote\Desk\Block\Customer\Ticket\View
{
    /**
     * Ticket model factory
     *
     * @var \Cart2Quote\Desk\Api\TicketRepositoryInterface
     */
    protected $_ticketRepositoryInterface;

    /**
     * Search criteria
     *
     * @var \Magento\Framework\Api\SearchCriteria
     */
    protected $_searchCriteria;

    /**
     * Filter group
     *
     * @var \Magento\Framework\Api\Search\FilterGroupFactory
     */
    protected $_filterGroupFactory;

    /**
     * Filter Factory
     *
     * @var \Magento\Framework\Api\FilterFactory
     */
    protected $_filterFactory;

    /**
     * Ticket model factory
     *
     * @var \Cart2Quote\Desk\Model\Ticket\MessageFactory
     */
    protected $_messageModelFactory;

    /**
     * List of Messages
     *
     * @var \Cart2Quote\Desk\Api\Data\MessageInterface[]
     */
    protected $_messages = [];

    /**
     * Class Message constructor
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface
     * @param \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @param \Magento\Framework\Api\Search\FilterGroupFactory $filterGroupFactory
     * @param \Magento\Framework\Api\FilterFactory $filterFactory
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Cart2Quote\Desk\Model\Ticket\MessageFactory $messageModelFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface,
        \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface,
        \Cart2Quote\Desk\Api\Data\TicketInterface $ticket,
        \Magento\Framework\Api\SearchCriteria $searchCriteria,
        \Magento\Framework\Api\Search\FilterGroupFactory $filterGroupFactory,
        \Magento\Framework\Api\FilterFactory $filterFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Cart2Quote\Desk\Model\Ticket\MessageFactory $messageModelFactory,
        array $data = []
    ) {
        $this->_messageModelFactory = $messageModelFactory;
        parent::__construct(
            $context,
            $ticketRepositoryInterface,
            $messageRepositoryInterface,
            $ticket,
            $searchCriteria,
            $filterGroupFactory,
            $filterFactory,
            $currentCustomer,
            $data);
    }

    /**
     * Get the list of messages by ticket ID
     *
     * @return \Magento\Customer\Api\Data\CustomerSearchResultsInterface[]
     */
    public function getMessages()
    {
        if (!$this->_messages && $this->getTicketId()) {
            $ticketFilter = $this->_filterFactory->create()->setField('ticket_id')->setValue($this->getTicketId());
            $privateFilter = $this->_filterFactory->create()->setField('is_private')->setValue(0);

            $filterGroupTicketId = $this->_filterGroupFactory->create()->setFilters([$ticketFilter]);
            $filterGroupIsPrivate = $this->_filterGroupFactory->create()->setFilters([$privateFilter]);

            $this->_searchCriteria->setFilterGroups([$filterGroupTicketId, $filterGroupIsPrivate]);
            $this->_messages = $this->_messageRepositoryInterface->getList($this->_searchCriteria);
        }

        return $this->_messages;
    }

    /**
     * Get the message model
     *
     * @param \Cart2Quote\Desk\Api\Data\MessageInterface $message
     * @return $this
     */
    public function getModel(\Cart2Quote\Desk\Api\Data\MessageInterface $message)
    {
        return $this->_messageModelFactory->create()->updateData($message);
    }

    /**
     * Combine all the classes
     *
     * @param int $key
     * @return string
     */
    public function getTicketDetailClass($key)
    {
        $class = '';
        $class .= $this->getFirstClass($key);
        $class .= $this->getLastClass($key);
        if (empty($class)) {
            $class .= $this->getDefaultClass();
        }
        $class .= ' '.$this->getOwnerClass();
        return $class;
    }

    /**
     * Get the default message CSS class
     *
     * @return string
     */
    public function getDefaultClass()
    {
        return 'message-details';
    }

    /**
     * Get the CSS class of the first message
     *
     * @param int $key
     * @return string
     */
    public function getFirstClass($key)
    {
        $class = '';
        if ($key == 0) {
            $class = 'message-details-first';
        }
        return $class;
    }

    /**
     * Get the CSS class of the last message
     *
     * @param int $key
     * @return string
     */
    public function getLastClass($key)
    {
        $class = '';
        $messages = $this->getMessages();
        if ($key == count($messages) - 1) {
            $class = 'message-details-last';
        }
        return $class;
    }

    /**
     * Get the CSS class of the owner (customer or admin)
     *
     * @return string
     */
    public function getOwnerClass()
    {
        $message = $this->getMessage();
        if ($message && $message->getUserId()) {
            $class = 'owner-admin';
        } else {
            $class = 'owner-customer';
        }
        return $class;
    }

    /**
     * Get the URL to update the ticket list
     *
     * @return string
     */
    public function getAjaxUpdateMessagesUrl()
    {
        return $this->getUrl('desk/customer_message/listmessage/');
    }

    /**
     * Get the last ID from the messages
     *
     * @return bool|int
     */
    public function getLastId()
    {
        $messages = $this->getMessages();
        /** @var \Cart2Quote\Desk\Api\Data\MessageInterface $firstMessage */
        $firstMessage = reset($messages);
        if ($firstMessage) {
            return $firstMessage->getId();
        } else {
            return false;
        }
    }
}
