<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Quote\Request\Button;

/**
 * Class QuoteList
 *
 * @package Cart2Quote\Quotation\Block\Quote\Request\Button
 */
class QuoteList extends Button implements \Cart2Quote\Quotation\Model\Strategy\StrategyInterface
{
    /**
     * @var string
     */
    protected $_template = "Cart2Quote_Quotation::product/view/quote/request/quotelist/button.phtml";
}
