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
 * @package     DeskEmail
 * @copyright   Copyright (c) 2016 Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */

namespace Cart2Quote\DeskEmail\Observer\Adminhtml;

use Cart2Quote\Desk\Api\Data\TicketInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class SaveMessageAfterObserver
 */
class SaveMessageAfterObserver implements ObserverInterface
{
    /**
     * Message Sender
     *
     * @var \Cart2Quote\DeskEmail\Model\Sender\MessageSender
     */
    protected $_messageSender;

    /**
     * Desk Helper
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_helper;

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
     * Message Repository Interface
     *
     * @var \Cart2Quote\Desk\Api\MessageRepositoryInterface
     */
    protected $_messageRepositoryInterface;

    /**
     * Class SaveMessageAfterObserver constructor
     *
     * @param \Cart2Quote\DeskEmail\Model\Sender\MessageSender $messageSender
     * @param \Magento\Backend\Helper\Data $helper
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @param \Magento\Framework\Api\Search\FilterGroupFactory $filterGroupFactory
     * @param \Magento\Framework\Api\FilterFactory $filterFactory
     * @param \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface
     */
    public function __construct(
        \Cart2Quote\DeskEmail\Model\Sender\MessageSender $messageSender,
        \Magento\Backend\Helper\Data $helper,
        \Magento\Framework\Api\SearchCriteria $searchCriteria,
        \Magento\Framework\Api\Search\FilterGroupFactory $filterGroupFactory,
        \Magento\Framework\Api\FilterFactory $filterFactory,
        \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface
    ) {
        $this->_messageRepositoryInterface = $messageRepositoryInterface;
        $this->_searchCriteria = $searchCriteria;
        $this->_filterGroupFactory = $filterGroupFactory;
        $this->_filterFactory = $filterFactory;
        $this->_messageSender = $messageSender;
        $this->_helper = $helper;
    }

    /**
     * Send message to the message receiver.
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $ticket = $observer->getEvent()->getTicket();
        $message = $observer->getEvent()->getMessage();

        if (count($this->getMessages($ticket)) > 1) {
            if ($message->getIsPrivate() == false) {
                $this->_messageSender->send($ticket, $message);
            }
        }

        return $this;
    }

    /**
     * Get the list of messages by ticket ID
     *
     * @param TicketInterface $ticket
     * @return \Magento\Customer\Api\Data\CustomerSearchResultsInterface[]
     */
    public function getMessages(TicketInterface $ticket)
    {
        $ticketFilter = $this->_filterFactory->create()->setField('ticket_id')->setValue($ticket->getId());
        $privateFilter = $this->_filterFactory->create()->setField('is_private')->setValue(0);

        $filterGroupTicketId = $this->_filterGroupFactory->create()->setFilters([$ticketFilter]);
        $filterGroupIsPrivate = $this->_filterGroupFactory->create()->setFilters([$privateFilter]);

        $this->_searchCriteria->setFilterGroups([$filterGroupTicketId, $filterGroupIsPrivate]);
        $list = $this->_messageRepositoryInterface->getList($this->_searchCriteria);

        return $list;
    }
}
