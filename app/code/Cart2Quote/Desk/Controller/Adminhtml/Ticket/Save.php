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

use Magento\Backend\App\Action;
use Magento\Framework\Exception\InputException;
use Magento\Review\Controller\Adminhtml\Product as ProductController;

/**
 * Class Save
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * Cart2Quote ticketFactory
     *
     * @var \Cart2Quote\Desk\Api\Data\TicketInterfaceFactory
     */
    protected $_ticketFactory;

    /**
     * Cart2Quote ticket message
     *
     * @var \Cart2Quote\Desk\Api\TicketRepositoryInterface
     */
    protected $_ticketRepositoryInterface;

    /**
     * Cart2Quote ticketFactory
     *
     * @var \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory
     */
    protected $_messageFactory;

    /**
     * Flag to keep track if this is a new ticket
     *
     * @var boolean
     */
    protected $_isNew;

    /**
     * Flag to keep track if this is a new ticket
     *
     * @var boolean
     */
    protected $_adminSession;

    /**
     * Cart2Quote data helper
     *
     * @var \Cart2Quote\Desk\Helper\Data
     */
    protected $_helperData;

    /**
     * Class Save constructor
     *
     * @param \Cart2Quote\Desk\Helper\Data $helperData
     * @param \Magento\Backend\Model\Auth\Session $adminSession
     * @param \Cart2Quote\Desk\Api\Data\TicketInterfaceFactory $ticketFactory
     * @param \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface
     * @param \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory $messageFactory
     * @param \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface
     * @param Action\Context $context
     */
    public function __construct(
        \Cart2Quote\Desk\Helper\Data $helperData,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Cart2Quote\Desk\Api\Data\TicketInterfaceFactory $ticketFactory,
        \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface,
        \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory $messageFactory,
        \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface,
        Action\Context $context
    ) {
        $this->_helperData = $helperData;
        $this->_adminSession = $adminSession;
        $this->_ticketFactory = $ticketFactory;
        $this->_ticketRepositoryInterface = $ticketRepositoryInterface;
        $this->_messageFactory = $messageFactory;
        $this->_messageRepositoryInterface = $messageRepositoryInterface;
        parent::__construct($context);
    }

    /**
     * Save/update the ticket
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        if (!$this->_helperData->getDeskEnabled()) {
            $this->getMessageManager()->addError(
                __("Customer Support Desk is currently disabled. " .
                    "Please contact your Magento administrator to enable Customer Support Desk again.")
            );
            return $this->_redirect('admin/dashboard');
        }

        $status = $this->getRequest()->getParam('status_id');
        if (isset($status)) {
            try {
                $this->setIsNew($this->getRequest()->getParam('id') == 0);
                $ticket = $this->_saveTicket();
                $message = $this->_saveMessage($ticket);
                $this->getRequest()->setParams(['id' => $ticket->getId()]);
                $this->_setSuccessMessage($ticket);

                if ($this->_isNew && $message && $ticket) {
                    $this->dispatchNewTicketEvent($message, $ticket);
                }
            } catch (InputException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }

    /**
     * Update or save the ticket
     *
     * @return \Cart2Quote\Desk\Api\Data\TicketInterface
     */
    protected function _saveTicket()
    {
        $ticket = $this->_ticketRepositoryInterface->getById($this->getRequest()->getParam('id'));
        $ticket->setStatusId($this->getRequest()->getParam('status_id'));
        $ticket->setAssigneeId($this->getAssigneeId());
        $ticket->setPriorityId($this->getRequest()->getParam('priority_id'));
        $ticket->setCustomerId($this->getRequest()->getParam('customer_id'));
        $ticket->setStoreId($this->getRequest()->getParam('store_id'));
        $ticket->setSubject($this->getRequest()->getParam('subject'));

        $this->dispatchSaveTicketEventBefore($ticket);
        $ticket = $this->_ticketRepositoryInterface->save($ticket);
        $this->dispatchSaveTicketEventAfter($ticket);

        return $ticket;
    }

    /**
     * Save a new message.
     *
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     *
     * @return bool|\Cart2Quote\Desk\Api\Data\MessageInterface
     */
    protected function _saveMessage(\Cart2Quote\Desk\Api\Data\TicketInterface $ticket)
    {
        $message = false;
        if ($this->getRequest()->getParam('message')) {
            $message = $this->_messageFactory->create();
            $message->setTicketId($ticket->getId());
            $message->setMessage($this->getRequest()->getParam('message'));
            $message->setUserId($ticket->getAssigneeId());
            $message->setIsPrivate($this->getRequest()->getParam('is_private'));

            $this->dispatchSaveMessageEventBefore($message, $ticket);
            $message = $this->_messageRepositoryInterface->save($message);
            $this->dispatchSaveMessageEventAfter($message, $ticket);
        }
        return $message;
    }

    /**
     * Set success message
     *
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     *
     * @return $this
     */
    protected function _setSuccessMessage(\Cart2Quote\Desk\Api\Data\TicketInterface $ticket)
    {
        $url = $this->_helper->getUrl('desk/ticket/edit', ['id' => $ticket->getId()]);
        if ($this->getIsNew()) {
            $successMessage = __("<a href=\"%1\">Ticket #%2 has been created.</a>", $url, $ticket->getId());
        } else {
            $successMessage = __("<a href=\"%1\">Ticket #%2 has been updated.</a>", $url, $ticket->getId());
        }
        $this->getMessageManager()->addSuccess($successMessage);
        return $this;
    }

    /**
     * Dispatch new ticket event
     *
     * @param \Cart2Quote\Desk\Api\Data\MessageInterface $message
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     *
     * @return void
     */
    private function dispatchNewTicketEvent(
        \Cart2Quote\Desk\Api\Data\MessageInterface $message,
        \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
    ) {
        $this->_eventManager->dispatch(
            'desk_adminhtml_new_ticket',
            [
                'message' => $message,
                'ticket' => $ticket
            ]
        );
    }

    /**
     * Dispatch save message after event
     *
     * @param \Cart2Quote\Desk\Api\Data\MessageInterface $message
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     *
     * @return void
     */
    private function dispatchSaveMessageEventAfter(
        \Cart2Quote\Desk\Api\Data\MessageInterface $message,
        \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
    ) {
        $this->_eventManager->dispatch(
            'desk_adminhtml_save_message_after',
            [
                'message' => $message,
                'ticket' => $ticket
            ]
        );
    }

    /**
     * Dispatch save message before event
     *
     * @param \Cart2Quote\Desk\Api\Data\MessageInterface $message
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     *
     * @return void
     */
    private function dispatchSaveMessageEventBefore(
        \Cart2Quote\Desk\Api\Data\MessageInterface $message,
        \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
    ) {
        $this->_eventManager->dispatch(
            'desk_adminhtml_save_message_before',
            [
                'message' => $message,
                'ticket' => $ticket
            ]
        );
    }

    /**
     * Dispatch save ticket after event
     *
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     * @return void
     */
    private function dispatchSaveTicketEventAfter(\Cart2Quote\Desk\Api\Data\TicketInterface $ticket)
    {
        $this->_eventManager->dispatch(
            'desk_adminhtml_save_ticket_after',
            ['ticket' => $ticket]
        );
    }

    /**
     * Dispatch save ticket before event
     *
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     * @return void
     */
    private function dispatchSaveTicketEventBefore(\Cart2Quote\Desk\Api\Data\TicketInterface $ticket)
    {
        $this->_eventManager->dispatch(
            'desk_adminhtml_save_ticket_before',
            ['ticket' => $ticket]
        );
    }

    /**
     * Flag for a new ticket
     *
     * @param bool|false $isNew
     * @return $this
     */
    public function setIsNew($isNew = false)
    {
        $this->_isNew = $isNew;
        return $this;
    }

    /**
     * Flag for a new ticket
     *
     * @return bool
     */
    public function getIsNew()
    {
        return $this->_isNew;
    }

    /**
     * Get assignee id
     *
     * @return int
     */
    public function getAssigneeId()
    {
        $assigneeId = $this->getRequest()->getParam('assignee_id');
        if ($assigneeId == 0) {
            $assigneeId = $this->_adminSession->getUser()->getId();
        }
        return $assigneeId;
    }
}
