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

/**
 * Class Delete
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * Cart2Quote ticket message
     *
     * @var \Cart2Quote\Desk\Api\TicketRepositoryInterface
     */
    protected $_ticketRepositoryInterface;

    /**
     * Class delete constructor
     *
     * @param \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_ticketRepositoryInterface = $ticketRepositoryInterface;
        parent::__construct($context);
    }

    /**
     * Deletes a ticket by ID
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $ticketId = $this->getRequest()->getParam('id', false);
        try {
            $this->dispatchDeleteTicketEventBefore($ticketId);
            $this->_ticketRepositoryInterface->deleteById($ticketId);
            $this->dispatchDeleteTicketEventAfter($ticketId);
            $resultRedirect->setPath('desk/ticket/index/');
            $this->messageManager->addSuccess(__("The ticket has been deleted."));
            return $resultRedirect;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __("Something went wrong deleting this ticket."));
        }

        return $resultRedirect->setPath('desk/ticket/edit/', ['id' => $ticketId]);
    }

    /**
     * Dispatch delete ticket after event
     *
     * @param int $ticketId
     * @return void
     */
    private function dispatchDeleteTicketEventAfter($ticketId)
    {
        $this->_eventManager->dispatch(
            'desk_adminhtml_delete_ticket_after',
            ['ticket_id' => $ticketId]
        );
    }

    /**
     * Dispatch delete ticket before event
     *
     * @param int $ticketId
     * @return void
     */
    private function dispatchDeleteTicketEventBefore($ticketId)
    {
        $this->_eventManager->dispatch(
            'desk_adminhtml_delete_ticket_before',
            ['ticket_id' => $ticketId]
        );
    }
}
