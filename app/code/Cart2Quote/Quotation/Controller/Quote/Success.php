<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Quote;

/**
 * Class Success
 *
 * @package Cart2Quote\Quotation\Controller\Quote
 */
class Success extends \Cart2Quote\Quotation\Controller\Quote
{
    /**
     * Constant 'created by customer' reference
     */
    const CUSTOMER = "Customer";

    /**
     * Order success action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $quoteId = $this->getRequest()->getParam('id', false);
        if (!$quoteId) {
            return $this->_redirect('*/*/index');
        }

        $quote = $this->_quoteFactory->create()->load($quoteId);
        $quote->setQuotationCreatedBy(self::CUSTOMER);
        $quote->setIsActive(false);
        $quote->save();

        $session = $this->getQuotationSession();
        $session->fullSessionClear();
        $session->updateLastQuote($quote);

        $resultPage = $this->resultPageFactory->create();
        $this->_eventManager->dispatch(
            'quotation_quote_controller_success_action',
            ['quote_ids' => [$session->getLastQuoteId()]]
        );
        return $resultPage;
    }
}
