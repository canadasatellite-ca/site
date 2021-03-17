<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Quote\Checkout;

/**
 * Class Customer
 *
 * @package Cart2Quote\Quotation\Controller\Quote\Checkout
 */
class Customer extends DefaultCheckout
{
    use \Cart2Quote\Features\Traits\Controller\Quote\Checkout\Customer {
        execute as private traitExecute;
    }

    /**
     * Redirect to customer dashboard or checkout page
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        return $this->traitExecute();
    }
}
