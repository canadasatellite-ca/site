<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Quote\Checkout;

/**
 * Class Accept
 *
 * @package Cart2Quote\Quotation\Controller\Quote\Checkout
 */
class Accept extends DefaultCheckout
{
    use \Cart2Quote\Features\Traits\Controller\Quote\Checkout\Accept {
        execute as private traitExecute;
    }

    /**
     * Redirect to customer checkout page if the quotation customer is the same customer as logged in
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        return $this->traitExecute();
    }
}
