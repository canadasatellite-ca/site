<?php

namespace Interactivated\Quotecheckout\Block\Adminhtml\System\Config;

class Label1 extends \Magento\Config\Block\System\Config\Form\Field
{
	protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {    	    
		$html = $this->getLayout()
			->createBlock('Interactivated\Quotecheckout\Block\Adminhtml\System\Config\Label1info')
			->toHtml();

        return $html;
    }
}
