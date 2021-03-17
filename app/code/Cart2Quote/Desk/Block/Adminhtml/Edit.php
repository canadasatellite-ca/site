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
 * Ticket edit form
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Ticket action pager
     *
     * @var \Cart2Quote\Desk\Helper\Action\Pager
     */
    protected $_ticketActionPager = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Ticket Repository
     *
     * @var \Cart2Quote\Desk\Api\TicketRepositoryInterface
     */
    protected $_ticketRepositoryInterface;

    /**
     * Ticket Model
     *
     * @var \Cart2Quote\Desk\Model\Ticket
     */
    protected $_ticket;

    /**
     * Status data
     *
     * @var \Cart2Quote\Desk\Model\ResourceModel\Ticket\Status\Collection
     */
    protected $_statusCollection = null;

    /**
     * Class Edit constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface
     * @param \Cart2Quote\Desk\Model\Ticket $ticket
     * @param \Cart2Quote\Desk\Helper\Action\Pager $ticketActionPager
     * @param \Magento\Framework\Registry $registry
     * @param \Cart2Quote\Desk\Model\ResourceModel\Ticket\Status\Collection $statusCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface,
        \Cart2Quote\Desk\Model\Ticket $ticket,
        \Cart2Quote\Desk\Helper\Action\Pager $ticketActionPager,
        \Magento\Framework\Registry $registry,
        \Cart2Quote\Desk\Model\ResourceModel\Ticket\Status\Collection $statusCollection,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_ticketActionPager = $ticketActionPager;
        $this->_ticketRepositoryInterface = $ticketRepositoryInterface;
        $this->_ticket = $ticket;
        $this->_statusCollection = $statusCollection;
        parent::__construct($context, $data);
    }

    /**
     * Overwrite: Force the form from layout xml file
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        $form = $this->getLayout()->getBlock('form');
        $this->setChild('form', $form);
        parent::_prepareLayout();
    }

    /**
     * Initialize edit ticket
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'Cart2Quote_Desk';
        $this->_controller = 'adminhtml';

        $this->removeButton('reset');
        $this->removeButton('save');

        /** @var $actionPager \Cart2Quote\Desk\Helper\Action\Pager */
        $actionPager = $this->_ticketActionPager;
        $actionPager->setStorageId('tickets');

        $ticketId = $this->_registerTicket();
        $this->_addPreviousButton($actionPager, $ticketId);
        $this->_addNextButton($actionPager, $ticketId);
        $this->_addSubmitButton($ticketId);
        $this->_addDeleteButton();
    }

    /**
     * Get edit ticket header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $ticketData = $this->_coreRegistry->registry('ticket_data');
        if ($ticketData && $ticketData->getId()) {
            return __("Edit Ticket #").$ticketData->getId();
        } else {
            return __("New Ticket");
        }
    }

    /**
     * Retrieve options for 'Add Product' split button
     *
     * @param int $ticketId
     * @return array
     */
    protected function _getAddProductButtonOptions($ticketId)
    {
        $splitButtonOptions = [];

        foreach ($this->_statusCollection->toOptionArray() as $statusId => $status) {
            $onclick =
                "document.getElementById('edit_form').action = " .
                "'{$this->_getTicketSubmitUrl($ticketId, $status['value'])}';".
                " document.getElementById('edit_form').submit();";

            $splitButtonOptions[$statusId] = [
                'label' => __("Submit as %1", ucfirst($status['label'])),
                'onclick' => $onclick,
                'default' => \Magento\Catalog\Model\Product\Type::DEFAULT_TYPE == $status['value'],
            ];
        }

        return $splitButtonOptions;
    }

    /**
     * Get the Ticket submit url by specified status type
     *
     * @param int $ticketId
     * @param int $statusId
     * @return string
     */
    protected function _getTicketSubmitUrl($ticketId, $statusId)
    {
        return $this->getUrl(
            '*/*/save',
            ['id' => $ticketId, 'status_id' => $statusId]
        );
    }

    /**
     * Register the ticket
     *
     * @return int $ticketId
     */
    protected function _registerTicket()
    {
        $ticketId = $this->getRequest()->getParam('id');
        if ($ticketId) {
            $this->_ticket->updateData($this->_ticketRepositoryInterface->getById($ticketId));
            $this->_coreRegistry->register('ticket_data', $this->_ticket);
            return $ticketId;
        }
        return $ticketId;
    }

    /**
     * Add the previous ticket button
     *
     * @param \Cart2Quote\Desk\Helper\Action\Pager $actionPager
     * @param int $ticketId
     *
     * @return void
     */
    protected function _addPreviousButton(\Cart2Quote\Desk\Helper\Action\Pager $actionPager, $ticketId)
    {
        $prevId = $actionPager->getPreviousItemId($ticketId);
        if ($prevId !== false) {
            $this->addButton(
                'previous',
                [
                    'label' => __("Previous"),
                    'onclick' => 'setLocation(\'' . $this->getUrl('desk/*/*', ['id' => $prevId]) . '\')'
                ],
                3,
                10
            );
        }
    }

    /**
     * Add the next ticket button
     *
     * @param \Cart2Quote\Desk\Helper\Action\Pager $actionPager
     * @param int $ticketId
     *
     * @return void
     */
    protected function _addNextButton(\Cart2Quote\Desk\Helper\Action\Pager $actionPager, $ticketId)
    {
        $nextId = $actionPager->getNextItemId($ticketId);
        if ($nextId !== false) {
            $this->addButton(
                'next',
                [
                    'label' => __("Next"),
                    'onclick' => 'setLocation(\'' . $this->getUrl('desk/*/*', ['id' => $nextId]) . '\')'
                ],
                3,
                105
            );
        }
    }

    /**
     * Add the submit button
     *
     * @param int $ticketId
     *
     * @return void
     */
    protected function _addSubmitButton($ticketId)
    {
        $statusHtml = __("Open");
        if ($this->_ticket->getStatusHtml()) {
            $statusHtml = $this->_ticket->getStatusHtml();
        }

        $addButtonProps = [
            'id' => 'submit_ticket',
            'label' => __("Submit as %1", $statusHtml),
            'class' => 'add',
            'button_class' => '',
            'class_name' => 'Cart2Quote\Desk\Block\Adminhtml\Edit\Overwrite\SplitButton',
            'options' => $this->_getAddProductButtonOptions($ticketId),
        ];
        $this->buttonList->add('add_new', $addButtonProps);
    }

    /**
     * Add the delete button
     *
     * @return void
     */
    protected function _addDeleteButton()
    {
        $this->buttonList->update('delete', 'label', __("Delete Ticket"));
        if ($this->getRequest()->getParam('ret', false) == 'pending') {
            $this->buttonList->update('back', 'onclick', 'setLocation(\'' . $this->getUrl('catalog/*/pending') . '\')');
            $this->buttonList->update(
                'delete',
                'onclick',
                'deleteConfirm(' . '\'' . __(
                    'Are you sure you want to do this?'
                ) . '\' ' . '\'' . $this->getUrl(
                    '*/*/delete',
                    [$this->_objectId => $this->getRequest()->getParam($this->_objectId), 'ret' => 'pending']
                ) . '\'' . ')'
            );
            $this->_coreRegistry->register('ret', 'pending');
        }
    }
}
