<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Quote\Checkout;

/**
 * Class Guest
 *
 * @package Cart2Quote\Quotation\Controller\Quote\Checkout
 */
class Guest extends DefaultCheckout
{
    use \Cart2Quote\Features\Traits\Controller\Quote\Checkout\Guest {
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
