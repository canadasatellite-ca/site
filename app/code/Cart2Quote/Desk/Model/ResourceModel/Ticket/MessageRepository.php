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

use Cart2Quote\Desk\Api\Data\MessageInterface;
use Cart2Quote\Desk\Api\Data\MessageSearchResultsInterfaceFactory;
use Cart2Quote\Desk\Model\ResourceModel\Ticket\Message;
use Cart2Quote\Desk\Model\Ticket\MessageFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\InputException;

/**
 * Ticket repository.
 */
class MessageRepository implements \Cart2Quote\Desk\Api\MessageRepositoryInterface
{
    /**
     * Message Factory
     *
     * @var MessageFactory
     */
    protected $_messageFactory;

    /**
     * Message Resource Model
     *
     * @var \Cart2Quote\Desk\Model\ResourceModel\Ticket\Message
     */
    protected $_messageResourceModel;

    /**
     * Search Result Factory
     *
     * @var MessageSearchResultsInterfaceFactory
     */
    protected $_searchResultsFactory;

    /**
     * Message Collection
     *
     * @var \Cart2Quote\Desk\Model\ResourceModel\Ticket\Message\Collection
     */
    protected $_messageCollection;

    /**
     * Class MessageRepository constructor
     *
     * @param MessageFactory $messageFactory
     * @param Message $messageResourceModel
     * @param MessageSearchResultsInterfaceFactory $searchResultsFactory
     * @param Collection|Message\Collection $messageCollection
     */
    public function __construct(
        MessageFactory $messageFactory,
        Message $messageResourceModel,
        MessageSearchResultsInterfaceFactory $searchResultsFactory,
        \Cart2Quote\Desk\Model\ResourceModel\Ticket\Message\Collection $messageCollection
    ) {
        $this->_messageFactory = $messageFactory;
        $this->_messageResourceModel = $messageResourceModel;
        $this->_messageCollection = $messageCollection;
        $this->_searchResultsFactory = $searchResultsFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(MessageInterface $message)
    {
        $this->validate($message);
        $messageModel = $this->_messageFactory->create();
        $messageModel->updateData($message);
        $this->_messageResourceModel->save($messageModel);
        $messageModel->loadName();
        $messageModel->loadEmail();
        return $messageModel->getDataModel()->setId($messageModel->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getById($ticketId)
    {
        $ticket = $this->_messageFactory->create();
        $this->_messageResourceModel->load($ticket, $ticketId);
        return $ticket->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->_searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $collection = $this->_messageCollection;

        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        } else {
            $collection->addOrder(\Cart2Quote\Desk\Api\Data\MessageInterface::CREATED_AT, 'DESC');
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $messages = [];

        /** @var \Cart2Quote\Desk\Model\Ticket\Message $messageModel */
        foreach ($collection as $messageModel) {
            $messageModel->loadName();
            $messageModel->loadEmail();
            $messages[] = $messageModel->getDataModel();
        }
        return $messages;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(MessageInterface $message)
    {
        return $this->deleteById($message->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($messageId)
    {
        $message = $this->_messageFactory->create();
        $this->_messageResourceModel->delete($message);
        return true;
    }

    /**
     * Validate ticket attribute values.
     *
     * @param MessageInterface $message
     * @throws InputException
     * @throws \Exception
     * @throws \Zend_Validate_Exception
     *
     * @return void
     */
    protected function validate(MessageInterface $message)
    {
        $exception = new InputException();
        /**
         * Check message
         */
        if (!\Zend_Validate::is(trim($message->getMessage()), 'NotEmpty')) {
            $exception->addError(__(InputException::REQUIRED_FIELD, ['fieldName' => 'message']));
        }

        /**
         * Check customer id and user id is both not set.
         * One of them needs to be set to make sure who is sending the message.
         */
        if (!\Zend_Validate::is(trim($message->getCustomerId()), 'NotEmpty') &&
            !\Zend_Validate::is(trim($message->getUserId()), 'NotEmpty')) {
            $exception->addError(
                __("%fieldName1 or %fieldName2 needs to be set, you cannot them both unset.",
                ['fieldName1' => 'customer_id', 'fieldName2' => 'user_id'])
            );
        }

        /**
         * Check customer id and user id if both is set
         * A message cannot be submitted by a admin user or a customer at once. It is send by one of them.
         */
        if (\Zend_Validate::is(trim($message->getCustomerId()), 'NotEmpty') &&
            \Zend_Validate::is(trim($message->getUserId()), 'NotEmpty')) {
            $exception->addError(
                __("%fieldName1 or %fieldName2 cannot be both set, " .
                    "you need to set only the %fieldName1 or %fieldName2.",
                    ['fieldName1' => 'customer_id', 'fieldName2' => 'user_id'])
            );
        }

        /**
         * Check ticket id
         */
        if (!\Zend_Validate::is(trim($message->getTicketId()), 'NotEmpty')) {
            $exception->addError(__(InputException::REQUIRED_FIELD, ['fieldName' => 'ticket_id']));
        }

        if ($exception->wasErrorAdded()) {
            throw $exception;
        }
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
     *
     * @return void
     */
    protected function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
    ) {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $field = $filter->getField();
            $value = $filter->getValue();
            if (isset($field) && isset($value)) {
                $conditionType = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = $field;
                $conditions[] = [$conditionType => $value];
            }
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
    }
}
