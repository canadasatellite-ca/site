<?php

namespace CanadaSatellite\Theme\Block\Customer\Account\Navigation;

class Sims extends \Magento\Customer\Block\Account\SortLink
{

    protected $_sims;

    function __construct(
        \CanadaSatellite\Theme\Block\Customer\Sim\ListSim $sims,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        array $data = [])
    {
        $this->_sims = $sims;
        parent::__construct($context, $defaultPath, $data);
    }

    protected function _toHtml()
    {
        if ($this->_sims->getSims() && count($this->_sims->getSims())) {
            return parent::_toHtml();
        }
        return '';
    }

}
