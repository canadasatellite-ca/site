<?php

namespace Interactivated\Quotecheckout\Block\Checkout\Config;

class Css extends \Magento\Framework\View\Element\Template
{
	public function _construct()
	{
		$this->setTemplate('Interactivated_Quotecheckout::config/css.phtml');
	}
}
