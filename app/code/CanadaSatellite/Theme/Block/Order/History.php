<?php

namespace CanadaSatellite\Theme\Block\Order;

class History extends \Magento\Sales\Block\Order\History
{

    protected function _construct()
    {
        \Magento\Framework\View\Element\Template::_construct();
    }
}