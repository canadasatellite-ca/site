<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Search\Grid\Renderer;

/**
 * Adminhtml quote view product search grid product name column renderer
 */
class Product extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * Render product name to add Configure link
     *
     * @param   \Magento\Framework\DataObject $row
     * @return  string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $rendered = parent::render($row);
        $isConfigurable = $row->canConfigure();
        $style = $isConfigurable ? '' : 'disabled';
        if ($isConfigurable) {
            $prodAttributes = sprintf(
                'list_type = "product_to_add" product_id = %s',
                $row->getId()
            );
        } else {
            $prodAttributes = 'disabled="disabled"';
        }

        $javascript = sprintf(
            '<a href="javascript:void(0)" class="action-configure %s" %s>%s</a>',
            $style,
            $prodAttributes,
            __('Configure')
        );

        return $javascript . $rendered;
    }
}
