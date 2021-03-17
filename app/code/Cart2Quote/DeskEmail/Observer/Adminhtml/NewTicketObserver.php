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

namespace Cart2Quote\DeskEmail\Observer\Adminhtml;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class NewTicketObserver
 */
class NewTicketObserver implements ObserverInterface
{
    /**
     * New Ticket Sender
     *
     * @var \Cart2Quote\DeskEmail\Model\Sender\newTicketSender
     */
    protected $_newTicketSender;

    /**
     * Desk Helper
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_helper;

    /**
     * Class NewTicketObserver constructor
     *
     * @param \Cart2Quote\DeskEmail\Model\Sender\NewTicketSender $newTicketSender
     * @param \Magento\Backend\Helper\Data $helper
     */
    public function __construct(
        \Cart2Quote\DeskEmail\Model\Sender\NewTicketSender $newTicketSender,
        \Magento\Backend\Helper\Data $helper
    ) {
        $this->_newTicketSender = $newTicketSender;
        $this->_helper = $helper;
    }

    /**
     * Send new ticket email to the message receiver.
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $ticket = $observer->getEvent()->getTicket();
        $message = $observer->getEvent()->getMessage();
        $this->_newTicketSender->send($ticket, $message);

        return $this;
    }
}
