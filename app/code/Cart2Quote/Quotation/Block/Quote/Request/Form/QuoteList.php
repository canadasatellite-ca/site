<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Quote\Request\Form;

use Cart2Quote\Quotation\Block\Product\Listing\Form as ListingForm;
use \Cart2Quote\Quotation\Model\Strategy\StrategyInterface;

/**
 * Class QuoteList
 *
 * @package Cart2Quote\Quotation\Block\Quote\Request\Form
 */
class QuoteList extends ListingForm implements StrategyInterface
{
    /**
     * @var string
     */
    protected $_template = "Cart2Quote_Quotation::product/list/quote/request/quotelist/form.phtml";
}
