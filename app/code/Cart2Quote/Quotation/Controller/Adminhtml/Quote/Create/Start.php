<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\Quote\Create;

/**
 * Class Start
 *
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote\Create
 */
class Start extends \Magento\Sales\Controller\Adminhtml\Order\Create\Start
{
    /**
     * Start order create action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $this->_getSession()->clearStorage();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('quotation/*', ['customer_id' => $this->getRequest()->getParam('customer_id')]);
    }
}
