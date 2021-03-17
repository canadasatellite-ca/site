<?php

namespace MW\Onestepcheckout\Block\Checkout\Config;

class Css extends \Magento\Framework\View\Element\Template
{
	public function _construct()
	{
		$this->setTemplate('MW_Onestepcheckout::config/css.phtml');
	}
}
