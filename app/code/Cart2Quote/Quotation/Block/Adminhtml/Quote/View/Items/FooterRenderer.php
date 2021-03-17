<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items;

/**
 * Class FooterRenderer
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items
 */
class FooterRenderer extends DefaultRenderer
{
    /**
     * Retrieve rendered column html content
     *
     * @param string $column the column key
     * @return string
     */
    public function getColumnFooterHtml($column)
    {
        $block = $this->getColumnRenderer($column, self::DEFAULT_TYPE);

        if ($block instanceof FooterInterface) {
            return $block->toFooterHtml();
        }
        return '&nbsp;';
    }
}
