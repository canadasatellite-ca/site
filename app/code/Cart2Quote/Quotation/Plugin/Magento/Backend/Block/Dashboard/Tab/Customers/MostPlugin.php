<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Magento\Backend\Block\Dashboard\Tab\Customers;

/**
 * Class MostPlugin
 *
 * @package Cart2Quote\Quotation\Plugin\Magento\Backend\Block\Dashboard\Tab\Customers
 */
class MostPlugin extends NewestPlugin
{
    /**
     * Add quotes_count column to dashboard tab
     *
     * @param \Magento\Backend\Block\Dashboard\Tab\Customers\Most $subject
     * @param bool $visible
     * @return array
     */
    public function beforeSetFilterVisibility(
        \Magento\Backend\Block\Dashboard\Grid $subject,
        $visible = true
    ) {
        return parent::beforeSetFilterVisibility($subject, $visible);
    }
}
