<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Email\Container;

/**
 * Class QuoteIdentity
 *
 * @package Cart2Quote\Quotation\Model\Quote\Email\Container
 */
class QuoteIdentity extends AbstractQuoteIdentity
{
    use \Cart2Quote\Features\Traits\Model\Quote\Email\Container\QuoteIdentity {
        getGuestTemplateId as private traitGetGuestTemplateId;
    }

    /**
     * Configuration paths
     */
    const XML_PATH_EMAIL_COPY_METHOD = 'cart2quote_email/new_quote_request/copy_method';
    const XML_PATH_EMAIL_COPY_TO = 'cart2quote_email/new_quote_request/copy_to';
    const XML_PATH_EMAIL_IDENTITY = 'cart2quote_email/new_quote_request/identity';
    const XML_PATH_EMAIL_GUEST_TEMPLATE = 'cart2quote_email/new_quote_request/guest_template';
    const XML_PATH_EMAIL_TEMPLATE = 'cart2quote_email/new_quote_request/template';
    const XML_PATH_EMAIL_ENABLED = 'cart2quote_email/new_quote_request/enabled';

    /**
     * Return guest template id
     *
     * @return mixed
     */
    public function getGuestTemplateId()
    {
        return $this->traitGetGuestTemplateId();
    }
}
