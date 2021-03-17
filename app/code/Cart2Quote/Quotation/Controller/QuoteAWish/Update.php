<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\QuoteAWish;

/**
 * Class Update
 *
 * @package Cart2Quote\Quotation\Controller\QuoteAWish
 */
class Update extends \Cart2Quote\Quotation\Controller\QuoteAWish
{
    /**
     * Quotation Cart display action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $this->quoteAWish();
            $resultRedirect->setPath('quotation/quote');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        }

        return $resultRedirect;
    }
}
