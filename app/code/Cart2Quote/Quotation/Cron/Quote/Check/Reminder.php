<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Cron\Quote\Check;

/**
 * Class Reminder
 *
 * @package Cart2Quote\Quotation\Cron\Quote\Check
 */
class Reminder
{
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteReminderSender
     */
    protected $quoteReminderSender;

    /**
     * Reminder constructor.
     *
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $collectionFactory
     * @param \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteReminderSender $quoteReminderSender
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $collectionFactory,
        \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteReminderSender $quoteReminderSender
    ) {

        $this->collectionFactory = $collectionFactory;
        $this->quoteReminderSender = $quoteReminderSender;
    }

    /**
     * Check for reminding proposal for quotes
     */
    public function execute()
    {
        $availableStatus = [
            \Cart2Quote\Quotation\Model\Quote\Status::STATUS_PROPOSAL_SENT
        ];

        $quotes = $this->collectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('reminder_enabled', ['eq' => 1])
            ->addFieldToFilter($this->quoteReminderSender->getEmailSentIdentifier(), ['null' => true])
            ->addFieldToFilter('is_quote', ['eq' => \Cart2Quote\Quotation\Model\Quote::IS_QUOTE])
            ->addFieldToFilter('status', ['in' => $availableStatus])
            ->setOrder('created_at', 'desc');

        /**
         * @var \Cart2Quote\Quotation\Model\Quote $quote
         */
        foreach ($quotes as $quote) {
            $reminderDate = $quote->getReminderDate();
            if ($reminderDate !== null) {
                if ($reminderDate == date('Y-m-d')) {
                    $this->quoteReminderSender->send($quote);
                }
            }
        }
    }
}
