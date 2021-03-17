<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\Quote\Customer;

use Magento\Customer\Controller\RegistryConstants;

/**
 * Class Grid
 *
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote
 */
class Grid extends \Cart2Quote\Quotation\Controller\Adminhtml\Quote
{
    /** @const CURRENT_CUSTOMER_ID */
    const CURRENT_CUSTOMER_ID = 'current_customer_id';

    /**
     * Quote grid
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $customerId = $this->getRequest()->getParam('id');
        if ($customerId) {
            $this->_coreRegistry->register(self::CURRENT_CUSTOMER_ID, $customerId);
        }

        $resultLayout = $this->resultLayoutFactory->create();
        return $resultLayout;
    }
}
