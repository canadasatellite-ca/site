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

namespace Cart2Quote\Desk\Block\Adminhtml\Edit\Container;

/**
 * Class Messages
 */
class Messages extends \Magento\Backend\Block\Template
{
    /**
     * Message model factory
     *
     * @var \Cart2Quote\Desk\Api\MessageRepositoryInterface
     */
    protected $_messageRepositoryInterface;

    /**
     * Ticket model factory
     *
     * @var \Cart2Quote\Desk\Model\Ticket\MessageFactory
     */
    protected $_messageModelFactory;

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
     * API filter
     *
     * @var \Magento\Framework\Api\Filter
     */
    protected $_filter;

    /**
     * Messages
     *
     * @var \Cart2Quote\Desk\Api\Data\MessageInterface[]
     */
    protected $_messages = [];

    /**
     * Class Messages constructor
     *
     * @param \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface
     * @param \Cart2Quote\Desk\Model\Ticket\MessageFactory $messageModelFactory
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magento\Framework\Api\Filter $filter
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface,
        \Cart2Quote\Desk\Model\Ticket\MessageFactory $messageModelFactory,
        \Magento\Framework\Api\SearchCriteria $searchCriteria,
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Framework\Api\Filter $filter,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->_messageRepositoryInterface = $messageRepositoryInterface;
        $this->_messageModelFactory = $messageModelFactory;
        $this->_searchCriteria = $searchCriteria;
        $this->_filterGroup = $filterGroup;
        $this->_filter = $filter;

        parent::__construct($context, $data);
    }

    /**
     * Get an array of messages
     *
     * @return \Magento\Customer\Api\Data\CustomerSearchResultsInterface[]
     */
    public function getMessages()
    {
        if (!$this->_messages && $this->getTicket()) {
            $this->_filter->setField('ticket_id')->setValue($this->getTicket()->getId());
            $this->_filterGroup->setFilters([$this->_filter]);
            $this->_searchCriteria->setFilterGroups([$this->_filterGroup]);
            $this->_messages = $this->_messageRepositoryInterface->getList($this->_searchCriteria);
        }

        return $this->_messages;
    }

    /**
     * Get the ticket
     *
     * @return \Cart2Quote\Desk\Model\Ticket
     */
    public function getTicket()
    {
        return $this->getParentBlock()->getTicket();
    }

    /**
     * Get the ticket ID
     *
     * @return int
     */
    public function getTicketId(){
        $ticketId = 0;
        if ($this->getTicket()) {
            $ticketId = $this->getTicket()->getTicketId();
        }
        return $ticketId;
    }

    /**
     * Get the AJAX update message URL
     *
     * @return string
     */
    public function getAjaxUpdateMessagesUrl()
    {
        $url =  $this->getUrl(
            '*/*/listmessage/',
            [
                'id' => $this->getTicketId()
            ]
        );
        return $url;
    }

    /**
     * Get the last message ID
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
