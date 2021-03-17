<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model;
use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection as DbAbstractCollection;
/**
 * Quotation emails sending observer.
 * Performs handling of cron jobs related to sending emails to customers
 * after creation/modification of Order, Invoice, Shipment or Creditmemo.
 */
trait EmailSenderHandler
{
    /**
     * Handles asynchronous email sending
     *
     * @return void
     */
    private function sendEmails()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->globalConfig->getValue('sales_email/general/async_sending')) {
            $this->entityCollection->addFieldToFilter($this->emailSender->getSendEmailIdentifier(), ['eq' => 1]);
            $this->entityCollection->addFieldToFilter($this->emailSender->getEmailSentIdentifier(), ['null' => true]);
            /** @var \Magento\Sales\Model\AbstractModel $item */
            foreach ($this->entityCollection->getItems() as $item) {
                $this->emailSender->send($item, true);
            }
        }
		}
	}
}
