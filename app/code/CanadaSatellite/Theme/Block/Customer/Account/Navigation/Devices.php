<?php

namespace CanadaSatellite\Theme\Block\Customer\Account\Navigation;

class Devices extends \Magento\Customer\Block\Account\SortLink
{

    protected $_devices;

    function __construct(
        \CanadaSatellite\Theme\Block\Customer\Device\ListDevice $devices,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        array $data = [])
    {
        $this->_devices = $devices;
        parent::__construct($context, $defaultPath, $data);
    }

    protected function _toHtml()
    {
        if ($this->_devices->getDevices() && count($this->_devices->getDevices())) {
            return parent::_toHtml();
        }
        return '';
    }

}
