<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View;

/**
 * Class CustomProduct
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View
 */
class CustomProduct extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\AbstractView
{
    /**
     * Get buttons HTML
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getButtonsHtml()
    {
        $addButtonData = [
            'label' => __('Add Custom Product'),
            'onclick' => 'quote.createCustomProduct()',
            'class' => 'action-add action-secondary',
        ];

        return $this->getLayout()->createBlock(\Magento\Backend\Block\Widget\Button::class)
            ->setData($addButtonData)
            ->toHtml();
    }
}
