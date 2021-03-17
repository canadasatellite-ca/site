<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\Quote;

/**
 * Class Index
 *
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote
 */
class Index extends \Cart2Quote\Quotation\Controller\Adminhtml\Quote
{
    /**
     * Quotes grid
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        if ($results = parent::execute()) {
            return $results;
        }

        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Quotes'));

        return $resultPage;
    }
}
