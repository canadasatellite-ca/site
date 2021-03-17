<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\Quote\Update;

/**
 * Class Address
 *
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote\Update
 */
class Address extends \Magento\Sales\Controller\Adminhtml\Order\Create\Start
{
    /**
     * Execute
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $this->_getSession()->setCustomerId($this->getRequest()->getParam('customer_id'));
        $this->_getSession()->setQuoteId($this->getRequest()->getParam('quote_id'));
        $this->_getSession()->setQuotationQuoteId($this->getRequest()->getParam('quote_id'));
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('quotation/quote_create');
    }
}
