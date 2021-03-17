<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\MoveToQuote;

/**
 * Class Index
 *
 * @package Cart2Quote\Quotation\Controller\MoveToQuote
 */
class Index extends \Cart2Quote\Quotation\Controller\MoveToQuote
{
    /**
     * Shopping cart display action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $copiedQuote = $this->cloneQuote();
            $this->messageManager->addSuccessMessage(__('The cart is successfully moved to the quote.'));
            $this->checkoutSession->clearQuote();
            $this->checkoutSession->clearStorage();
            $this->quotationSession->setQuoteId($copiedQuote->getId());
            $resultRedirect->setPath('quotation/quote/index');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        }

        return $resultRedirect;
    }
}
