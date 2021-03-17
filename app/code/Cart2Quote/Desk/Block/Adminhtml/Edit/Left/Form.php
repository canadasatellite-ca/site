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

/**
 * Adminhtml Cart2Quote Edit Form
 */
namespace Cart2Quote\Desk\Block\Adminhtml\Edit\Left;

/**
 * Class Form
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Ticket
     *
     * @var \Cart2Quote\Desk\Model\Ticket
     */
    protected $_ticket = null;

    /**
     * FieldSet
     *
     * @var \Magento\Framework\Data\Form\Element\Fieldset
     */
    protected $_fieldset = null;

    /**
     * Data Helper
     *
     * @var \Cart2Quote\Desk\Helper\Data
     */
    protected $_dataHelper = null;

    /**
     * Customer Repository
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $_customerRepository;

    /**
     * Search criteria
     *
     * @var \Magento\Framework\Api\SearchCriteria
     */
    protected $_searchCriteria;

    /**
     * Sort Order Builder
     *
     * @var \Magento\Framework\Api\SortOrderBuilder
     */
    protected $_sortOrderBuilder;

    /**
     * Catalog product factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * Desk system store model
     *
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * Priority data
     *
     * @var \Cart2Quote\Desk\Model\ResourceModel\Ticket\Status\Collection
     */
    protected $_priorityCollection = null;

    /**
     * User Collection
     *
     * @var \Magento\User\Model\ResourceModel\User\Collection
     */
    protected $_userCollection = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Customer name generator
     *
     * @var \Magento\Customer\Api\CustomerNameGenerationInterface
     */
    protected $_customerNameGenerationInterface;

    /**
     * Class Form constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @param \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Api\CustomerNameGenerationInterface $customerNameGenerationInterface
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Cart2Quote\Desk\Helper\Data $dataHelper
     * @param \Cart2Quote\Desk\Model\ResourceModel\Ticket\Priority\Collection $priorityCollection
     * @param \Magento\User\Model\ResourceModel\User\Collection $userCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\SearchCriteria $searchCriteria,
        \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\CustomerNameGenerationInterface $customerNameGenerationInterface,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Cart2Quote\Desk\Helper\Data $dataHelper,
        \Cart2Quote\Desk\Model\ResourceModel\Ticket\Priority\Collection $priorityCollection,
        \Magento\User\Model\ResourceModel\User\Collection $userCollection,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_customerRepository = $customerRepository;
        $this->_customerNameGenerationInterface = $customerNameGenerationInterface;
        $this->_searchCriteria = $searchCriteria;
        $this->_sortOrderBuilder = $sortOrderBuilder;
        $this->_productFactory = $productFactory;
        $this->_systemStore = $systemStore;
        $this->_priorityCollection = $priorityCollection;
        $this->_userCollection = $userCollection;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare edit ticket form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $form = $this->_createForm();
        $this->_addTicketFieldset($form)
            ->_addCustomer()
            ->_addStoreInfo()
            ->_addAssignee()
            ->_addPriority();

        $form->setUseContainer(true);
        if ($this->_getTicket()) {
            $values = $this->_getTicket()->getData();
        } else {
            $values = ['priority_id' => $this->_dataHelper->getDefaultPriority()];
        }
        $form->setValues($values);

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Get ticket store name
     *
     * @return null|string
     */
    protected function _getStoreName()
    {
        if ($this->_getTicket()) {
            $storeId = $this->_getTicket()->getStoreId();
            if ($storeId === null) {
                $deleted = __(" [deleted]");
                return nl2br($this->_getTicket()->getStoreName()) . $deleted;
            }
            $store = $this->_storeManager->getStore($storeId);
            $name = [
                $store->getWebsite()->getName(),
                "&nbsp;&nbsp;&nbsp;" . $store->getGroup()->getName(),
                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $store->getName()
            ];
            return implode('<br/>', $name);
        }

        return null;
    }

    /**
     * Get the ticket from registry
     *
     * @return mixed|null
     * @throws \Exception
     */
    protected function _getTicket()
    {
        if (!$this->_ticket) {
            $this->_ticket = $this->_coreRegistry->registry('ticket_data');
        }

        return $this->_ticket;
    }

    /**
     * Creates the ticket form
     *
     * @return \Magento\Framework\Data\Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _createForm()
    {
        return $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl(
                        'ticket/*/save',
                        [
                            'id' => $this->getRequest()->getParam('id'),
                            'ret' => $this->_coreRegistry->registry('ret')
                        ]
                    ),
                    'method' => 'post'
                ],
            ]
        );
    }

    /**
     * Sets the fieldset locally
     *
     * @param \Magento\Framework\Data\Form $form
     * @return $this
     */
    protected function _addTicketFieldset(\Magento\Framework\Data\Form $form)
    {
        if (!$this->_fieldset) {
            $this->_fieldset = $form->addFieldset(
                'ticket_edit_left_form',
                ['class' => 'ticket_edit_left_form']
            );
        }

        return $this;
    }

    /**
     * Retrieves the fieldset for the ticket fields
     *
     * @return \Magento\Framework\Data\Form\Element\Fieldset
     */
    protected function _getFieldset()
    {
        return $this->_fieldset;
    }

    /**
     * Adds the customer information to the form.
     *
     * @return $this
     * @throws \Exception
     */
    protected function _addCustomer()
    {
        $this->_searchCriteria->setFilterGroups([])->setSortOrders(
            [
                $this->_sortOrderBuilder
                    ->setField('firstname')
                    ->setDirection(\Magento\Framework\Api\SortOrder::SORT_ASC)->create(),
                $this->_sortOrderBuilder
                    ->setField('lastname')
                    ->setDirection(\Magento\Framework\Api\SortOrder::SORT_ASC)->create()
            ]
        );

        $customerArray = $this->_customerRepository->getList($this->_searchCriteria);
        $customerGridOptionArray = [];
        foreach ($customerArray->getItems() as $customer) {
            $customerGridOptionArray[$customer->getId()] =
                $this->_getCustomerName($customer) . ' - (' . $customer->getEmail() . ')';
        }

        $this->_getFieldSet()->addField(
            'customer_id',
            'select',
            [
                'label' => __("Customer"),
                'required' => true,
                'name' => 'customer_id',
                'values' => $customerGridOptionArray,
            ]
        );

        return $this;
    }

    /**
     * Adds the priority to the form
     *
     * @return $this
     */
    protected function _addPriority()
    {
        $this->_getFieldSet()->addField(
            'priority_id',
            'select',
            [
                'label' => __("Priority"),
                'required' => true,
                'name' => 'priority_id',
                'values' => $this->_priorityCollection->toGridOptionArray(),
            ]
        );
        return $this;
    }

    /**
     * Adds the priority to the form
     *
     * @return $this
     */
    protected function _addAssignee()
    {
        $this->_getFieldSet()->addField(
            'assignee_id',
            'select',
            [
                'label' => __("Assignee"),
                'required' => true,
                'name' => 'assignee_id',
                'values' => $this->_getUserList()
            ]
        );
        return $this;
    }

    /**
     * Get a list of admin users
     *
     * @return array
     */
    protected function _getUserList()
    {
        $this->_userCollection->addOrder('firstname', 'ASC')->addOrder('lastname', 'ASC');

        $users = [0 => __("Unassigned")];
        foreach ($this->_userCollection as $user) {
            $users[$user->getId()] = $this->_formatUser($user);
        }
        return $users;
    }

    /**
     * To String method for the admin user: Admin name - Admin email
     *
     * @param \Magento\User\Model\User $user
     * @return string
     */
    protected function _formatUser(\Magento\User\Model\User $user)
    {
        return "{$user->getName()} - {$user->getEmail()}";
    }

    /**
     * Adds the store info to this form
     *
     * @return $this
     */
    protected function _addStoreInfo()
    {

        $this->_getFieldSet()->addField(
            'store_id',
            'select',
            [
                'label' => __("Store"),
                'title' => __("Store"),
                'values' => $this->_systemStore->getStoreValuesForForm(),
                'name' => 'store_id',
                'required' => true
            ]
        );
        return $this;
    }

    /**
     * Get the customer name
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return string
     */
    protected function _getCustomerName(\Magento\Customer\Api\Data\CustomerInterface $customer)
    {
        return $this->_customerNameGenerationInterface->getCustomerName($customer);
    }
}
