<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model;
/**
 * Quotation emails sending observer.
 * Performs handling of cron jobs related to sending emails to customers
 * after creation/modification of Order, Invoice, Shipment or Creditmemo.
 */
trait EmailsSenders
{
    /**
     * Handles asynchronous email sending
     *
     * @return void
     */
    private function sendEmails()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			foreach ($this->emailSenderHandlers as $emailSenderHandler) {
            $emailSenderHandler->sendEmails();
        }
		}
	}
}
