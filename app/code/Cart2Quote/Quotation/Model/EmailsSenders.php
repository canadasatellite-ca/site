<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model;

/**
 * Quotation emails sending observer.
 * Performs handling of cron jobs related to sending emails to customers
 * after creation/modification of Order, Invoice, Shipment or Creditmemo.
 */
class EmailsSenders
{
    use \Cart2Quote\Features\Traits\Model\EmailsSenders {
        sendEmails as private traitSendEmails;
    }

    /**
     * @var EmailSenderHandler[]
     */
    protected $emailSenderHandlers;

    /**
     * EmailsSender constructor.
     *
     * @param EmailSenderHandler[] $emailSenderHandlers
     */
    public function __construct($emailSenderHandlers)
    {
        $this->emailSenderHandlers = $emailSenderHandlers;
    }

    /**
     * Handles asynchronous email sending
     *
     * @return void
     */
    public function sendEmails()
    {
        $this->traitSendEmails();
    }
}
