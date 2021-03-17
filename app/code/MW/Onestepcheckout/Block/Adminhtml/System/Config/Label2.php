<?php

namespace MW\Onestepcheckout\Block\Adminhtml\System\Config;

class Label2 extends \Magento\Config\Block\System\Config\Form\Field
{
	protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {    	    
		$html = $this->getLayout()
			->createBlock('MW\Onestepcheckout\Block\Adminhtml\System\Config\Label2info')
			->toHtml();

        return $html;
    }
}
