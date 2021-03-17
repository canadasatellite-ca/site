<?php

namespace CanadaSatellite\Theme\Block\Quote;

class History extends \Cart2Quote\Quotation\Block\Quote\History
{

    protected $_template = 'Cart2Quote_Quotation::quote/history.phtml';

    protected function _construct()
    {
        \Magento\Framework\View\Element\Template::_construct();
    }
}