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
interface FooterInterface
{
    /**
     * Get the footer HTML
     *
     * @return string
     */
    public function toFooterHtml();
}
