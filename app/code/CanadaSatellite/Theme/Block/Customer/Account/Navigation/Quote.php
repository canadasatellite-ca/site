<?php

namespace CanadaSatellite\Theme\Block\Customer\Account\Navigation;

class Quote extends \Magento\Customer\Block\Account\SortLink
{

    protected $_quotes;

    function __construct(
        \Cart2Quote\Quotation\Block\Quote\History $quotes,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        array $data = [])
    {
        $this->_quotes = $quotes;
        parent::__construct($context, $defaultPath, $data);
    }

    protected function _toHtml()
    {
        if ($this->_quotes->getQuotes() && count($this->_quotes->getQuotes())) {
            return parent::_toHtml();
        }
        return '';
    }
}
