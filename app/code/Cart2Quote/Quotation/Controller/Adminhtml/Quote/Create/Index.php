<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\Quote\Create;

/**
 * Class Index
 *
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote\Create
 */
class Index extends \Magento\Sales\Controller\Adminhtml\Order\Create\Index
{
    /**
     * Index page (controller entypoint)
     *
     * @return \Magento\Backend\Model\View\Result\Page|void
     */
    public function execute()
    {
        $this->_initSession();

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Cart2Quote_Quotation::quotation_quote');
        $resultPage->getConfig()->getTitle()->prepend(__('Quotes'));
        $resultPage->getConfig()->getTitle()->prepend(__('New Quote'));
        return $resultPage;
    }
}
