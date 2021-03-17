<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Email\Container;

/**
 * Class QuoteReminderIdentity
 *
 * @package Cart2Quote\Quotation\Model\Quote\Email\Container
 */
class QuoteReminderIdentity extends AbstractQuoteIdentity
{
    use \Cart2Quote\Features\Traits\Model\Quote\Email\Container\QuoteReminderIdentity {
    }

    /**
     * Configuration paths
     */
    const XML_PATH_EMAIL_COPY_METHOD = 'cart2quote_email/quote_reminder/copy_method';
    const XML_PATH_EMAIL_COPY_TO = 'cart2quote_email/quote_reminder/copy_to';
    const XML_PATH_EMAIL_IDENTITY = 'cart2quote_email/quote_reminder/identity';
    const XML_PATH_EMAIL_TEMPLATE = 'cart2quote_email/quote_reminder/template';
    const XML_PATH_EMAIL_ENABLED = 'cart2quote_email/quote_reminder/enabled';
}
