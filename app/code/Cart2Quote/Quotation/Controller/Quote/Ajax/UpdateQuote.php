<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Quote\Ajax;

/**
 * Class UpdateQuote
 *
 * @package Cart2Quote\Quotation\Controller\Quote
 */
class UpdateQuote extends \Cart2Quote\Quotation\Controller\Quote\Ajax\AjaxAbstract
{
    use \Cart2Quote\Features\Traits\Controller\Quote\Ajax\UpdateQuote {
        processAction as private traitProcessAction;
        updateFields as private traitUpdateFields;
    }

    const EVENT_PREFIX = 'update_quote';

    /**
     * Update customer's quote
     *
     * @return void
     */
    public function processAction()
    {
        $this->traitProcessAction();
    }

    /**
     * Update the quotation fields
     *
     * @return void
     */
    private function updateFields()
    {
        $this->traitUpdateFields();
    }
}
