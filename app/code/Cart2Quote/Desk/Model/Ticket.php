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

namespace Cart2Quote\Desk\Model;

use Cart2Quote\Desk\Model\Ticket\Priority;
use Cart2Quote\Desk\Model\Ticket\Status;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Customer;
use Magento\User\Model\User;

/**
 * Ticket model
 *
 * Magic setters
 * @method setStatusId(int $id)
 * @method setCustomerId(int $id)
 * @method setAssigneeId(int $id)
 * @method setPriorityId(int $id)
 * @method setSubject(string $subject)
 *
 * Magic getters
 * @method int getStatusId()
 * @method int getCustomerId()
 * @method int getAssigneeId()
 * @method int getPriorityId()
 * @method String getSubject()
 * @method String getPriorityCode()
 * @method String getStatus()
 *
 */
class Ticket extends \Magento\Framework\Model\AbstractModel
{
    const TICKET_GRID_INDEXER_ID = 'ticket_grid';

    /**
     * Cache tag
     */
    const CACHE_TAG = 'ticket_block';

    /**
     * Customer Model
     *
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * User Model
     *
     * @var \Magento\User\Model\User
     */
    protected $_user;

    /**
     * Priority Model
     *
     * @var Priority
     */
    protected $_priority;

    /**
     * Priority Model
     *
     * @var Status
     */
    protected $_status;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Cart2Quote helper
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_helper;

    /**
     * Customer Repository Interface
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $_customerRepositoryInterface;

    /**
     * Customer Name Api
     *
     * @var \Magento\Customer\Api\CustomerNameGenerationInterface
     */
    protected $_customerName;

    /**
     * Ticket Interface Factory
     *
     * @var \Cart2Quote\Desk\Api\Data\TicketInterfaceFactory
     */
    protected $_ticketInterfaceFactory;

    /**
     * Data Object Processor
     *
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $_dataObjectProcessor;

    /**
     * Data Object Helper
     *
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $_dataObjectHelper;

    /**
     * Class Ticket constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Ticket|\Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param ResourceModel\Ticket\Collection|\Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Cart2Quote\Desk\Helper\Data $helper
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Magento\Customer\Api\CustomerNameGenerationInterface $customerName
     * @param \Cart2Quote\Desk\Api\Data\TicketInterfaceFactory $ticketInterfaceFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param Customer $customer
     * @param User $user
     * @param Priority $priority
     * @param Status $status
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Cart2Quote\Desk\Model\ResourceModel\Ticket $resource,
        \Cart2Quote\Desk\Model\ResourceModel\Ticket\Collection $resourceCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Cart2Quote\Desk\Helper\Data $helper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Customer\Api\CustomerNameGenerationInterface $customerName,
        \Cart2Quote\Desk\Api\Data\TicketInterfaceFactory $ticketInterfaceFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        Customer $customer,
        User $user,
        Priority $priority,
        Status $status,
        array $data = []
    ) {
        $this->_helper = $helper;
        $this->_customer = $customer;
        $this->_user = $user;
        $this->_priority = $priority;
        $this->_status = $status;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_customerName = $customerName;
        $this->_ticketInterfaceFactory = $ticketInterfaceFactory;
        $this->_dataObjectHelper = $dataObjectHelper;
        $this->_dataObjectProcessor = $dataObjectProcessor;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cart2Quote\Desk\Model\ResourceModel\Ticket');
    }

    /**
     * Get the customer
     *
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->_customer;
    }

    /**
     * Return current store id
     *
     * @return int
     */
    public function getStoreId()
    {
        if ($this->getData('store_id') === null) {
            $this->setStoreId($this->_storeManager->getStore()->getId());
        }
        return $this->getData('store_id');
    }

    /**
     * Load the status on the ticket
     *
     * @return $this
     */
    public function loadStatus()
    {
        $status = $this->_status->load($this->getStatusId());
        $this->setStatus($status->getCode());

        return $this;
    }

    /**
     * Load the priority on the ticket
     *
     * @return $this
     */
    public function loadPriority()
    {
        $priority = $this->_priority->load($this->getPriorityId());
        $this->setPriority($priority->getCode());

        return $this;
    }

    /**
     * Get status text formatted for HTML.
     * @return string
     */
    public function getStatusHtml()
    {
        return $this->getLabelHtml('status');
    }

    /**
     * Get priority text formatted for HTML.
     * @return string
     */
    public function getPriorityHtml()
    {
        return $this->getLabelHtml('priority');
    }

    /**
     * Get subject in html format
     *
     * @return string
     */
    public function getSubjectHtml()
    {
        return $this->_helper->escapeHtml($this->getSubject());
    }

    /**
     * Get formatted label for HTML
     * @param string $field
     *
     * @return string
     */
    public function getLabelHtml($field)
    {
        $html = "";
        if ($fieldValue = $this->getData($field)) {
            $html = $this->_helper->getLabelHtml($fieldValue);
        }
        return $html;
    }

    /**
     * Loads the full customer name if customer id is available.
     *
     * @return $this
     */
    public function loadCustomerName()
    {
        if ($this->getCustomerId()) {
            $customer = $this->_customerRepositoryInterface->getById($this->getCustomerId());
            $name = $this->_customerName->getCustomerName($customer);
            $this->setCustomerName($name);
            $this->setCustomerEmail($customer->getEmail());
        }
        return $this;
    }

    /**
     * Loads the user name if user id is available.
     *
     * @return $this
     */
    public function loadAssignee()
    {
        if ($this->getAssigneeId()) {
            if (!($this->getAssigneeFirstname() && $this->getAssigneeLastname())) {
                $this->_user->load($this->getAssigneeId());
                $this->setAssigneeFirstname($this->_user->getFirstName());
                $this->setAssigneeLastname($this->_user->getLastName());
                $this->setAssigneeEmail($this->_user->getEmail());
            }
            $this->setAssigneeName($this->getAssigneeFirstname() . ' ' . $this->getAssigneeLastname());
        }

        return $this;
    }

    /**
     * Retrieve ticket model with ticket data
     *
     * @return \Cart2Quote\Desk\Api\Data\TicketInterface
     */
    public function getDataModel()
    {
        $messageData = $this->getData();
        $ticketDataObject = $this->_ticketInterfaceFactory->create();
        $this->_dataObjectHelper->populateWithArray(
            $ticketDataObject,
            $messageData,
            '\Cart2Quote\Desk\Api\Data\TicketInterface'
        );
        $ticketDataObject->setId($this->getId());
        return $ticketDataObject;
    }

    /**
     * Update ticket data
     *
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     * @return $this
     */
    public function updateData(\Cart2Quote\Desk\Api\Data\TicketInterface $ticket)
    {
        $ticketDataAttributes = $this->_dataObjectProcessor->buildOutputDataArray(
            $ticket,
            '\Cart2Quote\Desk\Api\Data\TicketInterface'
        );

        foreach ($ticketDataAttributes as $attributeCode => $attributeData) {
            $this->setDataUsingMethod($attributeCode, $attributeData);
        }

        $customAttributes = $ticket->getCustomAttributes();
        if ($customAttributes !== null) {
            foreach ($customAttributes as $attribute) {
                $this->setDataUsingMethod($attribute->getAttributeCode(), $attribute->getValue());
            }
        }

        $messageId = $ticket->getId();
        if ($messageId) {
            $this->setId($messageId);
        }

        return $this;
    }

    /**
     * Load Assignee, Customer Name, Priority and Status
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->loadAssignee();
        $this->loadCustomerName();
        $this->loadPriority();
        $this->loadStatus();
        return parent::_afterLoad();
    }
}