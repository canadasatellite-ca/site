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

namespace Cart2Quote\Desk\Controller\Adminhtml\Ticket;

use Magento\Framework\Controller\ResultFactory;
use Symfony\Component\Config\Definition\Exception\Exception;

class ListMessage extends \Magento\Backend\App\Action
{
    const LIST_MESSAGE_BLOCK = 'ticket.edit.container.messages.message';
    const LIST_MESSAGE_HANDLE = 'desk_ticket_listmessage';

    /**
     * JSON factory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * Cart2Quote ticket message
     *
     * @var \Cart2Quote\Desk\Api\MessageRepositoryInterface
     */
    protected $_messageRepositoryInterface;

    /**
     * Current Customer
     *
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $_currentCustomer;

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
     * Array of messages
     *
     * @var \Cart2Quote\Desk\Api\Data\MessageInterface[]
     */
    protected $_messages;

    /**
     * Class ListMessage constructor
     *
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magento\Framework\Api\Filter $filter
     * @param \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Api\SearchCriteria $searchCriteria,
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Framework\Api\Filter $filter,
        \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_searchCriteria = $searchCriteria;
        $this->_filterGroup = $filterGroup;
        $this->_filter = $filter;
        $this->_messageRepositoryInterface = $messageRepositoryInterface;
        $this->_currentCustomer = $currentCustomer;
        parent::__construct($context);
    }

    /**
     * Render my messages
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $ticketId = $this->getRequest()->getParam('id');
        $currentMessagesCount = $this->getRequest()->getParam('last_id', 0);
        $messages = $this->getMessages($ticketId);
        $lastId = $this->getLastId($messages);

        if ($lastId == $currentMessagesCount) {
            $response = ['html' => ''];
        } else {
            $layout = $resultPage->getLayout();
            $returnHtml = '';
            $block = $layout->getBlock(self::LIST_MESSAGE_BLOCK);
            if ($block) {
                if (count($messages)) {
                    $returnHtml .= $block->setIsNew(true)->setMessage(reset($messages))->toHtml();
                }
            } else {
                throw new Exception(
                    __("The \"%s\" block is not set for the handle \"%s\".",
                        self::LIST_MESSAGE_BLOCK,
                        self::LIST_MESSAGE_HANDLE)
                );
            }
            $response = ['html' => $returnHtml, 'lastId' => $lastId, 'ticketId' => $ticketId];
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->_resultJsonFactory->create();
        return $resultJson->setHttpResponseCode(200)->setData($response);
    }

    /**
     * Get the messages by ticket ID
     *
     * @param int $ticketId
     *
     * @return \Cart2Quote\Desk\Api\Data\MessageInterface[]
     */
    public function getMessages($ticketId)
    {
        if (!$this->_messages && $ticketId) {
            $this->_filter->setField('ticket_id')->setValue($ticketId);
            $this->_filterGroup->setFilters([$this->_filter]);
            $this->_searchCriteria->setFilterGroups([$this->_filterGroup]);
            $this->_messages = $this->_messageRepositoryInterface->getList($this->_searchCriteria);
        }

        if (!is_array($this->_messages)) {
            $this->_messages = array();
        }

        return $this->_messages;
    }

    /**
     * Get the last message ID
     *
     * @param array $messages
     * @return bool|int
     */
    public function getLastId(array $messages)
    {
        /** @var \Cart2Quote\Desk\Api\Data\MessageInterface $firstMessage */
        $firstMessage = reset($messages);
        if ($firstMessage) {
            return $firstMessage->getId();
        } else {
            return false;
        }
    }
}
