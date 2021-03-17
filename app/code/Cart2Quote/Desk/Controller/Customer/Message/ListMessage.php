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

namespace Cart2Quote\Desk\Controller\Customer\Message;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Symfony\Component\Config\Definition\Exception\Exception;
use Magento\Customer\Model\Session;

/**
 * Class ListMessage
 */
class ListMessage extends \Cart2Quote\Desk\Controller\Customer\Customer
{
    const LIST_MESSAGE_BLOCK = 'customer_ticket_view_message';
    const LIST_MESSAGE_HANDLE = 'desk_customer_message_listmessage';

    /**
     * JSON factory
     *
     * @var \Magento\Framework\Controller\Result\Json
     */
    protected $_resultJson;

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
     * @var \Magento\Framework\Api\Search\FilterGroupFactory
     */
    protected $_filterGroupFactory;

    /**
     * API filter
     *
     * @var \Magento\Framework\Api\FilterFactory
     */
    protected $_filterFactory;

    /**
     * Array of messages
     *
     * @var \Cart2Quote\Desk\Api\Data\MessageInterface[]
     */
    protected $_messages = [];

    /**
     * Class ListMessage constructor
     *
     * @param \Magento\Framework\Controller\Result\Json $resultJson
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @param \Magento\Framework\Api\Search\FilterGroupFactory $filterGroupFactory
     * @param \Magento\Framework\Api\FilterFactory $filterFactory
     * @param \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Cart2Quote\Desk\Helper\Data $dataHelper
     * @param Session $customerSession
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\Json $resultJson,
        \Magento\Framework\Api\SearchCriteria $searchCriteria,
        \Magento\Framework\Api\Search\FilterGroupFactory $filterGroupFactory,
        \Magento\Framework\Api\FilterFactory $filterFactory,
        \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Cart2Quote\Desk\Helper\Data $dataHelper,
        Session $customerSession,
        Context $context
    ) {
        $this->_resultJson = $resultJson;
        $this->_searchCriteria = $searchCriteria;
        $this->_filterGroupFactory = $filterGroupFactory;
        $this->_filterFactory = $filterFactory;
        $this->_messageRepositoryInterface = $messageRepositoryInterface;
        $this->_currentCustomer = $currentCustomer;
        parent::__construct($context, $customerSession, $dataHelper);
    }

    /**
     * Get ticket messages
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $response = [];
        try {
            $ticketId = $this->getRequest()->getParam('id');
            $currentMessagesCount = $this->getRequest()->getParam('last_id', 0);
            $messages = $this->getMessages($ticketId);
            $lastId = $this->getLastId($messages);

            if ($lastId == $currentMessagesCount) {
                $response = ['html' => ''];
            } else {
                /** @var \Magento\Framework\View\Result\Page $resultPage */
                $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
                $layout = $resultPage->getLayout();

                $block = $layout->getBlock(self::LIST_MESSAGE_BLOCK);
                if ($block) {
                    $returnHtml = $block->setMessage(reset($messages))->toHtml();
                } else {
                    throw new Exception(
                        __("The \"%s\" block is not set for the handle \"%s\".",
                            self::LIST_MESSAGE_BLOCK,
                            self::LIST_MESSAGE_HANDLE)
                    );
                }
                $response = ['html' => $returnHtml, 'lastId' => $lastId];
            }
        }catch(\Exception $e){
            // todo: log exception
        }

        return $this->_resultJson->setHttpResponseCode(200)->setData($response);
    }

    /**
     * Get a list of messages by Ticket Id
     *
     * @param int $ticketId
     * @return \Cart2Quote\Desk\Api\Data\MessageInterface[]
     */
    public function getMessages($ticketId)
    {
        if (!$this->_messages && $ticketId) {
            $ticketFilter = $this->_filterFactory->create()->setField('ticket_id')->setValue($ticketId);
            $privateFilter = $this->_filterFactory->create()->setField('is_private')->setValue(0);

            $filterGroupTicketId = $this->_filterGroupFactory->create()->setFilters([$ticketFilter]);
            $filterGroupIsPrivate = $this->_filterGroupFactory->create()->setFilters([$privateFilter]);

            $this->_searchCriteria->setFilterGroups([$filterGroupTicketId, $filterGroupIsPrivate]);
            $this->_messages = $this->_messageRepositoryInterface->getList($this->_searchCriteria);
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
