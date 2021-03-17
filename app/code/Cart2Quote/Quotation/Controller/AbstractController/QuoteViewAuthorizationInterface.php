<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\AbstractController;

/**
 * Interface QuoteViewAuthorizationInterface
 *
 * @package Cart2Quote\Quotation\Controller\AbstractController
 */
interface QuoteViewAuthorizationInterface
{
    /**
     * Check if quote can be viewed by user
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return bool
     */
    public function canView(\Cart2Quote\Quotation\Model\Quote $quote);
}
