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

namespace Cart2Quote\Desk\Block\Adminhtml;

/**
 * Class Grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Ticket action pager
     *
     * @var \Cart2Quote\Desk\Helper\Action\Pager
     */
    protected $_ticketActionPager;

    /**
     * Ticket data
     *
     * @var \Cart2Quote\Desk\Helper\Data
     */
    protected $_ticketData;

    /**
     * Ticket collection
     *
     * @var \Cart2Quote\Desk\Model\ResourceModel\Ticket\Collection
     */
    protected $_ticketCollection;

    /**
     * Status collection
     *
     * @var \Cart2Quote\Desk\Model\ResourceModel\Ticket\Status\Collection
     */
    protected $_statusCollection;

    /**
     * Priority collection
     *
     * @var \Cart2Quote\Desk\Model\ResourceModel\Ticket\Status\Collection
     */
    protected $_priorityCollection;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * List of tickets
     *
     * @var \Magento\Framework\Api\SearchResultsInterface
     */
    protected $_tickets;

    /**
     * Class Grid constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Cart2Quote\Desk\Model\ResourceModel\Ticket\Collection $ticketCollection
     * @param \Cart2Quote\Desk\Model\ResourceModel\Ticket\Status\Collection $statusCollection
     * @param \Cart2Quote\Desk\Model\ResourceModel\Ticket\Priority\Collection $priorityCollection
     * @param \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface
     * @param \Cart2Quote\Desk\Helper\Action\Pager $ticketActionPager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Cart2Quote\Desk\Model\ResourceModel\Ticket\Collection $ticketCollection,
        \Cart2Quote\Desk\Model\ResourceModel\Ticket\Status\Collection $statusCollection,
        \Cart2Quote\Desk\Model\ResourceModel\Ticket\Priority\Collection $priorityCollection,
        \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface,
        \Cart2Quote\Desk\Helper\Action\Pager $ticketActionPager,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_statusCollection = $statusCollection;
        $this->_priorityCollection = $priorityCollection;
        $this->_ticketRepositoryInterface = $ticketRepositoryInterface;
        $this->_coreRegistry = $coreRegistry;
        $this->_ticketActionPager = $ticketActionPager;
        $this->_ticketCollection = $ticketCollection;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Initialize grid
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('ticketGrid');
        $this->setDefaultSort('created_at');
    }

    /**
     * Save search results
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _afterLoadCollection()
    {
        /** @var $actionPager \Cart2Quote\Desk\Helper\Action\Pager */
        $actionPager = $this->_ticketActionPager;
        $actionPager->setStorageId('tickets');
        $actionPager->setItems($this->getCollection()->getResultingIds());

        return parent::_afterLoadCollection();
    }

    /**
     * Prepare collection
     *
     * @return \Cart2Quote\Desk\Block\Adminhtml\Grid
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->_ticketCollection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'ticket_id',
            [
                'header' => __("ID"),
                'filter_index' => 'ticket_id',
                'index' => 'ticket_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'created_at',
            [
                'header' => __("Created"),
                'type' => 'datetime',
                'filter_index' => 'created_at',
                'index' => 'created_at',
                'header_css_class' => 'col-date',
                'column_css_class' => 'col-date'
            ]
        );

        $this->addColumn(
            'updated_at',
            [
                'header' => __("Updated"),
                'type' => 'datetime',
                'filter_index' => 'updated_at',
                'index' => 'updated_at',
                'header_css_class' => 'col-date',
                'column_css_class' => 'col-date'
            ]
        );

        $this->addColumn(
            'subject',
            [
                'header' => __("Subject"),
                'filter_index' => 'subject',
                'index' => 'subject',
                'type' => 'text',
                'truncate' => 50,
                'escape' => true
            ]
        );

        $this->addColumn(
            'customer_name',
            [
                'header' => __("Customer Name"),
                'filter_index' => 'customer_name',
                'index' => 'customer_name',
                'type' => 'text',
                'truncate' => 50,
                'escape' => true,
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );

        $this->addColumn(
            'customer_email',
            [
                'header' => __("Customer Email"),
                'filter_index' => 'customer_email',
                'index' => 'customer_email',
                'type' => 'text',
                'truncate' => 50,
                'escape' => true,
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );

        $this->addColumn(
            'status_id',
            [
                'header' => __("Status"),
                'filter_index' => 'hts.status_id',
                'index' => 'status_id',
                'type' => 'options',
                'options' => $this->_statusCollection->toGridOptionArray(),
            ]
        );

        $this->addColumn(
            'priority_id',
            [
                'header' => __("Priority"),
                'filter_index' => 'htp.priority_id',
                'index' => 'priority_id',
                'type' => 'options',
                'options' => $this->_priorityCollection->toGridOptionArray(),
            ]
        );

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'store_id',
                [
                    'header' => __("Store View"),
                    'index' => 'store_id',
                    'type' => 'store',
                    'store_all' => true,
                    'store_view' => true,
                    'sortable' => false,
                    'filter_condition_callback' => [$this, '_filterStoreCondition'],
                    'column_css_class' => 'col-name'
                ]
            );
        }

        $this->addColumn(
            'assignee_name',
            [
                'header' => __("Assignee"),
                'filter_index' => 'assignee_name',
                'index' => 'assignee_name',
                'type' => 'text',
                'truncate' => 50,
                'escape' => true,
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );

        $this->addColumn(
            'action',
            [
                'header' => __("Action"),
                'type' => 'action',
                'getter' => 'getTicketId',
                'actions' => [
                    [
                        'caption' => __("Edit"),
                        'url' => [
                            'base' => 'desk/ticket/edit',
                            'params' => [
                                'id' => $this->getId()
                            ],
                        ],
                        'field' => 'id',
                    ],
                ],
                'filter' => false,
                'sortable' => false
            ]
        );

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

    /**
     * Prepare grid mass actions
     *
     * @return void
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('ticket_id');
        $this->setMassactionIdFilter('ticket_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('tickets');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __("Delete"),
                'url' => $this->getUrl(
                    '*/*/massDelete'
                ),
                'confirm' => __("Are you sure?")
            ]
        );
    }

    /**
     * Get row url
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'desk/ticket/edit',
            [
                'id' => $row->getTicketId()
            ]
        );
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getCurrentUrl();
    }

    /**
     * Filter store
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return void
     */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }
}
