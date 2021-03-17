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

namespace Cart2Quote\Desk\Model\ResourceModel;

use Cart2Quote\Desk\Api\Data\TicketInterface;
use Cart2Quote\Desk\Api\Data\TicketSearchResultsInterfaceFactory;
use Cart2Quote\Desk\Model\ResourceModel\Ticket;
use Cart2Quote\Desk\Model\ResourceModel\Ticket\Collection;
use Cart2Quote\Desk\Model\TicketFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\InputException;

/**
 * Ticket repository.
 */
class TicketRepository implements \Cart2Quote\Desk\Api\TicketRepositoryInterface
{
    /**
     * Ticket Factory
     *
     * @var TicketFactory
     */
    protected $_ticketFactory;

    /**
     * Ticket Resource Model
     *
     * @var \Magento\Customer\Model\ResourceModel\Customer
     */
    protected $_ticketResourceModel;

    /**
     * Search Results Factory
     *
     * @var TicketSearchResultsInterfaceFactory
     */
    protected $_searchResultsFactory;

    /**
     * Ticket Collection
     *
     * @var Collection
     */
    protected $_ticketCollection;

    /**
     * Status Resource Model
     *
     * @var \Cart2Quote\Desk\Model\ResourceModel\Ticket\Status
     */
    protected $_statusResourceModel;

    /**
     * Priority Resource Model
     *
     * @var \Cart2Quote\Desk\Model\ResourceModel\Ticket\Priority
     */
    protected $_priorityResourceModel;

    /**
     * Status Factory
     *
     * @var \Cart2Quote\Desk\Model\Ticket\StatusFactory
     */
    protected $_statusFactory;

    /**
     * Cart2Quote Data Helper
     *
     * @var \Cart2Quote\Desk\Helper\Data
     */
    protected $_dataHelper;

    /**
     * TicketRepository constructor
     *
     * @param TicketFactory $ticketFactory
     * @param Ticket $ticketResourceModel
     * @param TicketSearchResultsInterfaceFactory $searchResultsFactory
     * @param Collection $ticketCollection
     * @param \Cart2Quote\Desk\Model\ResourceModel\Ticket\Status $statusResourceModel
     * @param \Cart2Quote\Desk\Model\ResourceModel\Ticket\Priority $priorityResourceModel
     * @param \Cart2Quote\Desk\Model\Ticket\StatusFactory $statusFactory
     * @param \Cart2Quote\Desk\Helper\Data $dataHelper
     */
    public function __construct(
        TicketFactory $ticketFactory,
        Ticket $ticketResourceModel,
        TicketSearchResultsInterfaceFactory $searchResultsFactory,
        Collection $ticketCollection,
        \Cart2Quote\Desk\Model\ResourceModel\Ticket\Status $statusResourceModel,
        \Cart2Quote\Desk\Model\ResourceModel\Ticket\Priority $priorityResourceModel,
        \Cart2Quote\Desk\Model\Ticket\StatusFactory $statusFactory,
        \Cart2Quote\Desk\Helper\Data $dataHelper
    ) {
        $this->_ticketFactory = $ticketFactory;
        $this->_ticketResourceModel = $ticketResourceModel;
        $this->_ticketCollection = $ticketCollection;
        $this->_searchResultsFactory = $searchResultsFactory;
        $this->_statusResourceModel = $statusResourceModel;
        $this->_priorityResourceModel = $priorityResourceModel;
        $this->_statusFactory = $statusFactory;
        $this->_dataHelper = $dataHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function save(TicketInterface $ticket)
    {
        $ticket = $this->_addPriority($ticket);
        $ticket = $this->_addStatus($ticket);
        $this->validate($ticket);

        $ticketModel = $this->_ticketFactory->create();
        $ticketModel
            ->setId($ticket->getId())
            ->setStatusId($ticket->getStatusId())
            ->setCustomerId($ticket->getCustomerId())
            ->setPriorityId($ticket->getPriorityId())
            ->setAssigneeId($ticket->getAssigneeId())
            ->setStoreId($ticket->getStoreId())
            ->setSubject($ticket->getSubject())
            ->setDeleted($ticket->getDeleted());

        $this->_ticketResourceModel->save($ticketModel);
        $ticketModel->afterLoad();
        $ticket = $ticketModel->getDataModel();
        return $ticket;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($ticketId)
    {
        $ticketModel = $this->_ticketFactory->create();
        $this->_ticketResourceModel->load($ticketModel, $ticketId);
        $ticketModel->afterLoad();
        return $ticketModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->_searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $collection = $this->_ticketCollection;

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
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $tickets = [];
        /** @var \Cart2Quote\Desk\Model\Ticket $ticketModel */
        foreach ($collection as $ticketModel) {
            $tickets[] = $ticketModel->getDataModel();
        }
        $searchResults->setItems($tickets);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(TicketInterface $ticket)
    {
        return $this->deleteById($ticket->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($ticketId)
    {
        $ticket = $this->getById($ticketId);
        $ticket->setDeleted(1);
        $this->save($ticket);
        return true;
    }

    /**
     * Validate ticket attribute values.
     *
     * @param TicketInterface $ticket
     * @throws InputException
     * @throws \Exception
     * @throws \Zend_Validate_Exception
     *
     * @return void
     */
    protected function validate(TicketInterface $ticket)
    {
        $exception = new InputException();

        if (!\Zend_Validate::is(trim($ticket->getSubject()), 'NotEmpty')) {
            $exception->addError(__(InputException::REQUIRED_FIELD, ['fieldName' => 'subject']));
        }

        if (!\Zend_Validate::is(trim($ticket->getCustomerId()), 'NotEmpty')) {
            $exception->addError(__(InputException::REQUIRED_FIELD, ['fieldName' => 'customer_id']));
        }

        if (!\Zend_Validate::is(trim($ticket->getStatusId()), 'NotEmpty')) {
            $exception->addError(__(InputException::REQUIRED_FIELD, ['fieldName' => 'status_id']));
        }

        if (!\Zend_Validate::is(trim($ticket->getPriorityId()), 'NotEmpty')) {
            $exception->addError(__(InputException::REQUIRED_FIELD, ['fieldName' => 'priority_id']));
        }

        if (!\Zend_Validate::is(trim($ticket->getStoreId()), 'NotEmpty')) {
            $exception->addError(__(InputException::REQUIRED_FIELD, ['fieldName' => 'store_id']));
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

    /**
     * Create priority if empty
     *
     * @param TicketInterface $ticket
     * @return TicketInterface
     * @throws \Exception
     * @throws \Zend_Validate_Exception
     */
    protected function _addPriority(TicketInterface $ticket)
    {
        if (!\Zend_Validate::is(trim($ticket->getPriorityId()), 'NotEmpty')) {
            $ticket->setPriorityId($this->_dataHelper->getDefaultPriority());
        }
        return $ticket;
    }

    /**
     * Create status if empty
     *
     * @param TicketInterface $ticket
     * @return TicketInterface
     * @throws \Exception
     * @throws \Zend_Validate_Exception
     */
    protected function _addStatus(TicketInterface $ticket)
    {
        if (!\Zend_Validate::is(trim($ticket->getStatusId()), 'NotEmpty')) {
            $status = $this->_statusFactory->create();
            $this->_statusResourceModel->loadByCode($status, 'open');
            $ticket->setStatusId($status->getId());
        }
        return $ticket;
    }
}
