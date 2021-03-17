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
 * @package     DeskEmail
 * @copyright   Copyright (c) 2016 Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */

namespace Cart2Quote\DeskEmail\Model\Sender;

use Cart2Quote\DeskEmail\Model\SenderBuilder;
use Magento\Sales\Model\Order\Email\Container\Template;
use Cart2Quote\DeskEmail\Model\Sender;
use Magento\Framework\Event\ManagerInterface;
use Cart2Quote\Desk\Api\Data\TicketInterface;
use Cart2Quote\Desk\Api\Data\MessageInterface;

/**
 * Class MessageSender
 */
class MessageSender extends Sender
{
    /**
     * Application Event Dispatcher
     *
     * @var ManagerInterface
     */
    protected $_eventManager;

    /**
     * Customer Repository Interface
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $_customerRepositoryInterface;

    /**
     * Ticket Model
     *
     * @var \Cart2Quote\Desk\Model\Ticket
     */
    protected $_ticket;

    /**
     * Message Model
     *
     * @var \Cart2Quote\Desk\Model\Ticket\Message
     */
    protected $_message;

    /**
     * Admin helper
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_adminHelper;

    /**
     * Frontend Url
     *
     * @var \Magento\Framework\Url
     */
    protected $_frontendUrl;

    /**
     * Class MessageSender constructor
     *
     * @param Template $templateContainer
     * @param \Cart2Quote\DeskEmail\Model\Container\MessageIdentity $identityContainer
     * @param \Cart2Quote\DeskEmail\Model\SenderBuilderFactory $senderBuilderFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Customer\Model\Address $address
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepositoryInterface
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepositoryInterface
     * @param \Magento\Store\Model\Store $store
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Cart2Quote\Desk\Model\Ticket $ticket
     * @param \Cart2Quote\Desk\Model\Ticket\Message $message
     * @param \Magento\Backend\Helper\Data $adminHelper
     * @param \Magento\Framework\Url $frontendUrl
     */
    public function __construct(
        Template $templateContainer,
        \Cart2Quote\DeskEmail\Model\Container\MessageIdentity $identityContainer,
        \Cart2Quote\DeskEmail\Model\SenderBuilderFactory $senderBuilderFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Model\Address $address,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepositoryInterface,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepositoryInterface,
        \Magento\Store\Model\Store $store,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Cart2Quote\Desk\Model\Ticket $ticket,
        \Cart2Quote\Desk\Model\Ticket\Message $message,
        \Magento\Backend\Helper\Data $adminHelper,
        \Magento\Framework\Url $frontendUrl
    ) {
        $this->_adminHelper = $adminHelper;
        $this->_frontendUrl = $frontendUrl;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_ticket = $ticket;
        $this->_message = $message;
        parent::__construct(
            $templateContainer,
            $identityContainer,
            $senderBuilderFactory,
            $logger,
            $address,
            $addressRepositoryInterface,
            $storeRepositoryInterface,
            $store
        );
    }

    /**
     * Send email to customer
     *
     * @param TicketInterface $ticket
     * @param MessageInterface $message
     * @return bool
     */
    public function send(TicketInterface $ticket, MessageInterface $message)
    {
        $customer = $this->_customerRepositoryInterface->getById($ticket->getCustomerId());

        $transport = [
            'ticket' => $this->_ticket->updateData($ticket),
            'message' => $this->_message->updateData($message),
            'store' => $this->getStore($ticket),
            'formattedShippingAddress' => $this->getFormattedShippingAddress($customer),
            'formattedBillingAddress' => $this->getFormattedBillingAddress($customer),
            'admin_edit_ticket_url' => $this->_adminHelper->getUrl(
                'desk/ticket/edit',
                ['id' => $ticket->getId()]
            ),
            'frontend_edit_ticket_url' => $this->_frontendUrl->getUrl(
                'desk/customer/view',
                ['id' => $ticket->getId()]
            )
        ];

        $transport = new \Magento\Framework\DataObject($transport);

        $this->_templateContainer->setTemplateVars($transport->getData());

        return $this->checkAndSend($ticket, $message);
    }

    /**
     * Checks if the email is allowed to be send.
     * If true then the email will be send.
     *
     * @param TicketInterface $ticket
     * @param MessageInterface $message
     * @return bool
     */
    protected function checkAndSend(TicketInterface $ticket, MessageInterface $message)
    {
        $this->_identityContainer->setStore($this->getStore($ticket));
        if (!$this->_identityContainer->isEnabled()) {
            return false;
        }
        $this->prepareTemplate($message, $ticket);

        /** @var SenderBuilder $sender */
        $sender = $this->getSender();

        try {
            $sender->send();
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
        }

        return true;
    }

    /**
     * Prepare the template
     *
     * @param MessageInterface $message
     * @param TicketInterface $ticket
     * @return $this|void
     */
    protected function prepareTemplate(MessageInterface $message, TicketInterface $ticket)
    {
        $this->_templateContainer->setTemplateOptions($this->getTemplateOptions());

        if ($message->getUserId()) {
            $templateId = $this->_identityContainer->getTemplateId();
            $this->_identityContainer->setMainName($ticket->getCustomerName());
            $this->_identityContainer->setMainEmail($ticket->getCustomerEmail());
        } else {
            $templateId = $this->_identityContainer->getAdminTemplateId();
            $this->_identityContainer->setMainName($ticket->getAssigneeName());
            $this->_identityContainer->setMainEmail($ticket->getAssigneeEmail());
        }

        $this->_templateContainer->setTemplateId($templateId);

        return $this;
    }
}
