<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Magento\Backend\Block\Dashboard\Tab\Customers;

/**
 * Class NewestPlugin
 *
 * @package Cart2Quote\Quotation\Plugin\Magento\Backend\Block\Dashboard\Tab\Customers
 */
class NewestPlugin
{
    /**
     * Add quotes_count column to dashboard tab
     *
     * @param \Magento\Backend\Block\Dashboard\Tab\Customers\Newest $subject
     * @param bool $visible
     * @return array
     */
    public function beforeSetFilterVisibility(
        \Magento\Backend\Block\Dashboard\Grid $subject,
        $visible = true
    ) {
        $subject->addColumnAfter(
            'quotes_count',
            [
                'header' => __('Quotes'),
                'sortable' => false,
                'index' => 'quotes_count',
                'type' => 'number',
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ],
            'name'
        );

        return [$visible];
    }
}
