<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\Quote;

/**
 * Class Grid
 *
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote
 */
class Grid extends \Cart2Quote\Quotation\Controller\Adminhtml\Quote
{
    /**
     * Quote grid
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $resultLayout = $this->resultLayoutFactory->create();
        return $resultLayout;
    }
}
