<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection as DbAbstractCollection;

/**
 * Quotation emails sending observer.
 * Performs handling of cron jobs related to sending emails to customers
 * after creation/modification of Order, Invoice, Shipment or Creditmemo.
 */
class EmailSenderHandler
{
    use \Cart2Quote\Features\Traits\Model\EmailSenderHandler {
        sendEmails as private traitSendEmails;
    }

    /**
     * Email sender model.
     *
     * @var \Cart2Quote\Quotation\Model\Quote\Email\Sender\Sender
     */
    protected $emailSender;

    /**
     * Entity resource model.
     *
     * @var \Magento\Sales\Model\ResourceModel\EntityAbstract
     */
    protected $entityResource;

    /**
     * Entity collection model.
     *
     * @var \Magento\Sales\Model\ResourceModel\Collection\AbstractCollection
     */
    protected $entityCollection;

    /**
     * Global configuration storage.
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $globalConfig;

    /**
     * EmailSenderHandler constructor
     *
     * @param \Cart2Quote\Quotation\Model\Quote\Email\Sender\Sender $emailSender
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote $entityResource
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection $entityCollection
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Quote\Email\Sender\Sender $emailSender,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote $entityResource,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection $entityCollection,
        \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig
    ) {
        $this->emailSender = $emailSender;
        $this->entityResource = $entityResource;
        $this->entityCollection = $entityCollection;
        $this->globalConfig = $globalConfig;
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
