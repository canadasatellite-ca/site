<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Footer;

/**
 * Class OriginalSubtotal
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Footer
 */
class OriginalSubtotal extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\DefaultRenderer
{
    /**
     * Get original subtotal
     *
     * @return float
     */
    public function getOriginalSubtotal()
    {
        return $this->getQuote()->getOriginalSubtotal();
    }
}
