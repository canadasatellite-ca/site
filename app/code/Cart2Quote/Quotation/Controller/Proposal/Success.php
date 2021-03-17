<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Proposal;

/**
 * Class Success
 *
 * @package Cart2Quote\Quotation\Controller\Proposal
 */
class Success extends \Cart2Quote\Quotation\Controller\Proposal\Index
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        return $this->pageFactory->create();
    }
}
