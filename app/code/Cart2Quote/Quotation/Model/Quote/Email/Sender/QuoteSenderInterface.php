<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Email\Sender;

/**
 * Interface QuoteSenderInterface
 *
 * @package Cart2Quote\Quotation\Model\Quote\Email\Sender
 */
interface QuoteSenderInterface
{
    /**
     * Sends quote request email to the customer.
     * - Email will be sent immediately in two cases:
     * - if asynchronous email sending is disabled in global settings
     * - if $forceSyncMode parameter is set to TRUE
     * - Otherwise, email will be sent later during running of
     * - corresponding cron job.
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param bool $forceSyncMode
     * @return bool
     */
    public function send(\Cart2Quote\Quotation\Model\Quote $quote, $forceSyncMode = false);
}
