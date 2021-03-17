<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote;

/**
 * Adminhtml quotations quotes grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid
{
    /**
     * Apply sorting and filtering to collection
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareCollection()
    {
        if ($this->getCollection()) {
            foreach ($this->getDefaultFilter() as $field => $value) {
                $this->getCollection()->addFieldToFilter($field, $value);
            }
        }
        return parent::_prepareCollection();
    }
}
