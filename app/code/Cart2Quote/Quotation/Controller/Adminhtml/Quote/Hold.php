<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\Quote;

/**
 * Class Hold
 *
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote
 */
class Hold extends \Cart2Quote\Quotation\Controller\Adminhtml\Quote
{
    /**
     * View quote detail
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $quote = $this->_initQuote();

        if ($quote) {
            if ($quote->canHold()) {
                $quote->setState(\Cart2Quote\Quotation\Model\Quote\Status::STATE_HOLDED)
                    ->setStatus(\Cart2Quote\Quotation\Model\Quote\Status::STATUS_HOLDED)->save();
            }
        }

        return $this->resultRedirectFactory->create()->setPath('quotation/quote/view', ['quote_id' => $quote->getId()]);
    }

    /**
     * ACL check
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Cart2Quote_Quotation::hold');
    }
}
