<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Cron;

/**
 * Class SendEmails
 *
 * @package Cart2Quote\Quotation\Cron
 */
class SendEmails
{
    /**
     * Global configuration storage.
     *
     * @var \Cart2Quote\Quotation\Model\EmailsSenders
     */
    protected $emailsSenders;

    /**
     * @param \Cart2Quote\Quotation\Model\EmailsSenders $emailsSenders
     */
    public function __construct(\Cart2Quote\Quotation\Model\EmailsSenders $emailsSenders)
    {
        $this->emailsSenders = $emailsSenders;
    }

    /**
     * Handles asynchronous email sending during corresponding
     * - cron job.
     * - Also method is used in the next events:
     * - - config_data_sales_email_general_async_sending_disabled
     * - Works only if asynchronous email sending is enabled
     * - in global settings.
     *
     * @return void
     */
    public function execute()
    {
        $this->emailsSenders->sendEmails();
    }
}
