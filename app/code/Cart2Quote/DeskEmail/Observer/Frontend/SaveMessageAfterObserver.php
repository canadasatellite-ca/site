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

namespace Cart2Quote\DeskEmail\Observer\Frontend;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class SaveMessageAfterObserver
 */
class SaveMessageAfterObserver implements ObserverInterface
{
    /**
     * Message Sender
     *
     * @var \Cart2Quote\DeskEmail\Model\Sender\MessageSender
     */
    protected $_messageSender;

    /**
     * Class SaveMessageAfterObserver constructor
     *
     * @param \Cart2Quote\DeskEmail\Model\Sender\MessageSender $messageSender
     */
    public function __construct(
        \Cart2Quote\DeskEmail\Model\Sender\MessageSender $messageSender
    ) {
        $this->_messageSender = $messageSender;
    }

    /**
     * Send message to the message receiver.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $ticket = $observer->getEvent()->getTicket();
        $message = $observer->getEvent()->getMessage();

        $this->_messageSender->send($ticket, $message);

        return $this;
    }
}
