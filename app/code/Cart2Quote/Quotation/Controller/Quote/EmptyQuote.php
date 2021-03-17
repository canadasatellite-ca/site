<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Quote;

/**
 * Class EmptyQuote
 *
 * @package Cart2Quote\Quotation\Controller\Quote
 */
class EmptyQuote extends \Cart2Quote\Quotation\Controller\Quote
{
    /**
     * Shopping cart display action
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        if ($this->isValidQuoteRequest()) {
            return $this->_redirect('quotation/quote');
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Quote'));
        return $resultPage;
    }
}
