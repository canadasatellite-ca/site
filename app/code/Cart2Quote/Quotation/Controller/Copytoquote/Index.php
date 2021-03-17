<?php
/**
 * Cart2Quote
 */

namespace Cart2Quote\Quotation\Controller\Copytoquote;

/**
 * Class Index
 * @package Cart2Quote\Quotation\Controller\Copytoquote
 */
class Index extends \Cart2Quote\Quotation\Controller\Copytoquote
{
    /**
     * Shopping cart display action
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        //copy quote and use new quote id
        $quoteId = $this->_checkoutSession->getQuoteId();

        $quote = $this->_cloneQuote($quoteId);
        if (!$quote) {
            //set error message
            $this->messageManager->addError(__('The cart could not be copied to the quote.'));
        } else {
            $this->messageManager->addSuccess(__('The cart is successfully copied to the quote.'));
            $this->_quotationSession->setQuoteId($quote->getId());
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('quotation/quote/index');

        return $resultRedirect;
    }
}
