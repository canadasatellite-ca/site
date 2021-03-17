<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Payment\Info;

/**
 * Class Instructions
 *
 * @package Cart2Quote\Quotation\Block\Info
 */
class Instructions extends \Magento\Payment\Block\Info\Instructions
{
    /**
     * Render as PDF
     *
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('Cart2Quote_Quotation::payment/info/pdf/instructions.phtml');
        return $this->toHtml();
    }

    /**
     * Check if instructions enable setting is set
     *
     * @return bool
     */
    public function isInstructionsEnabled()
    {
        return (bool)$this->_scopeConfig->getValue(
            'cart2quote_pdf/quote/pdf_enable_instructions',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
