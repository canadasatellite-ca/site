<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Payment;

/**
 * Class QuotationQuote
 *
 * @package Cart2Quote\Quotation\Model\Payment
 */
class QuotationQuote extends \Magento\Payment\Model\Method\AbstractMethod
{

    use \Cart2Quote\Features\Traits\Model\Payment\QuotationQuote {
        isAvailable as private traitIsAvailable;
    }

    /**
     * @var string
     */
    protected $_code = "quotation_quote";
    /**
     * @var bool
     */
    protected $_isOffline = true;

    /**
     * Is available check for a given quote cart
     *
     * @param \Magento\Quote\Api\Data\CartInterface|null $quote
     * @return bool
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        return $this->traitIsAvailable($quote);
    }
}
