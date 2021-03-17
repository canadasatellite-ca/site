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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class MassDelete
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * Cart2Quote ticket message
     *
     * @var \Cart2Quote\Desk\Api\TicketRepositoryInterface
     */
    protected $_ticketRepositoryInterface;

    /**
     * Class MassDelete constructor
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
     * Mass delete the tickets
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $ticketIds = $this->getRequest()->getParam('tickets');
        if (!is_array($ticketIds)) {
            $this->messageManager->addError(__("Please select ticket(s)."));
        } else {
            try {
                $this->dispatchDeleteTicketsEventBefore($ticketIds);
                foreach ($ticketIds as $ticketId) {
                    $this->dispatchDeleteTicketEventBefore($ticketId);
                    $this->_ticketRepositoryInterface->deleteById($ticketId);
                    $this->dispatchDeleteTicketEventAfter($ticketId);
                }
                $this->dispatchDeleteTicketsEventAfter($ticketIds);
                $this->messageManager->addSuccess(
                    __("A total of %1 record(s) have been deleted.", count($ticketIds))
                );
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __("Something went wrong while deleting these records."));
            }
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('desk/ticket/index');
        return $resultRedirect;
    }

    /**
     * Dispatch delete ticket after event
     *
     * @param int $ticketId
     *
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
     *
     * @return void
     */
    private function dispatchDeleteTicketEventBefore($ticketId)
    {
        $this->_eventManager->dispatch(
            'desk_adminhtml_delete_ticket_before',
            ['ticket_id' => $ticketId]
        );
    }

    /**
     * Dispatch delete ticket after event
     *
     * @param array $tickets
     *
     * @return void
     */
    private function dispatchDeleteTicketsEventAfter($tickets)
    {
        $this->_eventManager->dispatch(
            'desk_adminhtml_mass_delete_ticket_after',
            ['tickets' => $tickets]
        );
    }

    /**
     * Dispatch delete ticket before event
     *
     * @param array $tickets
     *
     * @return void
     */
    private function dispatchDeleteTicketsEventBefore($tickets)
    {
        $this->_eventManager->dispatch(
            'desk_adminhtml_mass_delete_ticket_before',
            ['tickets' => $tickets]
        );
    }
}
